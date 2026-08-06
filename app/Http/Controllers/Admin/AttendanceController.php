<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
    public function index()
    {
        $events = Event::query()
            ->where(function ($q) {
                $q->where('attendance_open', true)
                  ->orWhereDate('starts_at', '>=', now()->subDays(30));
            })
            ->orderByDesc('starts_at')
            ->paginate(20);

        return view('admin.attendance.index', compact('events'));
    }

    public function show(Event $event)
    {
        Gate::authorize('manage-event-attendance', $event);

        $rsvps = $event->rsvps()->with('user')->where('status', 'going')->get();
        $attendances = $event->attendances()->with(['user', 'recorder'])->get();

        $rsvpCount = $rsvps->count();
        $checkedInCount = $attendances->where('user_id', '!=', null)->count();
        $walkInCount = $attendances->whereNull('user_id')->count();

        return view('admin.attendance.show', compact(
            'event', 'rsvps', 'attendances', 'rsvpCount', 'checkedInCount', 'walkInCount'
        ));
    }

    public function regenerateCode(Event $event)
    {
        Gate::authorize('manage-event-attendance', $event);
        $event->update(['checkin_code' => str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT)]);

        return back()->with('status', 'Check-in code regenerated.');
    }

    public function toggleOpen(Request $request, Event $event)
    {
        Gate::authorize('manage-event-attendance', $event);
        $event->update(['attendance_open' => (bool) $request->boolean('open')]);

        if ($event->attendance_open && ! $event->checkin_code) {
            $event->update(['checkin_code' => str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT)]);
        }

        return back()->with('status', $event->attendance_open ? 'Attendance is open.' : 'Attendance closed.');
    }

    public function store(Request $request, Event $event)
    {
        Gate::authorize('manage-event-attendance', $event);

        $data = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'guest_name' => ['nullable', 'string', 'max:120'],
            'guest_count' => ['nullable', 'integer', 'min:0', 'max:5'],
        ]);

        if (! empty($data['user_id'])) {
            $existing = EventAttendance::where('event_id', $event->id)
                ->where('user_id', $data['user_id'])
                ->first();
            if ($existing) {
                return back()->with('status', 'Already marked.');
            }
        }

        EventAttendance::create([
            'event_id' => $event->id,
            'user_id' => $data['user_id'] ?? null,
            'guest_name' => $data['guest_name'] ?? null,
            'guest_count' => $data['guest_count'] ?? 0,
            'checked_in_at' => now(),
            'method' => 'organizer',
            'recorded_by' => $request->user()->id,
        ]);

        return back()->with('status', 'Attendance recorded.');
    }

    public function destroy(Event $event, EventAttendance $attendance)
    {
        Gate::authorize('manage-event-attendance', $event);
        abort_unless($attendance->event_id === $event->id, 404);
        $attendance->delete();

        return back()->with('status', 'Attendance removed.');
    }

    public function export(Event $event): StreamedResponse
    {
        Gate::authorize('manage-event-attendance', $event);

        $filename = 'attendance-'.$event->slug.'-'.now()->toDateString().'.csv';

        return response()->streamDownload(function () use ($event) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Name', 'RSVP Guests', 'Checked In', 'Method', 'Checked In At', 'Note']);

            $rsvps = $event->rsvps()->with('user')->where('status', 'going')->get();
            $attendances = $event->attendances()->with('user')->get()->keyBy('user_id');

            foreach ($rsvps as $r) {
                $att = $attendances->get($r->user_id);
                fputcsv($out, [
                    self::csvSafe($r->user->name ?? ''),
                    $r->guest_count,
                    $att ? 'Yes' : 'No',
                    $att?->method ?? '',
                    $att?->checked_in_at?->toDateTimeString() ?? '',
                    self::csvSafe($r->note ?? ''),
                ]);
            }

            foreach ($event->attendances()->whereNull('user_id')->get() as $walk) {
                fputcsv($out, [
                    self::csvSafe($walk->guest_name ?: 'Guest'),
                    $walk->guest_count,
                    'Yes',
                    $walk->method,
                    $walk->checked_in_at?->toDateTimeString() ?? '',
                    '',
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private static function csvSafe(string $value): string
    {
        if ($value !== '' && in_array($value[0], ['=', '+', '-', '@'], true)) {
            return "'".$value;
        }

        return $value;
    }
}
