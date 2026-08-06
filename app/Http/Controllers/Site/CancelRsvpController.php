<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\User;
use Illuminate\Http\Request;

class CancelRsvpController extends Controller
{
    /**
     * Public, signature-protected cancel endpoint reached from reminder emails.
     * Logged-out members can still cancel without consuming the signed token
     * via an auth redirect.
     */
    public function __invoke(Request $request, Event $event, int $userId)
    {
        abort_unless($request->hasValidSignature(), 403);

        $rsvp = EventRsvp::where('event_id', $event->id)
            ->where('user_id', $userId)
            ->first();

        abort_if($rsvp === null, 404);

        if ($rsvp->status !== 'cancelled') {
            $rsvp->update(['status' => 'cancelled']);
        }

        $user = User::find($userId);
        $firstName = $user
            ? explode(' ', trim($user->name ?? 'friend'))[0]
            : 'friend';

        return view('site.events.cancel_confirmed', [
            'event' => $event,
            'firstName' => $firstName,
        ]);
    }
}
