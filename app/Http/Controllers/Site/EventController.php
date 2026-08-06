<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $page = Page::where('slug', 'events')->published()->first();

        $upcoming = Event::published()
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->paginate(12, ['*'], 'upcoming_page')
            ->withQueryString();

        $past = Event::published()
            ->where('starts_at', '<', now())
            ->orderByDesc('starts_at')
            ->paginate(12, ['*'], 'past_page')
            ->withQueryString();

        return view('site.events.index', compact('page', 'upcoming', 'past'));
    }

    public function show(Event $event)
    {
        abort_unless($event->is_published, 404);

        $myRsvp = auth()->check()
            ? \App\Models\EventRsvp::where('event_id', $event->id)
                ->where('user_id', auth()->id())
                ->first()
            : null;

        $goingCount = \App\Models\EventRsvp::where('event_id', $event->id)
            ->where('status', 'going')->count();

        $goingPreview = \App\Models\EventRsvp::with('user:id,name')
            ->where('event_id', $event->id)
            ->where('status', 'going')
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn ($r) => explode(' ', $r->user->name ?? 'Friend')[0])
            ->all();

        return view('site.events.show', compact('event', 'myRsvp', 'goingCount', 'goingPreview'));
    }

    public function ics(Event $event)
    {
        abort_unless($event->is_published, 404);

        $fmt = fn ($dt) => $dt ? $dt->copy()->utc()->format('Ymd\THis\Z') : '';
        $stamp = now()->utc()->format('Ymd\THis\Z');
        $end = $event->ends_at ?: $event->starts_at->copy()->addHour();

        $escape = function (?string $s): string {
            return str_replace(
                ["\\", "\n", ",", ";"],
                ["\\\\", "\\n", "\\,", "\\;"],
                strip_tags((string) $s)
            );
        };

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Assemblies of God Rawalpindi//Church CMS//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:event-'.$event->id.'@'.parse_url(config('app.url'), PHP_URL_HOST ?? 'church.local'),
            'DTSTAMP:'.$stamp,
            'DTSTART:'.$fmt($event->starts_at),
            'DTEND:'.$fmt($end),
            'SUMMARY:'.$escape($event->title),
            'DESCRIPTION:'.$escape($event->description),
            'LOCATION:'.$escape($event->location),
            'URL:'.route('site.events.show', $event),
            'END:VEVENT',
            'END:VCALENDAR',
        ];

        $body = implode("\r\n", $lines)."\r\n";

        return Response::make($body, 200, [
            'Content-Type' => 'text/calendar; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="event-'.$event->slug.'.ics"',
        ]);
    }
}
