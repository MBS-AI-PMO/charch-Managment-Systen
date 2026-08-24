@php
use Illuminate\Support\Facades\Route;

// Prefer real member.* routes when they exist; fall back to preview.member.* for the preview screens.
$pick = fn ($real, $preview) => Route::has($real) ? route($real) : (Route::has($preview) ? route($preview) : '#');
$activeReal = fn ($real, $preview) => request()->routeIs($real) || request()->routeIs($preview);

$attendanceOpenToday = \App\Models\Event::query()
    ->where('attendance_open', true)
    ->whereBetween('starts_at', [now()->subHours(4), now()->addHours(12)])
    ->exists();

$links = [
    ['url' => $pick('member.dashboard',  'preview.member.dashboard'), 'active' => $activeReal('member.dashboard',  'preview.member.dashboard'),  'label' => 'Dashboard'],
    ['url' => Route::has('member.certificates.index') ? route('member.certificates.index') : '#', 'active' => request()->routeIs('member.certificates.*'), 'label' => 'Certificates'],
    ['url' => $pick('member.prayer.index','preview.member.prayer'),   'active' => $activeReal('member.prayer.*',   'preview.member.prayer*'),    'label' => 'Prayer'],
    ['url' => $pick('member.care.index', 'preview.member.care'),      'active' => $activeReal('member.care.*',     'preview.member.care*'),      'label' => 'Knock'],
    ['url' => Route::has('member.questions.index') ? route('member.questions.index') : '#', 'active' => request()->routeIs('member.questions.*'), 'label' => 'Questions'],
    ['url' => $pick('member.feed.index', 'preview.member.feed'),      'active' => $activeReal('member.feed.*',     'preview.member.feed'),       'label' => 'Feed'],
];

$donateUrl = Route::has('site.donate') ? route('site.donate') : $pick('member.giving', 'preview.member.donate');
$donateLabel = function_exists('settings') ? settings('donate.button_label', 'Donate') : 'Donate';

if ($attendanceOpenToday && Route::has('member.checkin.show')) {
    $links[] = [
        'url' => route('member.checkin.show'),
        'active' => request()->routeIs('member.checkin.*'),
        'label' => 'Check in',
    ];
}

$brand = function_exists('settings') ? settings('brand.name', 'Assemblies of God') : 'Assemblies of God';
$tagline = function_exists('settings') ? settings('brand.tagline', 'Rawalpindi') : 'Rawalpindi';
$user = auth()->user();
$displayName = $user?->name ?? 'Member';
$initials = $user
    ? collect(explode(' ', $user->name))->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->join('')
    : 'M';
$dashboardUrl = $pick('member.dashboard', 'preview.member.dashboard');
$profileUrl   = $pick('member.profile.edit', 'preview.member.profile');
$hasLogout    = Route::has('logout');
$siteHomeUrl  = Route::has('site.home') ? route('site.home') : url('/');
@endphp
<header
    x-data="{
        open: false,
        syncNavLock() {
            const lock = this.open && window.matchMedia('(max-width: 1279px)').matches;
            document.documentElement.classList.toggle('overflow-hidden', lock);
        }
    }"
    x-init="
        $watch('open', () => syncNavLock());
        window.addEventListener('resize', () => {
            if (window.matchMedia('(min-width: 1280px)').matches) open = false;
            syncNavLock();
        });
    "
    @keydown.escape.window="open=false"
    class="site-header sticky top-0 z-40"
>
    <div class="site-header-accent" aria-hidden="true"></div>

    <div class="site-header-bar w-full flex items-center gap-3 sm:gap-5 lg:gap-6 px-4 sm:px-6 lg:px-8 xl:px-10 py-3.5">
        <a href="{{ $dashboardUrl }}" class="site-header-brand inline-flex items-center gap-2.5 sm:gap-3 shrink-0" aria-label="{{ $brand }} — Dashboard">
            @if($brandLogoUrl ?? null)
                <img src="{{ $brandLogoUrl }}" alt="{{ $brand }}" class="h-11 sm:h-12 w-auto max-w-[88px] object-contain shrink-0">
            @endif
            <span class="leading-tight">
                <span class="block font-serif text-[0.95rem] sm:text-lg font-semibold tracking-tight text-ink">{{ $brand }}</span>
                @if($tagline)
                    <span class="block text-[0.7rem] sm:text-xs tracking-[0.14em] uppercase text-ink-muted mt-0.5">{{ $tagline }}</span>
                @endif
            </span>
        </a>

        <nav class="site-header-nav hidden xl:flex flex-1 items-center justify-center min-w-0" aria-label="Member">
            @foreach($links as $l)
                <a
                    href="{{ $l['url'] }}"
                    @class([
                        'site-header-link',
                        'is-active' => $l['active'],
                    ])
                >{{ $l['label'] }}</a>
            @endforeach
        </nav>

        <div class="site-header-actions hidden xl:flex items-center shrink-0">
            <div x-data="{menu:false}" @keydown.escape.window="menu=false" class="relative">
                <button
                    type="button"
                    @click="menu=!menu"
                    :aria-expanded="menu"
                    aria-haspopup="menu"
                    class="site-header-text-link inline-flex items-center gap-2"
                >
                    @if($user?->avatarUrl())
                        <img src="{{ $user->avatarUrl() }}" alt="" class="w-8 h-8 rounded-full object-cover shrink-0">
                    @else
                        <span class="w-8 h-8 rounded-full bg-brand-primary text-white flex items-center justify-center text-xs font-semibold">{{ $initials }}</span>
                    @endif
                    <span class="max-w-[9rem] truncate">{{ $displayName }}</span>
                    <svg class="w-3 h-3 opacity-60" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M5.25 7.5 10 12.25 14.75 7.5z"/></svg>
                </button>
                <div
                    x-show="menu"
                    x-cloak
                    @click.away="menu=false"
                    x-transition.opacity
                    role="menu"
                    class="absolute right-0 mt-2 w-48 rounded-xl border border-[rgb(var(--border))] bg-white p-1.5 text-sm shadow-lg z-50"
                >
                    <a href="{{ $profileUrl }}" role="menuitem" class="block px-3 py-2 rounded-lg hover:bg-surface">My profile</a>
                    <a href="{{ $siteHomeUrl }}" role="menuitem" class="block px-3 py-2 rounded-lg hover:bg-surface">Website</a>
                    @if($hasLogout)
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button type="submit" class="w-full text-left px-3 py-2 rounded-lg hover:bg-surface text-brand-primary">Sign out</button>
                        </form>
                    @endif
                </div>
            </div>

            @if($donateUrl && $donateUrl !== '#')
                <a href="{{ $donateUrl }}" class="site-header-cta-ghost">{{ $donateLabel }}</a>
            @endif
        </div>

        <button
            type="button"
            class="xl:hidden p-2 text-ink shrink-0 ml-auto rounded-lg hover:bg-white/70 transition-colors"
            @click="open=!open"
            :aria-expanded="open"
            aria-controls="member-mobile-nav"
            aria-label="Menu"
        >
            <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Mobile side drawer --}}
    <template x-teleport="body">
        <div class="xl:hidden">
            <div
                x-show="open"
                x-cloak
                x-transition:enter="transition-opacity ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="site-mobile-nav-backdrop"
                @click="open=false"
                aria-hidden="true"
            ></div>

            <div
                id="member-mobile-nav"
                x-show="open"
                x-cloak
                role="dialog"
                aria-modal="true"
                aria-label="Member menu"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="site-mobile-nav-drawer"
            >
                <div class="site-mobile-nav-drawer-head">
                    <a href="{{ $dashboardUrl }}" @click="open=false" class="site-mobile-nav-drawer-brand" aria-label="{{ $brand }} — Dashboard">
                        @if($brandLogoUrl ?? null)
                            <img src="{{ $brandLogoUrl }}" alt="" class="h-9 w-auto max-w-[64px] object-contain shrink-0">
                        @endif
                        <span class="leading-tight min-w-0">
                            <span class="block font-serif text-sm font-semibold tracking-tight text-ink truncate">{{ $brand }}</span>
                            @if($tagline)
                                <span class="block text-[0.65rem] tracking-[0.12em] uppercase text-ink-muted mt-0.5 truncate">{{ $tagline }}</span>
                            @endif
                        </span>
                    </a>
                    <button
                        type="button"
                        @click="open=false"
                        class="site-mobile-nav-drawer-close"
                        aria-label="Close menu"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="site-mobile-nav-drawer-body">
                    <nav class="site-mobile-nav-grid" aria-label="Member">
                        @foreach($links as $l)
                            <a
                                href="{{ $l['url'] }}"
                                @click="open=false"
                                @class([
                                    'site-mobile-nav-chip',
                                    'border-[rgb(var(--brand-primary)/0.25)] bg-[rgb(var(--brand-primary)/0.06)] text-brand-primary' => $l['active'],
                                ])
                            >{{ $l['label'] }}</a>
                        @endforeach
                        <a href="{{ $profileUrl }}" @click="open=false" class="site-mobile-nav-chip">My profile</a>
                        <a href="{{ $siteHomeUrl }}" @click="open=false" class="site-mobile-nav-chip">Website</a>
                        @if($hasLogout)
                            <form method="POST" action="{{ route('logout') }}">@csrf
                                <button type="submit" class="site-mobile-nav-chip w-full text-left text-brand-primary">Sign out</button>
                            </form>
                        @endif
                    </nav>
                </div>

                @if($donateUrl && $donateUrl !== '#')
                    <div class="site-mobile-nav-drawer-foot">
                        <a href="{{ $donateUrl }}" @click="open=false" class="btn-primary shine-btn glow-primary w-full text-sm justify-center">
                            <span>{{ $donateLabel }}</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </template>
</header>
