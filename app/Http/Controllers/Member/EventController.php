<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRsvp;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function rsvp(Request $request, Event $event)
    {
        $data = $request->validate([
            'guest_count' => ['nullable', 'integer', 'min:0', 'max:5'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $rsvp = EventRsvp::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => $request->user()->id],
            [
                'guest_count' => $data['guest_count'] ?? 0,
                'note' => $data['note'] ?? null,
                'status' => 'going',
            ],
        );

        return redirect()
            ->route('site.events.show', $event)
            ->with('status', 'You\'re going to '.$event->title.'.');
    }

    public function cancelRsvp(Request $request, Event $event)
    {
        EventRsvp::where('event_id', $event->id)
            ->where('user_id', $request->user()->id)
            ->update(['status' => 'cancelled']);

        return redirect()
            ->route('site.events.show', $event)
            ->with('status', 'RSVP cancelled.');
    }
}
