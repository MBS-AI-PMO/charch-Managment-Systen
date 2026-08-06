<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\User;
use Illuminate\Support\Facades\URL;

class EventReminderMail extends BrandedMail
{
    public function __construct(public Event $event, public EventRsvp $rsvp, public User $user) {}

    protected function subjectLine(): string
    {
        return 'Reminder: '.$this->event->title.' tomorrow';
    }

    protected function viewName(): string
    {
        return 'mail.event_reminder';
    }

    protected function viewData(): array
    {
        return [
            'event' => $this->event,
            'rsvp' => $this->rsvp,
            'user' => $this->user,
            'cancelUrl' => URL::temporarySignedRoute(
                'site.events.cancel-rsvp.public',
                now()->addDays(7),
                ['event' => $this->event->slug, 'user' => $this->user->id]
            ),
        ];
    }
}
