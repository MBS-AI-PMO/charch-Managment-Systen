<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SermonSeriesRequest;
use App\Models\SermonSeries;

class SermonSeriesController extends Controller
{
    public function index()
    {
        $series = SermonSeries::withCount('sermons')->orderBy('name')->paginate(10);
        return view('admin.sermons.series.index', compact('series'));
    }

    public function create()
    {
        return view('admin.sermons.series.edit', ['series' => new SermonSeries()]);
    }

    public function store(SermonSeriesRequest $req)
    {
        $data = $req->validated();
        if ($req->hasFile('cover_image_file')) {
            $data['cover_image_path'] = $req->file('cover_image_file')->store('uploads/sermons', 'public');
        }
        unset($data['cover_image_file']);

        SermonSeries::create($data);
        return redirect()->route('admin.sermons.series.index')->with('success', 'Series created.');
    }

    public function edit(SermonSeries $series)
    {
        return view('admin.sermons.series.edit', compact('series'));
    }

    public function update(SermonSeriesRequest $req, SermonSeries $series)
    {
        $data = $req->validated();
        if ($req->hasFile('cover_image_file')) {
            $data['cover_image_path'] = $req->file('cover_image_file')->store('uploads/sermons', 'public');
        }
        unset($data['cover_image_file']);

        $series->update($data);
        return redirect()->route('admin.sermons.series.index')->with('success', 'Series updated.');
    }

    public function show(SermonSeries $series)
    {
        return redirect()->route('admin.sermons.series.edit', $series);
    }

    public function destroy(SermonSeries $series)
    {
        $series->delete();
        return redirect()->route('admin.sermons.series.index')->with('success', 'Series deleted.');
    }
}
