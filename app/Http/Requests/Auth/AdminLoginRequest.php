<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AdminLoginRequest extends FormRequest
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

        if (! Auth::guard('admin')->attempt(
            ['email' => $this->email, 'password' => $this->password, 'is_admin' => true],
            $this->boolean('remember')
        )) {
            RateLimiter::hit($this->throttleKey(), 60);

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
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
        return strtolower((string) $this->email).'|'.$this->ip().'|admin-login';
    }
}
