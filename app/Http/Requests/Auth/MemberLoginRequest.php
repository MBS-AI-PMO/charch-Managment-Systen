<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class MemberLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
    }

    public function authenticate(): void
    {
        $this->ensureNotLocked();
        $this->ensureIsNotRateLimited();

        if (! Auth::guard('web')->attempt(
            ['email' => $this->email, 'password' => $this->password],
            $this->boolean('remember')
        )) {
            RateLimiter::hit($this->throttleKey(), 60);

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Reject admin accounts from member portal — they must use /admin/login.
        $user = Auth::guard('web')->user();
        if ($user && $user->is_admin) {
            Auth::guard('web')->logout();
            $this->session()->invalidate();
            $this->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Please use the admin login page.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    protected function ensureNotLocked(): void
    {
        if (Cache::get('login-locked:'.strtolower((string) $this->email))) {
            throw ValidationException::withMessages([
                'email' => 'This account is temporarily locked due to repeated failed attempts. Try again in 15 minutes.',
            ]);
        }
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        throw ValidationException::withMessages([
            'email' => 'Too many login attempts. Try again in '.RateLimiter::availableIn($this->throttleKey()).' seconds.',
        ]);
    }

    public function throttleKey(): string
    {
        return strtolower((string) $this->email).'|'.$this->ip().'|member-login';
    }
}
