<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\ChurchBranch;
use App\Models\Event;
use App\Models\HeroSlide;
use App\Models\Ministry;
use App\Models\Page;
use App\Models\Sermon;

class PageController extends Controller
{
    public function home()
    {
        $page = Page::where('slug', 'home')->published()->firstOrFail();

        $upcomingEvents = Event::published()
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->limit(3)
            ->get();

        $latestSermon = Sermon::published()
            ->with(['series', 'speaker'])
            ->latest('preached_on')
            ->first();

        $recentSermons = Sermon::published()
            ->with(['series', 'speaker'])
            ->latest('preached_on')
            ->limit(3)
            ->get();

        $morePosts = BlogPost::published()
            ->with(['category', 'author'])
            ->latest('published_at')
            ->limit(3)
            ->get();

        $ministries = Ministry::published()
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        $heroSlides = HeroSlide::active()->get();

        return view('site.home', compact(
            'page', 'upcomingEvents', 'latestSermon', 'recentSermons', 'morePosts', 'ministries', 'heroSlides'
        ));
    }

    public function about()
    {
        $page = Page::whereIn('slug', ['about-us', 'about', 'church'])->published()->firstOrFail();

        return view('site.about', compact('page'));
    }

    public function donate()
    {
        $page = Page::where('slug', 'donate')->published()->firstOrFail();

        return view('site.donate', compact('page'));
    }

    public function churches()
    {
        $page = Page::whereIn('slug', ['our-churches', 'churches'])->published()->first();
        $branches = ChurchBranch::published()->ordered()->get();

        return view('site.churches', compact('page', 'branches'));
    }

    public function church(ChurchBranch $church)
    {
        abort_unless($church->is_published, 404);

        $page = Page::whereIn('slug', ['our-churches', 'churches'])->published()->first();

        return view('site.church-show', ['page' => $page, 'branch' => $church]);
    }
}
