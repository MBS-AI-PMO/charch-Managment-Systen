<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Page;

class QaController extends Controller
{
    public function index()
    {
        $page = Page::whereIn('slug', ['qa', 'questions'])->published()->first();

        $items = ContactMessage::query()
            ->publicAnswered()
            ->with(['replies' => fn ($q) => $q->where('is_staff', true)->oldest()])
            ->latest('replied_at')
            ->paginate(10);

        return view('site.qa.index', compact('page', 'items'));
    }
}
