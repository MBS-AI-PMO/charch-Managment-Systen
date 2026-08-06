<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class PasswordResetController extends Controller
{
    // ───────── Admin ─────────

    public function showLinkRequestAdmin()
    {
        return view('auth.admin.forgot');
    }

    public function sendResetLinkAdmin(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker('admins')->sendResetLink(
            ['email' => $request->email, 'is_admin' => true]
        );

        return $status === Password::ResetLinkSent
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetAdmin(Request $request, string $token)
    {
        return view('auth.admin.reset', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetAdmin(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'string', PasswordRule::defaults(), 'confirmed'],
        ]);

        $status = Password::broker('admins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token') + ['is_admin' => true],
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PasswordReset
            ? redirect()->route('admin.login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    // ───────── Member ─────────

    public function showLinkRequestMember()
    {
        return view('auth.member.forgot');
    }

    public function sendResetLinkMember(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = strtolower(trim($request->email));
        $exists = \App\Models\User::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->exists();

        if (! $exists) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'No account found with this email. Please register first, or use the email you signed up with.',
                ]);
        }

        $status = Password::broker('users')->sendResetLink(
            ['email' => $request->email]
        );

        return $status === Password::ResetLinkSent
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetMember(Request $request, string $token)
    {
        return view('auth.member.reset', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetMember(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'string', PasswordRule::defaults(), 'confirmed'],
        ]);

        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PasswordReset
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
