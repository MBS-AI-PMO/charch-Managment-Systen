<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SermonRequest;
use App\Models\Sermon;
use App\Models\SermonSeries;
use App\Models\SermonSpeaker;
use Illuminate\Http\Request;

class SermonController extends Controller
{
    public function index(Request $req)
    {
        $q = Sermon::with(['series', 'speaker']);
        if ($s = $req->query('search')) $q->where('title', 'like', "%$s%");
        if ($series = $req->query('series_id')) $q->where('series_id', $series);
        if ($speaker = $req->query('speaker_id')) $q->where('speaker_id', $speaker);
        $sermons = $q->latest('preached_on')->paginate(20)->withQueryString();
        $series = SermonSeries::orderBy('name')->get();
        $speakers = SermonSpeaker::orderBy('name')->get();
        return view('admin.sermons.index', compact('sermons', 'series', 'speakers'));
    }

    public function create()
    {
        $sermon = new Sermon();
        $series = SermonSeries::orderBy('name')->get();
        $speakers = SermonSpeaker::orderBy('name')->get();
        return view('admin.sermons.edit', compact('sermon', 'series', 'speakers'));
    }

    public function store(SermonRequest $req)
    {
        $data = $req->validated();
        if ($req->hasFile('thumbnail_file')) {
            $data['thumbnail_path'] = $req->file('thumbnail_file')->store('uploads/sermons', 'public');
        }
        unset($data['thumbnail_file']);

        $sermon = Sermon::create($data);
        return redirect()->route('admin.sermons.edit', $sermon)->with('success', 'Sermon created.');
    }

    public function edit(Sermon $sermon)
    {
        $series = SermonSeries::orderBy('name')->get();
        $speakers = SermonSpeaker::orderBy('name')->get();
        return view('admin.sermons.edit', compact('sermon', 'series', 'speakers'));
    }

    public function update(SermonRequest $req, Sermon $sermon)
    {
        $data = $req->validated();
        if ($req->hasFile('thumbnail_file')) {
            $data['thumbnail_path'] = $req->file('thumbnail_file')->store('uploads/sermons', 'public');
        }
        unset($data['thumbnail_file']);

        $sermon->update($data);
        return redirect()->route('admin.sermons.edit', $sermon)->with('success', 'Sermon updated.');
    }

    public function show(Sermon $sermon)
    {
        return redirect()->route('admin.sermons.edit', $sermon);
    }

    public function destroy(Sermon $sermon)
    {
        $sermon->delete();
        return redirect()->route('admin.sermons.index')->with('success', 'Sermon deleted.');
    }
}
