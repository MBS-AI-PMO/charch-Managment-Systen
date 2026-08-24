<?php

namespace App\Providers;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Site Admins always pass policy checks. Returning null lets the
        // normal policy method run for non-admins.
        Gate::before(function ($user) {
            return method_exists($user, 'hasRole') && $user->hasRole('Site Admin')
                ? true
                : null;
        });

        $this->applyMailSettingsFromDatabase();
        $this->applyGlobalReplyTo();

        // M10-T04: Force HTTPS and secure cookies in production.
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
            config([
                'session.secure' => true,
                'session.same_site' => 'lax',
            ]);
        }

        // M10-T05: Strong password defaults (min 12 chars, mixed case, digits, symbols).
        Password::defaults(fn () => Password::min(12)->mixedCase()->numbers()->symbols());

        // M10-T03: Auth event listeners for lockout + last_login_at tracking.
        Event::listen(\Illuminate\Auth\Events\Failed::class, \App\Listeners\HandleFailedLogin::class);
        Event::listen(\Illuminate\Auth\Events\Login::class, \App\Listeners\HandleSuccessfulLogin::class);

        // Phase 2 M2-T06: Observer maintains pray_count denormalized counter.
        \App\Models\PrayerRequestPray::observe(\App\Observers\PrayerRequestPrayObserver::class);

        // Phase 3 M5-T01: Event attendance management gate.
        Gate::define('manage-event-attendance', [\App\Policies\EventAttendancePolicy::class, 'manage']);
    }

    /**
     * Do not override SMTP from from DB — .env is source of truth for delivery.
     */
    protected function applyMailSettingsFromDatabase(): void
    {
        // Intentionally empty: admin "mail.from_address" was forcing aamir@ while
        // Gmail SMTP delivers, which made From look wrong / get rewritten.
    }

    /**
     * Ensure every outgoing message has Reply-To = church mailbox when configured.
     */
    protected function applyGlobalReplyTo(): void
    {
        Event::listen(MessageSending::class, function (MessageSending $event) {
            $reply = config('mail.reply_to.address');
            if (! filled($reply)) {
                return;
            }
            if (count($event->message->getReplyTo()) > 0) {
                return;
            }
            $event->message->replyTo($reply, (string) config('mail.reply_to.name'));
        });
    }
}
