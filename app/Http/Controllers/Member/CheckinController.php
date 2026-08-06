<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAttendance;
use Illuminate\Http\Request;

class CheckinController extends Controller
{
    public function show()
    {
        return view('member.checkin.show');
    }

    public function submit(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'size:4'],
        ]);

        $event = Event::query()
            ->where('checkin_code', $data['code'])
            ->where('attendance_open', true)
            ->whereBetween('starts_at', [now()->subHours(4), now()->addHours(12)])
            ->first();

        if (! $event) {
            return back()->withErrors(['code' => 'That code doesn\'t match an event happening now. Double-check with an organizer.']);
        }

        $existing = EventAttendance::where('event_id', $event->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existing) {
            return redirect()
                ->route('member.checkin.thanks', $existing)
                ->with('status', 'You\'re already checked in. ✓');
        }

        $attendance = EventAttendance::create([
            'event_id' => $event->id,
            'user_id' => $request->user()->id,
            'checked_in_at' => now(),
            'method' => 'self',
        ]);

        return redirect()->route('member.checkin.thanks', $attendance);
    }

    public function thanks(EventAttendance $attendance, Request $request)
    {
        abort_unless($attendance->user_id === $request->user()->id, 403);
        $attendance->load('event');
        return view('member.checkin.thanks', compact('attendance'));
    }
}
