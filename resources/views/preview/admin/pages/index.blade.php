@php
$pages = [
    ['title' => 'Home',          'slug' => '/',           'status' => 'published', 'editor' => 'Sarah Admin', 'updated' => '2 min ago'],
    ['title' => 'About',         'slug' => '/about',      'status' => 'published', 'editor' => 'Sarah Admin', 'updated' => 'Yesterday at 3:42 PM'],
    ['title' => 'Beliefs',       'slug' => '/beliefs',    'status' => 'published', 'editor' => 'Pastor Greg', 'updated' => '3 days ago'],
    ['title' => 'Visit',         'slug' => '/visit',      'status' => 'published', 'editor' => 'Mike Davis',  'updated' => '5 days ago'],
    ['title' => 'Giving',        'slug' => '/giving',     'status' => 'draft',     'editor' => 'Sarah Admin', 'updated' => '6 days ago'],
    ['title' => 'Plan a visit',  'slug' => '/plan-visit', 'status' => 'published', 'editor' => 'Mike Davis',  'updated' => 'Apr 22, 2026'],
    ['title' => 'Privacy policy','slug' => '/privacy',    'status' => 'published', 'editor' => 'Sarah Admin', 'updated' => 'Mar 14, 2026'],
];
@endphp

<x-admin.layout title="Pages">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-serif">Pages</h1>
            <p class="text-sm text-ink-muted mt-1">Edit the singleton pages that make up your public site. New top-level pages are added through Menus.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="btn-ghost text-sm">Export</button>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="px-5 py-3 border-b border-[rgb(var(--border))] flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-2 text-sm">
                <input type="text" class="input w-64" placeholder="Search pages…">
                <select class="input w-36">
                    <option>All statuses</option>
                    <option>Published</option>
                    <option>Draft</option>
                </select>
            </div>
            <div class="text-xs text-ink-muted">{{ count($pages) }} pages</div>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-surface text-ink-muted text-xs uppercase tracking-wider">
                <tr>
                    <th class="text-left font-medium px-5 py-3">Title</th>
                    <th class="text-left font-medium px-5 py-3">Slug</th>
                    <th class="text-left font-medium px-5 py-3">Status</th>
                    <th class="text-left font-medium px-5 py-3">Last edited by</th>
                    <th class="text-left font-medium px-5 py-3">Updated</th>
                    <th class="text-right font-medium px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[rgb(var(--border))]">
                @foreach($pages as $p)
                    <tr class="hover:bg-surface/60">
                        <td class="px-5 py-3 font-medium">{{ $p['title'] }}</td>
                        <td class="px-5 py-3 text-ink-muted font-mono text-xs">{{ $p['slug'] }}</td>
                        <td class="px-5 py-3">
                            @if($p['status'] === 'published')
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Published
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-ink-muted">{{ $p['editor'] }}</td>
                        <td class="px-5 py-3 text-ink-muted">{{ $p['updated'] }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.preview.pages.edit') }}" class="inline-flex items-center gap-1 text-brand-primary hover:underline text-xs">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                Edit
                            </a>
                            <a href="#" class="inline-flex items-center gap-1 text-ink-muted hover:text-ink text-xs ml-3">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1.5 12s4-7.5 10.5-7.5S22.5 12 22.5 12 18.5 19.5 12 19.5 1.5 12 1.5 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                View
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin.layout>
