<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventAttendancePolicy
{
    public function manage(User $user, Event $event): bool
    {
        if ($user->hasRole('Site Admin')) {
            return true;
        }

        return $user->hasRole('Event Organizer') && $event->organizer_id === $user->id;
    }
}
