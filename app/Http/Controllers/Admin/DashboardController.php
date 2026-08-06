<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActivityLog;
use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\Event;
use App\Models\Page;

class DashboardController extends Controller
{
    public function index()
    {
        $kpis = [
            'pages' => Page::count(),
            'posts' => BlogPost::published()->count(),
            'drafts' => BlogPost::where('is_published', false)->count(),
            'upcoming_events' => Event::published()->where('starts_at', '>=', now())->count(),
            'unread_messages' => ContactMessage::whereNull('read_at')->count(),
        ];

        $activity = ActivityLog::with('user')->latest('created_at')->limit(10)->get();

        return view('admin.dashboard', compact('kpis', 'activity'));
    }
}
