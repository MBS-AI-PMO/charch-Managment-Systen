<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Cache;

class HandleFailedLogin
{
    public function handle(Failed $event): void
    {
        $email = $event->credentials['email'] ?? null;
        if (! $email) {
            return;
        }

        $key = 'login-fail:'.strtolower($email);
        $count = (int) Cache::get($key, 0) + 1;
        Cache::put($key, $count, now()->addMinutes(15));

        if ($count >= 5) {
            Cache::put('login-locked:'.strtolower($email), true, now()->addMinutes(15));
        }
    }
}
