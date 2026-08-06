<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\EventRequest;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $req)
    {
        $tab = $req->query('tab', 'upcoming');
        $q = Event::query();
        if ($tab === 'past') {
            $q->where('starts_at', '<', now())->orderByDesc('starts_at');
        } else {
            $q->where('starts_at', '>=', now())->orderBy('starts_at');
        }
        $events = $q->paginate(20)->withQueryString();
        $upcomingCount = Event::where('starts_at', '>=', now())->count();
        $pastCount = Event::where('starts_at', '<', now())->count();
        return view('admin.events.index', compact('events', 'tab', 'upcomingCount', 'pastCount'));
    }

    public function create()
    {
        return view('admin.events.edit', ['event' => new Event()]);
    }

    public function store(EventRequest $req)
    {
        $data = $req->validated();
        if ($req->hasFile('cover_image_file')) {
            $data['cover_image_path'] = $req->file('cover_image_file')->store('uploads/events', 'public');
        }
        unset($data['cover_image_file']);

        $event = Event::create($data);
        return redirect()->route('admin.events.edit', $event)->with('success', 'Event created.');
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(EventRequest $req, Event $event)
    {
        $data = $req->validated();
        if ($req->hasFile('cover_image_file')) {
            $data['cover_image_path'] = $req->file('cover_image_file')->store('uploads/events', 'public');
        }
        unset($data['cover_image_file']);

        $event->update($data);
        return redirect()->route('admin.events.edit', $event)->with('success', 'Event updated.');
    }

    public function show(Event $event)
    {
        return redirect()->route('admin.events.edit', $event);
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Event deleted.');
    }
}
