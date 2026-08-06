@php
$kpis = [
    ['label' => 'Pages',            'value' => 7,  'sub' => 'singletons', 'link' => route('admin.preview.pages'),    'icon' => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/>',                          'tint' => 'bg-brand-primary/10 text-brand-primary'],
    ['label' => 'Published posts',  'value' => 12, 'sub' => '3 drafts',   'link' => route('admin.preview.blog'),     'icon' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4Z"/>',                                'tint' => 'bg-brand-secondary/20 text-[#8a6e2c]'],
    ['label' => 'Upcoming events',  'value' => 5,  'sub' => 'next: Sun',  'link' => route('admin.preview.events'),   'icon' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>',                            'tint' => 'bg-emerald-50 text-emerald-700'],
    ['label' => 'Unread messages',  'value' => 3,  'sub' => 'inbox',      'link' => route('admin.preview.messages'), 'icon' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',                                     'tint' => 'bg-blue-50 text-blue-700'],
];

$activity = [
    ['who' => 'Sarah Admin',    'what' => 'published a new blog post',  'target' => 'A season of small beginnings', 'when' => '2 min ago',           'icon' => 'pen'],
    ['who' => 'Mike Davis',     'what' => 'updated the event',          'target' => 'Family Picnic, June 14',       'when' => '24 min ago',          'icon' => 'calendar'],
    ['who' => 'Sarah Admin',    'what' => 'edited',                     'target' => 'Home page → Hero section',     'when' => '1 hour ago',          'icon' => 'doc'],
    ['who' => 'Pastor Greg',    'what' => 'uploaded sermon',            'target' => 'Hope in the Wilderness',       'when' => '3 hours ago',         'icon' => 'mic'],
    ['who' => 'Mike Davis',     'what' => 'created a new ministry',     'target' => 'Young adults',                 'when' => 'Yesterday at 4:12 PM','icon' => 'users'],
    ['who' => 'Sarah Admin',    'what' => 'added 8 photos to',          'target' => 'Easter 2026 album',            'when' => 'Yesterday at 3:42 PM','icon' => 'image'],
    ['who' => 'Anna Wright',    'what' => 'submitted contact message',  'target' => 'Question about baptism',       'when' => 'Yesterday at 1:18 PM','icon' => 'mail'],
    ['who' => 'Sarah Admin',    'what' => 'changed footer settings',    'target' => 'Site settings',                'when' => '2 days ago',          'icon' => 'cog'],
    ['who' => 'Mike Davis',     'what' => 'invited new user',           'target' => 'james@grace.local',            'when' => '2 days ago',          'icon' => 'user'],
    ['who' => 'Pastor Greg',    'what' => 'published',                  'target' => 'Sermon series: Walking by faith','when' => '3 days ago',        'icon' => 'mic'],
];

$activityIcons = [
    'pen'      => '<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4Z"/>',
    'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>',
    'doc'      => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/>',
    'mic'      => '<rect x="9" y="3" width="6" height="12" rx="3"/><path d="M5 11a7 7 0 0 0 14 0"/><path d="M12 18v3"/>',
    'users'    => '<circle cx="9" cy="8" r="3.2"/><path d="M2.5 20c.6-3.4 3.4-5.5 6.5-5.5s5.9 2.1 6.5 5.5"/>',
    'image'    => '<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="m21 16-5-5L5 21"/>',
    'mail'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
    'cog'      => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 0 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5"/>',
    'user'     => '<circle cx="12" cy="8" r="3.5"/><path d="M4 21c.7-4 3.9-6.5 8-6.5s7.3 2.5 8 6.5"/>',
];
@endphp

<x-admin.layout title="Dashboard">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-serif text-ink">Good afternoon, Sarah</h1>
            <p class="text-sm text-ink-muted mt-1">Here's what's happening across your church website today.</p>
        </div>
        <div class="text-xs text-ink-muted">Today is {{ now()->format('l, F j, Y') }}</div>
    </div>

    {{-- KPI grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        @foreach($kpis as $k)
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
                    <a href="{{ $k['link'] }}" class="text-xs text-brand-primary hover:underline">View all →</a>
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
                <a href="#" class="text-xs text-brand-primary hover:underline">View full log</a>
            </div>
            <ul class="divide-y divide-[rgb(var(--border))]">
                @foreach($activity as $a)
                    <li class="px-5 py-3 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-surface flex items-center justify-center text-ink-muted shrink-0">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">{!! $activityIcons[$a['icon']] !!}</svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-ink">
                                <span class="font-medium">{{ $a['who'] }}</span>
                                <span class="text-ink-muted">{{ $a['what'] }}</span>
                                <span class="font-medium">{{ $a['target'] }}</span>
                            </p>
                            <p class="text-xs text-ink-muted mt-0.5">{{ $a['when'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <h2 class="text-base font-serif mb-1">Quick actions</h2>
                <p class="text-xs text-ink-muted mb-4">Get something done without digging through menus.</p>
                <div class="space-y-2">
                    <a href="{{ route('admin.preview.blog.edit') }}" class="flex items-center justify-between gap-3 p-3 rounded-lg border border-[rgb(var(--border))] hover:bg-surface transition">
                        <span class="flex items-center gap-3 text-sm">
                            <span class="w-8 h-8 rounded-md bg-brand-primary/10 text-brand-primary flex items-center justify-center">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4Z"/></svg>
                            </span>
                            <span><span class="block font-medium">New blog post</span><span class="block text-xs text-ink-muted">Draft an article</span></span>
                        </span>
                        <span class="text-ink-muted">→</span>
                    </a>
                    <a href="{{ route('admin.preview.events') }}" class="flex items-center justify-between gap-3 p-3 rounded-lg border border-[rgb(var(--border))] hover:bg-surface transition">
                        <span class="flex items-center gap-3 text-sm">
                            <span class="w-8 h-8 rounded-md bg-emerald-50 text-emerald-700 flex items-center justify-center">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>
                            </span>
                            <span><span class="block font-medium">New event</span><span class="block text-xs text-ink-muted">Add a service or gathering</span></span>
                        </span>
                        <span class="text-ink-muted">→</span>
                    </a>
                    <a href="{{ route('admin.preview.pages.edit') }}" class="flex items-center justify-between gap-3 p-3 rounded-lg border border-[rgb(var(--border))] hover:bg-surface transition">
                        <span class="flex items-center gap-3 text-sm">
                            <span class="w-8 h-8 rounded-md bg-brand-secondary/20 text-[#8a6e2c] flex items-center justify-center">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V20a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1V9.5"/></svg>
                            </span>
                            <span><span class="block font-medium">Edit homepage</span><span class="block text-xs text-ink-muted">Hero, sections, SEO</span></span>
                        </span>
                        <span class="text-ink-muted">→</span>
                    </a>
                </div>
            </div>

            <div class="card p-5">
                <h2 class="text-base font-serif mb-3">This Sunday</h2>
                <div class="text-sm space-y-2">
                    <div class="flex items-baseline justify-between"><span class="text-ink-muted">Service</span><span class="font-medium">Hope in the wilderness</span></div>
                    <div class="flex items-baseline justify-between"><span class="text-ink-muted">Speaker</span><span class="font-medium">Pastor Greg</span></div>
                    <div class="flex items-baseline justify-between"><span class="text-ink-muted">Times</span><span class="font-medium">9 AM &amp; 11 AM</span></div>
                </div>
                <a href="{{ route('admin.preview.sermons') }}" class="mt-4 inline-flex text-xs text-brand-primary hover:underline">Manage sermons →</a>
            </div>
        </div>
    </div>
</x-admin.layout>
