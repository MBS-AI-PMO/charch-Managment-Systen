<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class RateLimitServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RateLimiter::for('admin-login', function (Request $request) {
            $key = strtolower((string) $request->input('email')).'|'.$request->ip();

            return Limit::perMinute(5)->by($key);
        });

        RateLimiter::for('member-login', function (Request $request) {
            $key = strtolower((string) $request->input('email')).'|'.$request->ip();

            return Limit::perMinute(5)->by($key);
        });

        RateLimiter::for('password-reset', fn (Request $request) => Limit::perHour(3)->by($request->ip()));

        RateLimiter::for('contact-form', fn (Request $request) => Limit::perHour(5)->by($request->ip()));

        RateLimiter::for('member-register', fn (Request $request) => Limit::perHour(3)->by($request->ip()));

        // M4 Prayer Requests
        RateLimiter::for('prayer-submit', fn (Request $request) => Limit::perHour(10)->by($request->user()?->id ?? $request->ip()));
        RateLimiter::for('feed-react', fn (Request $request) => Limit::perMinute(60)->by($request->user()?->id ?? $request->ip()));

        // M5 Knock for Help
        RateLimiter::for('care-submit', fn (Request $request) => Limit::perHour(3)->by($request->user()?->id ?? $request->ip()));

        // Phase 3 M2 — Event RSVPs
        RateLimiter::for('event-rsvp', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        // Phase 3 M3 — Self check-in
        RateLimiter::for('checkin-submit', function (Request $request) {
            return Limit::perMinutes(5, 10)->by($request->ip());
        });
    }
}
