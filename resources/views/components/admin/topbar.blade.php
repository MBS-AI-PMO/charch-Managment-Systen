@php
$routeName = Route::currentRouteName() ?? '';
$parts = explode('.', $routeName);
$section = $parts[1] ?? 'dashboard';
$sub = $parts[2] ?? null;
$action = $parts[3] ?? null;
$labels = [
    'dashboard'  => 'Dashboard',
    'pages'      => 'Pages',
    'blog'       => 'News',
    'sermons'    => 'Sermons',
    'events'     => 'Events',
    'ministries' => 'Ministries',
    'media'      => 'Media library',
    'menus'      => 'Menus',
    'messages'   => 'Messages',
    'users'      => 'Users',
    'roles'      => 'Roles & permissions',
    'settings'   => 'Site settings',
    'tithes'     => 'Tithes',
    'reports'    => 'Reports',
    'attendance' => 'Attendance',
];
$subLabels = [
    'funds'    => 'Funds',
    'series'   => 'Series',
    'speakers' => 'Speakers',
];
$primary = $labels[$section] ?? ucfirst($section);
$secondary = $subLabels[$sub] ?? (in_array($sub, ['edit', 'create', 'show'], true) ? ucfirst($sub) : null);
$tertiary = in_array($action, ['edit', 'create', 'show'], true) ? ucfirst($action) : null;
$user = auth('admin')->user();
$initials = $user
    ? collect(explode(' ', $user->name))->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->join('')
    : 'AD';
@endphp

<header class="h-14 bg-white border-b border-[rgb(var(--border))] flex items-center px-3 sm:px-4 lg:px-8 justify-between sticky top-0 z-20 gap-2">
    <div class="flex items-center gap-2 sm:gap-3 min-w-0">
        <button class="lg:hidden p-2 -ml-2 text-ink-muted shrink-0" @click="sidebar=!sidebar" aria-label="Toggle sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <nav class="text-sm flex items-center gap-1.5 sm:gap-2 text-ink-muted min-w-0 overflow-hidden">
            <a href="{{ route('admin.dashboard') }}" class="hover:underline shrink-0">Admin</a>
            <span class="text-ink-muted/50 shrink-0">/</span>
            <span class="text-ink font-medium truncate">{{ $primary }}</span>
            @if($secondary)
                <span class="text-ink-muted/50 shrink-0 hidden sm:inline">/</span>
                <span class="text-ink font-medium truncate hidden sm:inline">{{ $secondary }}</span>
            @endif
            @if($tertiary)
                <span class="text-ink-muted/50 shrink-0 hidden sm:inline">/</span>
                <span class="text-ink font-medium truncate hidden sm:inline">{{ $tertiary }}</span>
            @endif
        </nav>
    </div>

    <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
        <a href="{{ route('site.home') }}" target="_blank" rel="noopener" class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-md border border-[rgb(var(--border))] text-xs text-ink-muted hover:bg-surface">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3h7v7"/><path d="M21 3 12 12"/><path d="M5 5h6v2H7v10h10v-4h2v6H5z"/></svg>
            View site
        </a>

        <a href="{{ route('admin.messages.index') }}" class="relative p-2 rounded-md hover:bg-surface text-ink-muted" title="Messages">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/></svg>
            @php $unread = \App\Models\ContactMessage::whereNull('read_at')->count(); @endphp
            @if($unread > 0)
                <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-brand-primary"></span>
            @endif
        </a>

        <div x-data="{open:false}" @keydown.escape.window="open=false" class="relative">
            <button @click="open=!open" :aria-expanded="open" aria-haspopup="menu" class="flex items-center gap-2 text-sm pl-2 pr-3 py-1.5 rounded-md hover:bg-surface">
                <div class="w-7 h-7 rounded-full bg-brand-primary text-white flex items-center justify-center text-xs font-semibold">{{ $initials }}</div>
                <span class="hidden md:inline">{{ $user?->name ?? 'Admin' }}</span>
                <svg class="w-3 h-3 text-ink-muted" viewBox="0 0 20 20" fill="currentColor"><path d="M5.25 7.5 10 12.25 14.75 7.5z"/></svg>
            </button>
            <div x-show="open" @click.away="open=false" x-cloak x-transition.opacity role="menu" class="absolute right-0 mt-2 w-56 card p-1.5 text-sm z-40 bg-white">
                <div class="px-3 py-2 border-b border-[rgb(var(--border))]">
                    <div class="font-medium">{{ $user?->name }}</div>
                    <div class="text-xs text-ink-muted">{{ $user?->email }}</div>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 hover:bg-surface rounded text-brand-primary">Sign out</button>
                </form>
            </div>
        </div>
    </div>
</header>
