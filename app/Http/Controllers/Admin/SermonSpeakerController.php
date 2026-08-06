<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SermonSpeakerRequest;
use App\Models\SermonSpeaker;

class SermonSpeakerController extends Controller
{
    public function index()
    {
        $speakers = SermonSpeaker::withCount('sermons')->orderBy('name')->paginate(30);
        return view('admin.sermons.speakers.index', compact('speakers'));
    }

    public function create()
    {
        return view('admin.sermons.speakers.edit', ['speaker' => new SermonSpeaker()]);
    }

    public function store(SermonSpeakerRequest $req)
    {
        $data = $req->validated();
        if ($req->hasFile('photo_file')) {
            $data['photo_path'] = $req->file('photo_file')->store('uploads/speakers', 'public');
        }
        unset($data['photo_file']);

        SermonSpeaker::create($data);
        return redirect()->route('admin.sermons.speakers.index')->with('success', 'Speaker created.');
    }

    public function edit(SermonSpeaker $speaker)
    {
        return view('admin.sermons.speakers.edit', compact('speaker'));
    }

    public function update(SermonSpeakerRequest $req, SermonSpeaker $speaker)
    {
        $data = $req->validated();
        if ($req->hasFile('photo_file')) {
            $data['photo_path'] = $req->file('photo_file')->store('uploads/speakers', 'public');
        }
        unset($data['photo_file']);

        $speaker->update($data);
        return redirect()->route('admin.sermons.speakers.index')->with('success', 'Speaker updated.');
    }

    public function show(SermonSpeaker $speaker)
    {
        return redirect()->route('admin.sermons.speakers.edit', $speaker);
    }

    public function destroy(SermonSpeaker $speaker)
    {
        $speaker->delete();
        return redirect()->route('admin.sermons.speakers.index')->with('success', 'Speaker deleted.');
    }
}
