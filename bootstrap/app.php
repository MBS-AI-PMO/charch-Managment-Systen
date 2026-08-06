<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'admin' => \App\Http\Middleware\EnsureIsAdmin::class,
            'password.change' => \App\Http\Middleware\RequirePasswordChange::class,
        ]);

        // Make brand tokens (primary/secondary colour as "R G B" triples,
        // brand name and tagline) available to every Blade view rendered for
        // a web request. Layouts emit them as inline :root CSS variables so
        // settings can override the Tailwind defaults from app.css. Also
        // injects security response headers (M10) and the force-password-
        // change guard which redirects flagged users away from the rest of
        // the site until they choose a new password.
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\HydrateBrandTokens::class,
            \App\Http\Middleware\RequirePasswordChange::class,
        ]);

        // Send unauthenticated users to the correct login screen based on the
        // guard they tripped. Requests whose path starts with /admin go to the
        // admin login; everything else goes to the member login.
        $middleware->redirectGuestsTo(function (Illuminate\Http\Request $request) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }

            return route('login');
        });
    })
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule): void {
        $schedule->job(new \App\Jobs\SendEventReminderEmails)
            ->hourly()->onOneServer()->withoutOverlapping();

        $schedule->job(new \App\Jobs\SendWeeklyMemberDigest)
            ->weeklyOn(
                weekday(settings('reminders.weekly_digest_day', 'Saturday')),
                sprintf('%02d:00', (int) settings('reminders.weekly_digest_hour', 18))
            )
            ->onOneServer()->withoutOverlapping();

        $schedule->job(new \App\Jobs\SendAdminDailyDigest)
            ->dailyAt('08:00')->onOneServer()->withoutOverlapping();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
