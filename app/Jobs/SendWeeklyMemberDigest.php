<?php

namespace App\Jobs;

use App\Mail\WeeklyDigestMail;
use App\Models\Event;
use App\Models\FeedPost;
use App\Models\PrayerRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendWeeklyMemberDigest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function handle(): void
    {
        if (! settings('reminders.weekly_digest_enabled', true)) return;

        $events = Event::where('starts_at', '>=', now())
            ->where('starts_at', '<=', now()->addDays(7))
            ->orderBy('starts_at')
            ->limit(5)->get();

        $prayer = class_exists(PrayerRequest::class)
            ? PrayerRequest::latest()->limit(3)->get()
            : collect();

        $feed = class_exists(FeedPost::class)
            ? FeedPost::latest()->limit(3)->get()
            : collect();

        $total = 0;
        User::query()
            ->where('is_admin', false)
            ->where('email_weekly_digest', true)
            ->whereNotNull('email_verified_at')
            ->chunk(200, function ($chunk) use ($events, $prayer, $feed, &$total) {
                foreach ($chunk as $user) {
                    $payload = [
                        'events' => $events,
                        'prayer' => $prayer,
                        'feed' => $feed,
                    ];
                    if ($events->isEmpty() && $prayer->isEmpty() && $feed->isEmpty()) continue;
                    Mail::to($user->email)->queue(new WeeklyDigestMail($user, $payload));
                    $total++;
                }
            });

        \App\Models\ActivityLog::create([
            'user_id' => null,
            'subject_type' => User::class,
            'subject_id' => 0,
            'action' => 'weekly_digest_sent',
            'changes' => ['count' => $total],
            'ip' => null,
        ]);
    }
}
