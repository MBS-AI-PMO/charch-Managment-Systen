<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\FeedPost;
use App\Models\PrayerRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $latestPosts = FeedPost::published()
            ->orderBy('pinned', 'desc')
            ->latest('published_at')
            ->limit(3)
            ->get();

        $myPrayer = PrayerRequest::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        $upcomingEvents = Event::published()
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->limit(3)
            ->get();

        $upcomingRsvps = EventRsvp::with(['event' => function ($q) {
                $q->where('starts_at', '>=', now())->orderBy('starts_at');
            }])
            ->where('user_id', $user->id)
            ->where('status', 'going')
            ->get()
            ->filter(fn ($r) => $r->event !== null)
            ->take(3);

        $hasOpenCheckin = Event::query()
            ->where('attendance_open', true)
            ->whereBetween('starts_at', [now()->subHours(4), now()->addHours(12)])
            ->exists();

        return view('member.dashboard', compact(
            'latestPosts',
            'myPrayer',
            'upcomingEvents',
            'upcomingRsvps',
            'hasOpenCheckin',
        ));
    }
}
