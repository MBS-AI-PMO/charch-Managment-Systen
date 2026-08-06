<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class ForcePasswordController extends Controller
{
    // ───────── Admin ─────────

    public function showAdmin()
    {
        return view('auth.admin.force-password');
    }

    public function updateAdmin(Request $request)
    {
        return $this->handleUpdate($request, 'admin', route('admin.dashboard'));
    }

    // ───────── Member ─────────

    public function show()
    {
        return view('auth.member.force-password');
    }

    public function update(Request $request)
    {
        return $this->handleUpdate($request, 'web', route('member.dashboard'));
    }

    private function handleUpdate(Request $request, string $guard, string $target)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', PasswordRule::defaults(), 'confirmed', 'different:current_password'],
        ]);

        $user = auth($guard)->user();

        if (! $user || ! Hash::check($request->input('current_password'), $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => __('The provided password does not match your current password.'),
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($request->input('password')),
            'password_change_required' => false,
        ])->save();

        return redirect($target)->with('status', 'Password updated. Welcome back.');
    }
}
