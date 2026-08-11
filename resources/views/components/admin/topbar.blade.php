@php include resource_path('views/components/admin/partials/nav-config.php'); @endphp

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
    'certificates' => 'Certificates',
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
$adminBrand = settings('brand.name', 'Assemblies of God');
$adminTagline = settings('brand.tagline', 'Rawalpindi') ?: 'Rawalpindi';
@endphp

{{-- Site-style sticky header: bar + dropdown mobile nav (not a side drawer) --}}
<header
    class="admin-topbar sticky top-0 z-40 bg-white border-b border-[rgb(var(--border))]"
    @keydown.escape.window="sidebar=false"
>
    <div class="h-14 flex items-center px-3 sm:px-4 lg:px-8 justify-between gap-2">
        <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
            {{-- Mobile brand (matches public site header) --}}
            <a href="{{ route('admin.dashboard') }}" class="lg:hidden flex items-center gap-2 min-w-0 shrink">
                @if($brandLogoUrl ?? null)
                    <img src="{{ $brandLogoUrl }}" alt="{{ $adminBrand }}" class="h-8 w-auto max-w-[40px] object-contain shrink-0">
                @endif
                <span class="leading-tight min-w-0">
                    <span class="block text-[13px] font-semibold text-ink truncate">{{ $adminBrand }}</span>
                    <span class="block text-[10px] tracking-wide uppercase text-ink-muted truncate">{{ $adminTagline }}</span>
                </span>
            </a>

            {{-- Desktop breadcrumbs --}}
            <nav class="text-sm hidden lg:flex items-center gap-1.5 sm:gap-2 text-ink-muted min-w-0 overflow-hidden">
                <a href="{{ route('admin.dashboard') }}" class="hover:underline shrink-0">Admin</a>
                <span class="text-ink-muted/50 shrink-0">/</span>
                <span class="text-ink font-medium truncate">{{ $primary }}</span>
                @if($secondary)
                    <span class="text-ink-muted/50 shrink-0">/</span>
                    <span class="text-ink font-medium truncate">{{ $secondary }}</span>
                @endif
                @if($tertiary)
                    <span class="text-ink-muted/50 shrink-0">/</span>
                    <span class="text-ink font-medium truncate">{{ $tertiary }}</span>
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

            <div x-data="{open:false}" @keydown.escape.window="open=false" class="relative hidden sm:block">
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

            {{-- Hamburger — same pattern as public site header --}}
            <button
                type="button"
                class="lg:hidden p-2 -mr-1 text-ink shrink-0 rounded-lg hover:bg-surface transition-colors"
                @click="sidebar=!sidebar"
                :aria-expanded="sidebar"
                aria-controls="admin-mobile-nav"
                aria-label="Menu"
            >
                <svg x-show="!sidebar" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="sidebar" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile dropdown under header (website-style chip grid) --}}
    <div
        id="admin-mobile-nav"
        x-show="sidebar"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="lg:hidden border-t border-[rgb(var(--border))] bg-surface/98 backdrop-blur"
    >
        <div class="px-3 sm:px-4 py-4 max-h-[min(70vh,28rem)] overflow-y-auto overscroll-contain">
            <nav class="site-mobile-nav-grid" aria-label="Admin mobile">
                @foreach($adminVisibleLinks as $l)
                    <a
                        href="{{ Route::has($l['route']) ? route($l['route']) : '#' }}"
                        @click="sidebar=false"
                        @class([
                            'site-mobile-nav-chip',
                            'border-brand-primary/25 bg-brand-primary/5 text-brand-primary' => $adminNavIsActive($l['route']),
                        ])
                    >
                        <span class="truncate">{{ $l['label'] }}</span>
                        @if(!empty($l['badge']))
                            <span class="shrink-0 text-[10px] px-1.5 py-0.5 rounded bg-black/5 text-ink-muted">{{ $l['badge'] }}</span>
                        @endif
                    </a>
                @endforeach
            </nav>

            <div class="mt-4 flex flex-col gap-2 border-t border-[rgb(var(--border))] pt-4">
                <div class="flex items-center gap-2.5 px-1">
                    <div class="w-8 h-8 rounded-full bg-brand-primary text-white flex items-center justify-center text-[11px] font-semibold shrink-0">{{ $initials }}</div>
                    <div class="leading-tight min-w-0 overflow-hidden flex-1">
                        <div class="text-ink text-[13px] font-medium truncate">{{ $user?->name ?? 'Admin' }}</div>
                        <div class="text-[11px] text-ink-muted truncate">{{ $user?->roles?->first()?->name ?? 'Administrator' }}</div>
                    </div>
                </div>
                <a href="{{ route('site.home') }}" target="_blank" rel="noopener" @click="sidebar=false" class="site-mobile-nav-chip">View site</a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="site-mobile-nav-chip w-full text-left text-brand-primary">Sign out</button>
                </form>
            </div>
        </div>
    </div>
</header>
