<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.member.register');
    }

    public function store(RegisterRequest $request)
    {
        // Honeypot — silently drop bot submissions but mimic a normal redirect.
        if ($request->filled('website')) {
            return redirect()->route('verification.notice');
        }

        $user = User::create([
            'name' => $request->string('name'),
            'email' => $request->string('email'),
            'password' => $request->string('password'),
            'is_admin' => false,
        ]);

        event(new Registered($user));
        Auth::guard('web')->login($user);

        return redirect()->route('verification.notice');
    }
}
