<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Cache;

class HandleSuccessfulLogin
{
    public function handle(Login $event): void
    {
        $email = strtolower((string) ($event->user->email ?? ''));

        if ($email !== '') {
            Cache::forget('login-fail:'.$email);
            Cache::forget('login-locked:'.$email);
        }

        // Track last_login_at — see M10-T06/T07.
        if (method_exists($event->user, 'forceFill')) {
            $event->user->forceFill(['last_login_at' => now()])->save();
        }
    }
}
