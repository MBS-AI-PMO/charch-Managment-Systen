<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Ministry;
use App\Models\Page;

class MinistryController extends Controller
{
    public function index()
    {
        $page = Page::where('slug', 'ministries')->published()->first();

        $ministries = Ministry::published()
            ->orderBy('sort_order')
            ->paginate(12);

        return view('site.ministries.index', compact('page', 'ministries'));
    }

    public function show(Ministry $ministry)
    {
        abort_unless($ministry->is_published, 404);

        $related = Ministry::published()
            ->where('id', '!=', $ministry->id)
            ->orderBy('sort_order')
            ->limit(3)
            ->get();

        return view('site.ministries.show', compact('ministry', 'related'));
    }
}
