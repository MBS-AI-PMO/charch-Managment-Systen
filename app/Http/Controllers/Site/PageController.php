<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
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

        $branches = [
            [
                'name' => 'Assemblies of God Church, Naseerabad',
                'city' => 'Rawalpindi',
                'role' => 'Main campus',
                'address' => settings('contact.address', 'Naseerabad, Rawalpindi'),
                'phone' => settings('contact.phone'),
                'email' => settings('contact.email'),
                'services' => settings('contact.service_times', "Sunday Service: 10:00 AM – 12:00 PM\nPrayer Meeting: Thursday, 6:00 PM – 7:00 PM"),
                'note' => 'Our home congregation — gathered in worship, discipleship, and community care since 2001.',
            ],
            [
                'name' => 'Assemblies of God — Satellite Fellowship',
                'city' => 'Rawalpindi',
                'role' => 'Branch',
                'address' => 'Details coming soon',
                'phone' => settings('contact.phone'),
                'email' => settings('contact.email'),
                'services' => 'Service times will be posted here as this fellowship grows.',
                'note' => 'A growing fellowship connected to our main church family. Reach out to learn more or plan a visit.',
            ],
            [
                'name' => 'Assemblies of God — Outreach Point',
                'city' => 'Islamabad / Rawalpindi',
                'role' => 'Outreach',
                'address' => 'Contact the church office for location',
                'phone' => settings('contact.phone'),
                'email' => settings('contact.email'),
                'services' => 'Seasonal gatherings and community outreach.',
                'note' => 'An outreach point for prayer, teaching, and connecting families across the twin cities.',
            ],
        ];

        return view('site.churches', compact('page', 'branches'));
    }
}
