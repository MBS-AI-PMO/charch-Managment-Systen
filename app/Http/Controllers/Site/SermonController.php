<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Sermon;
use App\Models\SermonSeries;
use App\Models\SermonSpeaker;
use Illuminate\Http\Request;

class SermonController extends Controller
{
    public function index(Request $request)
    {
        $page = Page::where('slug', 'sermons')->published()->first();

        $allSeries = SermonSeries::orderBy('name')->get();
        $allSpeakers = SermonSpeaker::orderBy('name')->get();

        $query = Sermon::published()->with(['series', 'speaker'])->latest('preached_on');

        if ($seriesSlug = $request->query('series')) {
            $s = SermonSeries::where('slug', $seriesSlug)->first();
            if ($s) {
                $query->where('series_id', $s->id);
            }
        }
        if ($speakerSlug = $request->query('speaker')) {
            $sp = SermonSpeaker::where('slug', $speakerSlug)->first();
            if ($sp) {
                $query->where('speaker_id', $sp->id);
            }
        }
        if ($q = $request->query('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhere('summary', 'like', "%{$q}%")
                  ->orWhere('scripture_reference', 'like', "%{$q}%");
            });
        }

        $sermons = $query->paginate(12)->withQueryString();

        return view('site.sermons.index', compact('page', 'sermons', 'allSeries', 'allSpeakers'));
    }

    public function show(Sermon $sermon)
    {
        abort_unless($sermon->is_published, 404);

        $sermon->load(['series', 'speaker']);

        $relatedInSeries = collect();
        if ($sermon->series_id) {
            $relatedInSeries = Sermon::published()
                ->with(['series', 'speaker'])
                ->where('series_id', $sermon->series_id)
                ->where('id', '!=', $sermon->id)
                ->latest('preached_on')
                ->limit(4)
                ->get();
        }

        return view('site.sermons.show', compact('sermon', 'relatedInSeries'));
    }
}
