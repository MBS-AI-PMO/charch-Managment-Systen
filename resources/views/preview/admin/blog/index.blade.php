@php
$posts = [
    ['title'=>'A season of small beginnings',  'category'=>'Devotional', 'author'=>'Pastor Greg', 'status'=>'published','date'=>'May 28, 2026'],
    ['title'=>'Why we serve the city',         'category'=>'Outreach',   'author'=>'Mike Davis',  'status'=>'published','date'=>'May 21, 2026'],
    ['title'=>'Practicing sabbath',            'category'=>'Devotional', 'author'=>'Sarah Admin', 'status'=>'draft',    'date'=>'—'],
    ['title'=>'Easter recap & photos',         'category'=>'News',       'author'=>'Sarah Admin', 'status'=>'published','date'=>'Apr 06, 2026'],
    ['title'=>'Welcoming Pastor James',        'category'=>'News',       'author'=>'Pastor Greg', 'status'=>'published','date'=>'Mar 30, 2026'],
    ['title'=>'Lent: a 40-day journey',        'category'=>'Devotional', 'author'=>'Pastor Greg', 'status'=>'published','date'=>'Feb 22, 2026'],
    ['title'=>'Volunteer Sunday — wrap-up',    'category'=>'Outreach',   'author'=>'Mike Davis',  'status'=>'scheduled','date'=>'Jun 14, 2026'],
    ['title'=>'New worship night format',      'category'=>'News',       'author'=>'Sarah Admin', 'status'=>'draft',    'date'=>'—'],
];
@endphp

<x-admin.layout title="Blog">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-serif">Blog</h1>
            <p class="text-sm text-ink-muted mt-1">Write devotionals, news, and outreach stories for your congregation.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="btn-ghost text-sm">Export</button>
            <a href="{{ route('admin.preview.blog.edit') }}" class="btn-primary text-sm">+ New post</a>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="px-5 py-3 border-b border-[rgb(var(--border))] flex items-center gap-2 flex-wrap">
            <input type="text" class="input w-64" placeholder="Search posts…">
            <select class="input w-40">
                <option>All statuses</option>
                <option>Published</option>
                <option>Draft</option>
                <option>Scheduled</option>
            </select>
            <select class="input w-40">
                <option>All categories</option>
                <option>Devotional</option>
                <option>News</option>
                <option>Outreach</option>
            </select>
            <span class="ml-auto text-xs text-ink-muted">{{ count($posts) }} posts</span>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-surface text-ink-muted text-xs uppercase tracking-wider">
                <tr>
                    <th class="text-left font-medium px-5 py-3">Title</th>
                    <th class="text-left font-medium px-5 py-3">Category</th>
                    <th class="text-left font-medium px-5 py-3">Author</th>
                    <th class="text-left font-medium px-5 py-3">Status</th>
                    <th class="text-left font-medium px-5 py-3">Published</th>
                    <th class="text-right font-medium px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[rgb(var(--border))]">
                @foreach($posts as $p)
                    <tr class="hover:bg-surface/60">
                        <td class="px-5 py-3 font-medium">{{ $p['title'] }}</td>
                        <td class="px-5 py-3 text-ink-muted">{{ $p['category'] }}</td>
                        <td class="px-5 py-3 text-ink-muted">{{ $p['author'] }}</td>
                        <td class="px-5 py-3">
                            @php $s = $p['status']; @endphp
                            @if($s === 'published')
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Published</span>
                            @elseif($s === 'scheduled')
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-blue-700 border border-blue-200"><span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Scheduled</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Draft</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-ink-muted">{{ $p['date'] }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.preview.blog.edit') }}" class="text-xs text-brand-primary hover:underline">Edit</a>
                            <a href="#" class="text-xs text-ink-muted hover:text-ink ml-3">View</a>
                            <a href="#" class="text-xs text-ink-muted hover:text-brand-primary ml-3">Delete</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-[rgb(var(--border))] flex items-center justify-between text-xs text-ink-muted">
            <span>Showing 1–{{ count($posts) }} of 24</span>
            <div class="flex items-center gap-1">
                <button class="px-2 py-1 rounded border border-[rgb(var(--border))]">‹</button>
                <button class="px-2 py-1 rounded bg-brand-primary text-white">1</button>
                <button class="px-2 py-1 rounded border border-[rgb(var(--border))]">2</button>
                <button class="px-2 py-1 rounded border border-[rgb(var(--border))]">3</button>
                <button class="px-2 py-1 rounded border border-[rgb(var(--border))]">›</button>
            </div>
        </div>
    </div>
</x-admin.layout>
