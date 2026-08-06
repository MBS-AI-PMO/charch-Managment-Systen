<?php

namespace App\Jobs;

use App\Mail\EventReminderMail;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEventReminderEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function handle(): void
    {
        if (! settings('reminders.event_24h_enabled', true)) return;

        $events = Event::query()
            ->whereNull('reminded_at')
            ->whereBetween('starts_at', [now()->addHours(23), now()->addHours(25)])
            ->get();

        foreach ($events as $event) {
            $count = 0;
            $event->rsvps()
                ->where('status', 'going')
                ->with('user')
                ->chunk(200, function ($chunk) use ($event, &$count) {
                    foreach ($chunk as $rsvp) {
                        $user = $rsvp->user;
                        if (! $user || ! $user->email_verified_at) continue;
                        if (! $user->email_reminder_event_24h) continue;
                        Mail::to($user->email)->queue(new EventReminderMail($event, $rsvp, $user));
                        $count++;
                    }
                });

            $event->update(['reminded_at' => now()]);
            \App\Models\ActivityLog::create([
                'user_id' => null,
                'subject_type' => Event::class,
                'subject_id' => $event->id,
                'action' => 'event_reminders_sent',
                'changes' => ['count' => $count, 'title' => $event->title],
                'ip' => null,
            ]);
        }
    }
}
