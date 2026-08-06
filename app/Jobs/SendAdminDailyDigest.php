<?php

namespace App\Jobs;

use App\Mail\AdminDailyDigestMail;
use App\Models\Event;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAdminDailyDigest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function handle(): void
    {
        if (! settings('reminders.admin_daily_digest_enabled', false)) return;

        $payload = [
            'new_members' => User::whereDate('created_at', today()->subDay())->where('is_admin', false)->get(),
            'new_prayer' => class_exists(\App\Models\PrayerRequest::class)
                ? \App\Models\PrayerRequest::whereDate('created_at', today()->subDay())->get()
                : collect(),
            'open_care' => class_exists(\App\Models\CareRequest::class)
                ? \App\Models\CareRequest::where('status', 'open')->get()
                : collect(),
            'events_today' => Event::whereDate('starts_at', today())
                ->where('attendance_open', true)->get(),
        ];

        $total = 0;
        User::where('is_admin', true)
            ->where('email_admin_daily_digest', true)
            ->each(function ($admin) use ($payload, &$total) {
                Mail::to($admin->email)->queue(new AdminDailyDigestMail($admin, $payload));
                $total++;
            });

        \App\Models\ActivityLog::create([
            'user_id' => null,
            'subject_type' => User::class,
            'subject_id' => 0,
            'action' => 'admin_daily_digest_sent',
            'changes' => ['count' => $total],
            'ip' => null,
        ]);
    }
}
