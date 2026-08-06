@php
$kpiCards = [
    [
        'label' => 'Pages',
        'value' => $kpis['pages'],
        'sub'   => 'singletons',
        'link'  => route('admin.pages.index'),
        'icon'  => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/>',
        'tint'  => 'bg-brand-primary/10 text-brand-primary',
    ],
    [
        'label' => 'Published news',
        'value' => $kpis['posts'],
        'sub'   => $kpis['drafts'] . ' draft' . ($kpis['drafts'] === 1 ? '' : 's'),
        'link'  => route('admin.blog.posts.index'),
        'icon'  => '<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4Z"/>',
        'tint'  => 'bg-brand-secondary/20 text-[#8a6e2c]',
    ],
    [
        'label' => 'Upcoming events',
        'value' => $kpis['upcoming_events'],
        'sub'   => 'published',
        'link'  => route('admin.events.index'),
        'icon'  => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>',
        'tint'  => 'bg-emerald-50 text-emerald-700',
    ],
    [
        'label' => 'Unread messages',
        'value' => $kpis['unread_messages'],
        'sub'   => 'inbox',
        'link'  => route('admin.messages.index'),
        'icon'  => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
        'tint'  => 'bg-blue-50 text-blue-700',
    ],
];

$user = auth('admin')->user();
$hour = (int) now()->format('G');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
@endphp

<x-admin.layout title="Dashboard">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-serif text-ink">{{ $greeting }}, {{ explode(' ', $user?->name ?? 'Admin')[0] }}</h1>
            <p class="text-sm text-ink-muted mt-1">Here's what's happening across your church website today.</p>
        </div>
        <div class="text-xs text-ink-muted">Today is {{ now()->format('l, F j, Y') }}</div>
    </div>

    {{-- KPI grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        @foreach($kpiCards as $k)
            <div class="card p-5">
                <div class="flex items-start justify-between">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $k['tint'] }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">{!! $k['icon'] !!}</svg>
                    </div>
                    <span class="text-[11px] text-ink-muted uppercase tracking-wider">{{ $k['sub'] }}</span>
                </div>
                <div class="mt-4 text-3xl font-serif text-ink">{{ $k['value'] }}</div>
                <div class="mt-1 flex items-center justify-between">
                    <span class="text-sm text-ink-muted">{{ $k['label'] }}</span>
                    <a href="{{ $k['link'] }}" class="text-xs text-brand-primary hover:underline">View all &rarr;</a>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Activity + quick actions --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="card lg:col-span-2">
            <div class="px-5 py-4 border-b border-[rgb(var(--border))] flex items-center justify-between">
                <div>
                    <h2 class="text-base font-serif">Recent activity</h2>
                    <p class="text-xs text-ink-muted mt-0.5">Edits, publishes and uploads from your team.</p>
                </div>
            </div>
            @if($activity->isEmpty())
                <div class="px-5 py-10 text-center text-sm text-ink-muted">No activity yet. As your team edits pages, posts and events, the log will fill up here.</div>
            @else
                <ul class="divide-y divide-[rgb(var(--border))]">
                    @foreach($activity as $a)
                        <li class="px-5 py-3 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-surface flex items-center justify-center text-ink-muted shrink-0">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4Z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-ink">
                                    <span class="font-medium">{{ $a->user?->name ?? 'System' }}</span>
                                    <span class="text-ink-muted">{{ $a->action }}</span>
                                    <span class="font-medium">{{ class_basename($a->subject_type) }} #{{ $a->subject_id }}</span>
                                </p>
                                <p class="text-xs text-ink-muted mt-0.5">{{ $a->created_at?->diffForHumans() }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <h2 class="text-base font-serif mb-1">Quick actions</h2>
                <p class="text-xs text-ink-muted mb-4">Get something done without digging through menus.</p>
                <div class="space-y-2">
                    <a href="{{ route('admin.blog.posts.create') }}" class="flex items-center justify-between gap-3 p-3 rounded-lg border border-[rgb(var(--border))] hover:bg-surface transition">
                        <span class="flex items-center gap-3 text-sm">
                            <span class="w-8 h-8 rounded-md bg-brand-primary/10 text-brand-primary flex items-center justify-center">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4Z"/></svg>
                            </span>
                            <span><span class="block font-medium">New news post</span><span class="block text-xs text-ink-muted">Share an update</span></span>
                        </span>
                        <span class="text-ink-muted">&rarr;</span>
                    </a>
                    <a href="{{ route('admin.events.create') }}" class="flex items-center justify-between gap-3 p-3 rounded-lg border border-[rgb(var(--border))] hover:bg-surface transition">
                        <span class="flex items-center gap-3 text-sm">
                            <span class="w-8 h-8 rounded-md bg-emerald-50 text-emerald-700 flex items-center justify-center">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>
                            </span>
                            <span><span class="block font-medium">New event</span><span class="block text-xs text-ink-muted">Add a service or gathering</span></span>
                        </span>
                        <span class="text-ink-muted">&rarr;</span>
                    </a>
                    <a href="{{ route('admin.pages.index') }}" class="flex items-center justify-between gap-3 p-3 rounded-lg border border-[rgb(var(--border))] hover:bg-surface transition">
                        <span class="flex items-center gap-3 text-sm">
                            <span class="w-8 h-8 rounded-md bg-brand-secondary/20 text-[#8a6e2c] flex items-center justify-center">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V20a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1V9.5"/></svg>
                            </span>
                            <span><span class="block font-medium">Edit a page</span><span class="block text-xs text-ink-muted">Hero, body, SEO</span></span>
                        </span>
                        <span class="text-ink-muted">&rarr;</span>
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="flex items-center justify-between gap-3 p-3 rounded-lg border border-[rgb(var(--border))] hover:bg-surface transition">
                        <span class="flex items-center gap-3 text-sm">
                            <span class="w-8 h-8 rounded-md bg-blue-50 text-blue-700 flex items-center justify-center">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/></svg>
                            </span>
                            <span><span class="block font-medium">Site settings</span><span class="block text-xs text-ink-muted">Brand, contact, footer</span></span>
                        </span>
                        <span class="text-ink-muted">&rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-admin.layout>
