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
    ['url' => $pick('member.prayer.index','preview.member.prayer'),   'active' => $activeReal('member.prayer.*',   'preview.member.prayer*'),    'label' => 'Prayer'],
    ['url' => $pick('member.care.index', 'preview.member.care'),      'active' => $activeReal('member.care.*',     'preview.member.care*'),      'label' => 'Knock'],
    ['url' => Route::has('member.questions.index') ? route('member.questions.index') : '#', 'active' => request()->routeIs('member.questions.*'), 'label' => 'Questions'],
    ['url' => $pick('member.feed.index', 'preview.member.feed'),      'active' => $activeReal('member.feed.*',     'preview.member.feed'),       'label' => 'Feed'],
    ['url' => Route::has('site.donate') ? route('site.donate') : $pick('member.giving', 'preview.member.donate'), 'active' => false, 'label' => settings('donate.button_label', 'Give')],
];

if ($attendanceOpenToday && \Illuminate\Support\Facades\Route::has('member.checkin.show')) {
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
@endphp
<header
    x-data="{open:false}"
    @keydown.escape.window="open=false"
    @click.away="open=false"
    class="sticky top-0 z-40 bg-surface/95 backdrop-blur border-b border-[rgb(var(--border))]"
>
    <div class="max-w-container mx-auto flex items-center gap-4 sm:gap-8 lg:gap-12 px-4 sm:px-6 py-3">
        <a href="{{ $dashboardUrl }}" class="inline-flex items-center gap-2.5 sm:gap-3.5 shrink min-w-0 pr-2 lg:pr-4">
            @if($brandLogoUrl ?? null)
                <img src="{{ $brandLogoUrl }}" alt="{{ $brand }}" class="h-10 sm:h-12 md:h-14 w-auto max-w-[100px] sm:max-w-[160px] object-contain shrink-0">
            @endif
            <span class="leading-tight min-w-0">
                <span class="block font-serif text-sm sm:text-base md:text-lg font-semibold tracking-tight truncate">{{ $brand }}</span>
                @if($tagline)
                    <span class="block text-xs text-ink-muted mt-0.5 truncate">{{ $tagline }}</span>
                @endif
            </span>
        </a>

        <nav class="hidden xl:flex flex-1 items-center justify-end gap-4 xl:gap-6 min-w-0">
            @foreach($links as $l)
                <a href="{{ $l['url'] }}"
                   @class([
                        'text-[15px] transition-colors whitespace-nowrap',
                        'text-brand-primary font-medium' => $l['active'],
                        'text-ink hover:text-brand-primary' => ! $l['active'],
                   ])>{{ $l['label'] }}</a>
            @endforeach
        </nav>

        <div class="hidden xl:flex items-center gap-3 shrink-0">
            <div x-data="{open:false}" @keydown.escape.window="open=false" class="relative">
                <button @click="open=!open" :aria-expanded="open" aria-haspopup="menu"
                        class="flex items-center gap-2 text-sm pl-2 pr-3 py-1.5 rounded-md hover:bg-white">
                    <span class="w-8 h-8 rounded-full bg-brand-primary text-white flex items-center justify-center text-xs font-semibold">{{ $initials }}</span>
                    <span class="max-w-[10rem] truncate">{{ $displayName }}</span>
                    <svg class="w-3 h-3 text-ink-muted" viewBox="0 0 20 20" fill="currentColor"><path d="M5.25 7.5 10 12.25 14.75 7.5z"/></svg>
                </button>
                <div x-show="open" x-cloak @click.away="open=false" x-transition.opacity
                     role="menu"
                     class="absolute right-0 mt-2 w-48 card p-1.5 text-sm z-50 bg-white">
                    <a href="{{ $profileUrl }}" role="menuitem" class="block px-3 py-2 hover:bg-surface rounded">My profile</a>
                    @if($hasLogout)
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button type="submit" class="w-full text-left px-3 py-2 hover:bg-surface rounded text-brand-primary">Sign out</button>
                        </form>
                    @else
                        <a href="#" role="menuitem" class="block px-3 py-2 hover:bg-surface rounded text-brand-primary">Sign out</a>
                    @endif
                </div>
            </div>
        </div>

        <button
            type="button"
            class="xl:hidden p-2 ml-auto shrink-0 rounded-md hover:bg-white"
            @click="open=!open"
            :aria-expanded="open"
            aria-haspopup="menu"
            aria-label="Open menu"
        >
            <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Compact dropdown under header — does not cover the whole screen --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="xl:hidden border-t border-[rgb(var(--border))] bg-surface"
        role="menu"
    >
        <div class="max-w-container mx-auto px-3 sm:px-4 py-3 max-h-[min(70vh,26rem)] overflow-y-auto overscroll-contain">
            <nav class="site-mobile-nav-grid" aria-label="Member">
                @foreach($links as $l)
                    <a
                        href="{{ $l['url'] }}"
                        role="menuitem"
                        @click="open=false"
                        @class([
                            'site-mobile-nav-chip',
                            'border-[rgb(var(--brand-primary)/0.25)] bg-[rgb(var(--brand-primary)/0.06)] text-brand-primary' => $l['active'],
                        ])
                    >{{ $l['label'] }}</a>
                @endforeach
                <a href="{{ $profileUrl }}" role="menuitem" @click="open=false" class="site-mobile-nav-chip">My profile</a>
                @if($hasLogout)
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button type="submit" class="site-mobile-nav-chip w-full text-left text-brand-primary">Sign out</button>
                    </form>
                @endif
            </nav>
        </div>
    </div>
</header>
