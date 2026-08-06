@php
$links = [
    ['route' => 'admin.dashboard',            'label' => 'Dashboard',        'icon' => 'home',      'permission' => null],
    ['route' => 'admin.pages.index',          'label' => 'Pages',            'icon' => 'doc',       'permission' => 'manage-pages'],
    ['route' => 'admin.blog.posts.index',     'label' => 'News',             'icon' => 'pen',       'permission' => 'manage-blog'],
    ['route' => 'admin.sermons.index',        'label' => 'Sermons',          'icon' => 'mic',       'permission' => 'manage-sermons'],
    ['route' => 'admin.events.index',         'label' => 'Events',           'icon' => 'calendar',  'permission' => 'manage-events'],
    ['route' => 'admin.ministries.index',     'label' => 'Ministries',       'icon' => 'users',     'permission' => 'manage-ministries'],
    ['route' => 'admin.media.index',          'label' => 'Media',            'icon' => 'image',     'permission' => 'manage-media'],
    ['route' => 'admin.menus.index',          'label' => 'Menus',            'icon' => 'list',      'permission' => 'manage-menus'],
    ['route' => 'admin.prayer-requests.index','label' => 'Prayer Requests',  'icon' => 'pray',      'permission' => 'manage-prayer-requests'],
    ['route' => 'admin.care.index',           'label' => 'Knock for Help',   'icon' => 'lifebuoy',  'permission' => 'manage-knock-help'],
    ['route' => 'admin.feed.index',           'label' => 'Community Feed',   'icon' => 'megaphone', 'permission' => 'manage-community-feed'],
    ['route' => 'admin.attendance.index',     'label' => 'Attendance',       'icon' => 'check',     'permission' => 'manage-events'],
    ['route' => 'admin.reminders.stub',       'label' => 'Reminders',        'icon' => 'bell',      'permission' => 'manage-events', 'badge' => 'Phase 3'],
    ['route' => 'admin.tithes.index',         'label' => 'Tithes',           'icon' => 'coins',     'permission' => 'manage-tithes'],
    ['route' => 'admin.tithes.funds.index',   'label' => 'Funds',            'icon' => 'wallet',    'permission' => 'manage-tithes'],
    ['route' => 'admin.reports.index',        'label' => 'Reports',          'icon' => 'chart',     'permission' => 'view-reports'],
    ['route' => 'admin.messages.index',       'label' => 'Messages',         'icon' => 'mail',      'permission' => 'manage-messages'],
    ['route' => 'admin.users.index',          'label' => 'Members',          'icon' => 'user',      'permission' => 'manage-users'],
    ['route' => 'admin.roles.index',          'label' => 'Roles',            'icon' => 'shield',    'permission' => 'manage-roles'],
    ['route' => 'admin.settings.index',       'label' => 'Settings',         'icon' => 'cog',       'permission' => 'manage-settings'],
];

$icons = [
    'home'      => '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V20a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1V9.5"/>',
    'doc'       => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/><path d="M9 13h6M9 17h6"/>',
    'pen'       => '<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4Z"/>',
    'mic'       => '<rect x="9" y="3" width="6" height="12" rx="3"/><path d="M5 11a7 7 0 0 0 14 0"/><path d="M12 18v3"/>',
    'calendar'  => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>',
    'users'     => '<circle cx="9" cy="8" r="3.2"/><path d="M2.5 20c.6-3.4 3.4-5.5 6.5-5.5s5.9 2.1 6.5 5.5"/><circle cx="17" cy="9" r="2.6"/><path d="M15.5 14.5c2.5.2 4.5 2 5 4.5"/>',
    'image'     => '<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="m21 16-5-5L5 21"/>',
    'list'      => '<path d="M8 6h13M8 12h13M8 18h13"/><circle cx="4" cy="6" r="1.2"/><circle cx="4" cy="12" r="1.2"/><circle cx="4" cy="18" r="1.2"/>',
    'mail'      => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
    'user'      => '<circle cx="12" cy="8" r="3.5"/><path d="M4 21c.7-4 3.9-6.5 8-6.5s7.3 2.5 8 6.5"/>',
    'shield'    => '<path d="M12 3 4 6v6c0 4.5 3.4 8.4 8 9 4.6-.6 8-4.5 8-9V6Z"/><path d="m9 12 2.2 2.2L15 10.5"/>',
    'cog'       => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 0 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 0 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 0 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1Z"/>',
    'pray'      => '<path d="M12 3v6M9 9h6M9 21l3-3 3 3M5 21l3-5 4 1M19 21l-3-5-4 1"/>',
    'lifebuoy'  => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/><path d="M5.6 5.6l3.4 3.4M15 15l3.4 3.4M5.6 18.4L9 15M15 9l3.4-3.4"/>',
    'megaphone' => '<path d="M3 11v2a2 2 0 0 0 2 2h1l4 4V5L6 9H5a2 2 0 0 0-2 2zM18 8a4 4 0 0 1 0 8"/>',
    'check'     => '<path d="m5 12 5 5L20 7"/>',
    'bell'      => '<path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/>',
    'coins'     => '<ellipse cx="9" cy="7" rx="6" ry="2.5"/><path d="M3 7v4c0 1.4 2.7 2.5 6 2.5s6-1.1 6-2.5V7"/><path d="M3 11v4c0 1.4 2.7 2.5 6 2.5s6-1.1 6-2.5v-4"/><ellipse cx="16" cy="14" rx="5" ry="2"/><path d="M11 14v3c0 1.1 2.2 2 5 2s5-.9 5-2v-3"/>',
    'wallet'    => '<rect x="3" y="6" width="18" height="14" rx="2"/><path d="M3 10h18"/><circle cx="17" cy="15" r="1.2"/>',
    'chart'     => '<path d="M3 3v18h18"/><path d="M7 15l4-4 3 3 5-6"/>',
];

$current = request()->route()?->getName() ?? '';

$isActive = function (string $route) use ($current, $links) {
    $stem = preg_replace('/\.index$/', '', $route);
    $matches = $current === $route
        || $current === $stem
        || str_starts_with($current, $stem.'.');

    if (! $matches) {
        return false;
    }

    // Prefer a more specific sidebar link (e.g. Funds over Tithes on /tithes/funds).
    foreach ($links as $other) {
        $otherRoute = $other['route'] ?? '';
        if ($otherRoute === $route) {
            continue;
        }

        $otherStem = preg_replace('/\.index$/', '', $otherRoute);
        if (! str_starts_with($otherStem, $stem.'.')) {
            continue;
        }

        if (
            $current === $otherRoute
            || $current === $otherStem
            || str_starts_with($current, $otherStem.'.')
        ) {
            return false;
        }
    }

    return true;
};

$user = auth('admin')->user();
$initials = $user
    ? collect(explode(' ', $user->name))->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->join('')
    : 'AD';
$adminBrand = settings('brand.name', 'Assemblies of God');
$adminTagline = settings('brand.tagline', 'Rawalpindi') ?: 'Rawalpindi';
@endphp

{{-- Mobile overlay --}}
<div
    x-show="sidebar"
    x-cloak
    @click="sidebar=false"
    class="fixed inset-0 z-30 bg-black/40 lg:hidden"
></div>

<aside
    class="admin-sidebar admin-sidebar--expanded fixed z-40 inset-y-0 left-0 bg-[#1F1A18] text-[#FAF7F2] flex flex-col"
    :class="[
        collapsed ? 'admin-sidebar--collapsed' : 'admin-sidebar--expanded',
        sidebar ? 'admin-sidebar--open' : '',
    ]"
    aria-label="Admin navigation"
>
    {{-- Brand + collapse toggle --}}
    <div class="shrink-0 border-b border-white/10 px-2 py-3">
        <div class="flex items-start gap-2">
            <a href="{{ route('admin.dashboard') }}" class="flex items-start gap-2.5 min-w-0 flex-1" :title="collapsed ? '{{ $adminBrand }}' : null">
                @if($brandLogoUrl ?? null)
                    <img
                        src="{{ $brandLogoUrl }}"
                        alt="{{ $adminBrand }}"
                        class="object-contain shrink-0 mt-0.5 h-9 w-auto max-w-[52px]"
                        :class="collapsed ? 'h-8 w-8 max-w-none' : 'h-9 w-auto max-w-[52px]'"
                    >
                @else
                    <span class="h-8 w-8 rounded-md bg-brand-primary flex items-center justify-center text-white font-serif text-sm shrink-0">
                        {{ mb_strtoupper(mb_substr($adminBrand, 0, 1)) }}
                    </span>
                @endif
                <span class="admin-sidebar-label leading-snug min-w-0 pt-0.5">
                    <span class="block text-[12px] font-semibold text-white leading-snug">{{ $adminBrand }}</span>
                    <span class="block text-[10px] text-white/55 mt-1 leading-none">{{ $adminTagline }}</span>
                </span>
            </a>
            <button
                type="button"
                class="hidden lg:inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md text-white/50 hover:bg-white/10 hover:text-white transition mt-0.5"
                @click="setCollapsed(!collapsed)"
                :aria-label="collapsed ? 'Expand sidebar' : 'Collapse sidebar'"
                :title="collapsed ? 'Expand' : 'Collapse'"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                     :class="collapsed ? 'rotate-180' : ''" class="transition-transform duration-200">
                    <path d="M15 6l-6 6 6 6"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Nav with visible scrollbar --}}
    <nav class="admin-sidebar-nav flex-1 overflow-y-scroll overflow-x-hidden py-2">
        @foreach($links as $l)
            @if($l['permission'] === null || auth('admin')->user()?->can($l['permission']))
                <a
                    href="{{ Route::has($l['route']) ? route($l['route']) : '#' }}"
                    :title="collapsed ? '{{ $l['label'] }}' : null"
                    @class([
                        'admin-sidebar-link group relative mx-1.5 mb-0.5 flex items-center gap-3 rounded-lg transition px-3 py-2',
                        'bg-white/10 text-brand-secondary font-medium' => $isActive($l['route']),
                        'text-white/65 hover:bg-white/8 hover:text-white' => ! $isActive($l['route']),
                    ])
                    :class="collapsed ? 'justify-center px-0 h-10' : 'px-3 py-2'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 opacity-90">
                        {!! $icons[$l['icon']] ?? $icons['doc'] !!}
                    </svg>
                    <span class="admin-sidebar-label text-[13px] truncate">{{ $l['label'] }}</span>
                    @if(!empty($l['badge']))
                        <span class="admin-sidebar-label ml-auto text-[10px] px-1.5 py-0.5 rounded bg-white/10 text-white/55">{{ $l['badge'] }}</span>
                    @endif

                    {{-- Tooltip when collapsed --}}
                    <span
                        x-show="collapsed"
                        x-cloak
                        class="pointer-events-none absolute left-[calc(100%+0.4rem)] top-1/2 z-50 -translate-y-1/2 whitespace-nowrap rounded-md bg-[#1F1A18] px-2 py-1 text-[11px] font-medium text-white opacity-0 shadow-lg ring-1 ring-white/10 transition group-hover:opacity-100"
                    >{{ $l['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>

    <div class="admin-sidebar-foot shrink-0 border-t border-white/10 flex items-center gap-2.5 px-3 py-3" :class="collapsed ? 'justify-center px-2' : 'px-3'">
        <div class="w-8 h-8 rounded-full bg-brand-secondary/20 text-brand-secondary flex items-center justify-center text-[11px] font-semibold shrink-0">{{ $initials }}</div>
        <div class="admin-sidebar-label leading-tight min-w-0 overflow-hidden">
            <div class="text-white text-[12px] truncate">{{ $user?->name ?? 'Admin' }}</div>
            <div class="text-[10px] text-white/50 truncate">{{ $user?->roles?->first()?->name ?? 'Administrator' }}</div>
        </div>
    </div>
</aside>
