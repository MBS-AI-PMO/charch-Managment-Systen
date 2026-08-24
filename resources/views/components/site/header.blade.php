@props(['nav' => []])
@php
  $homeUrl = \Illuminate\Support\Facades\Route::has('site.home') ? route('site.home') : (\Illuminate\Support\Facades\Route::has('preview.home') ? route('preview.home') : url('/'));
  $contactUrl = \Illuminate\Support\Facades\Route::has('site.contact') ? route('site.contact') : (\Illuminate\Support\Facades\Route::has('preview.contact') ? route('preview.contact') : url('/contact'));
  $donateUrl = \Illuminate\Support\Facades\Route::has('site.donate') ? route('site.donate') : null;
  $donateLabel = function_exists('settings') ? settings('donate.button_label', 'Donate') : 'Donate';
  $visitCtaLabel = function_exists('settings') ? settings('header.visit_cta_label', 'Plan your visit') : 'Plan your visit';
  $visitCtaUrl = function_exists('settings') ? settings('header.visit_cta_url', $contactUrl) : $contactUrl;
  $brand = $brandName ?? (function_exists('settings') ? settings('brand.name', 'Assemblies of God') : 'Assemblies of God');
  $tagline = $brandTagline ?? (function_exists('settings') ? settings('brand.tagline', 'Rawalpindi') : 'Rawalpindi');
  $member = auth('web')->user();
  $loginUrl = \Illuminate\Support\Facades\Route::has('login') ? route('login') : null;
  $dashboardUrl = \Illuminate\Support\Facades\Route::has('member.dashboard') ? route('member.dashboard') : null;
  $logoutUrl = \Illuminate\Support\Facades\Route::has('logout') ? route('logout') : null;

  $isActive = function (?string $url): bool {
      if (! $url || $url === '#') {
          return false;
      }
      $path = parse_url($url, PHP_URL_PATH) ?: '/';
      $current = '/'.ltrim(request()->path(), '/');
      if ($path === '/' || $path === '') {
          return $current === '/';
      }

      return $current === $path || str_starts_with($current, rtrim($path, '/').'/');
  };

  // Logo already goes home — keep nav focused on real sections.
  $navItems = collect($nav)->reject(function ($item) {
      $label = strtolower(trim((string) ($item['label'] ?? '')));
      $url = (string) ($item['url'] ?? '');

      if ($label === 'home' || $label === 'news') {
          return true;
      }

      // Broken/placeholder links resolve to "#" — don't treat them as Home.
      if ($url === '' || $url === '#') {
          return false;
      }

      $path = rtrim((string) (parse_url($url, PHP_URL_PATH) ?: ''), '/') ?: '/';

      return $path === '/';
  })->values();

  // Ensure Our Churches appears even before menus are re-seeded.
  $hasChurches = $navItems->contains(function ($item) {
      $label = strtolower(trim((string) ($item['label'] ?? '')));

      return $label === 'our churches' || $label === 'churches';
  });

  if (! $hasChurches && \Illuminate\Support\Facades\Route::has('site.churches')) {
      $navItems->push([
          'label' => 'Our Churches',
          'url' => route('site.churches', [], false),
          'target' => '_self',
          'children' => [],
      ]);
  }

  // Ensure Gallery appears even before menus are re-seeded.
  $hasGallery = $navItems->contains(function ($item) {
      $label = strtolower(trim((string) ($item['label'] ?? '')));

      return $label === 'gallery';
  });

  if (! $hasGallery && \Illuminate\Support\Facades\Route::has('site.gallery.index')) {
      $navItems->push([
          'label' => 'Gallery',
          'url' => route('site.gallery.index', [], false),
          'target' => '_self',
          'children' => [],
      ]);
  }

  // Media dropdown groups Sermons + Gallery. Events stays its own top-level tab.
  $navGroups = [
      'Media' => ['sermons', 'gallery'],
  ];

  $dropdownMeta = [
      'sermons' => ['icon' => 'sermons'],
      'gallery' => ['icon' => 'gallery'],
  ];

  $groupedNav = [];
  $bucket = [];
  foreach ($navGroups as $groupLabel => $labels) {
      $bucket[$groupLabel] = [
          'label' => $groupLabel,
          'url' => '#',
          'target' => '_self',
          'children' => [],
          '_order' => null,
      ];
  }

  foreach ($navItems as $index => $item) {
      $label = strtolower(trim((string) ($item['label'] ?? '')));
      $placed = false;

      foreach ($navGroups as $groupLabel => $labels) {
          if (in_array($label, $labels, true)) {
              if ($bucket[$groupLabel]['_order'] === null) {
                  $bucket[$groupLabel]['_order'] = $index;
              }
              $bucket[$groupLabel]['children'][] = $item;
              $placed = true;
              break;
          }
      }

      if (! $placed) {
          $groupedNav[] = $item + ['_order' => $index];
      }
  }

  foreach ($bucket as $group) {
      if (! empty($group['children'])) {
          $groupedNav[] = $group;
      }
  }

  $navItems = collect($groupedNav)
      ->sortBy('_order')
      ->map(function ($item) {
          unset($item['_order']);

          return $item;
      })
      ->values()
      ->all();
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
    <a href="{{ $homeUrl }}" class="site-header-brand inline-flex items-center gap-2.5 sm:gap-3 shrink-0" aria-label="{{ $brand }} — Home">
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

    <nav class="site-header-nav hidden xl:flex flex-1 items-center justify-center min-w-0" aria-label="Primary">
      @foreach($navItems as $item)
        @php
          $children = $item['children'] ?? [];
          $active = $isActive($item['url'] ?? null);
          if (! $active) {
              foreach ($children as $child) {
                  if ($isActive($child['url'] ?? null)) {
                      $active = true;
                      break;
                  }
              }
          }
        @endphp
        @if(!empty($children))
          <div x-data="{open:false}" @mouseenter="open=true" @mouseleave="open=false" class="site-header-drop relative">
            <button
              type="button"
              @click="open=!open"
              :aria-expanded="open"
              @class([
                'site-header-link inline-flex items-center gap-1',
                'is-active' => $active,
              ])
            >
              {{ $item['label'] }}
              <svg class="site-header-chevron w-3 h-3 opacity-60" :class="open && 'is-open'" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div
              x-show="open"
              x-cloak
              x-transition:enter="site-header-drop-enter"
              x-transition:enter-start="site-header-drop-enter-start"
              x-transition:enter-end="site-header-drop-enter-end"
              x-transition:leave="site-header-drop-leave"
              x-transition:leave-start="site-header-drop-leave-start"
              x-transition:leave-end="site-header-drop-leave-end"
              class="site-header-dropdown-wrap"
            >
              <div class="site-header-dropdown">
                @if(!empty($item['url']) && $item['url'] !== '#')
                  @php
                    $parentKey = strtolower(trim((string) $item['label']));
                    $parentMeta = $dropdownMeta[$parentKey] ?? null;
                    $parentActive = $isActive($item['url'] ?? null);
                  @endphp
                  <a href="{{ $item['url'] }}" target="{{ $item['target'] ?? '_self' }}" @class(['site-header-dropdown-link', 'is-active' => $parentActive])>
                    <span class="site-header-dropdown-icon" aria-hidden="true">
                      @include('components.site.partials.nav-dropdown-icon', ['icon' => $parentMeta['icon'] ?? 'link'])
                    </span>
                    <span class="site-header-dropdown-title">{{ $item['label'] }}</span>
                  </a>
                @endif
                @foreach($children as $child)
                  @php
                    $childKey = strtolower(trim((string) ($child['label'] ?? '')));
                    $childMeta = $dropdownMeta[$childKey] ?? null;
                    $childActive = $isActive($child['url'] ?? null);
                  @endphp
                  <a href="{{ $child['url'] }}" target="{{ $child['target'] ?? '_self' }}" @class(['site-header-dropdown-link', 'is-active' => $childActive])>
                    <span class="site-header-dropdown-icon" aria-hidden="true">
                      @include('components.site.partials.nav-dropdown-icon', ['icon' => $childMeta['icon'] ?? 'link'])
                    </span>
                    <span class="site-header-dropdown-title">{{ $child['label'] }}</span>
                  </a>
                @endforeach
              </div>
            </div>
          </div>
        @else
          <a
            href="{{ $item['url'] }}"
            target="{{ $item['target'] ?? '_self' }}"
            @class(['site-header-link', 'is-active' => $active])
          >{{ $item['label'] }}</a>
        @endif
      @endforeach
    </nav>

    <div class="site-header-actions hidden xl:flex items-center shrink-0">
      @if($member && $dashboardUrl)
        <a href="{{ $dashboardUrl }}" class="site-header-text-link">My account</a>
      @elseif($loginUrl)
        <a href="{{ $loginUrl }}" class="site-header-text-link">Sign in</a>
      @endif

      @if($donateUrl)
        <a href="{{ $donateUrl }}" class="site-header-cta-ghost">{{ $donateLabel }}</a>
      @endif

      <a href="{{ $visitCtaUrl ?: $contactUrl }}" class="site-header-cta-primary shine-btn glow-primary">
        <span>{{ $visitCtaLabel }}</span>
      </a>
    </div>

    <button
      type="button"
      @click="open=!open"
      :aria-expanded="open"
      aria-controls="site-mobile-nav"
      class="xl:hidden p-2 text-ink shrink-0 ml-auto rounded-lg hover:bg-white/70 transition-colors"
      aria-label="Menu"
    >
      <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
      <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
  </div>

  {{-- Mobile side drawer (teleported so header backdrop-filter doesn't trap fixed positioning) --}}
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
        id="site-mobile-nav"
        x-show="open"
        x-cloak
        role="dialog"
        aria-modal="true"
        aria-label="Menu"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="site-mobile-nav-drawer"
      >
        <div class="site-mobile-nav-drawer-head">
          <a href="{{ $homeUrl }}" @click="open=false" class="site-mobile-nav-drawer-brand" aria-label="{{ $brand }} — Home">
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
          <nav class="site-mobile-nav-grid" aria-label="Mobile">
            @forelse($navItems as $item)
              @php $children = $item['children'] ?? []; @endphp
              @if(!empty($children))
                <details class="site-mobile-nav-details site-mobile-nav-span">
                  <summary class="site-mobile-nav-summary site-mobile-nav-chip">
                    <span>{{ $item['label'] }}</span>
                    <svg class="site-mobile-nav-chevron h-3.5 w-3.5 shrink-0 text-ink-muted transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                  </summary>
                  <div class="site-mobile-nav-sub">
                    @if(!empty($item['url']) && $item['url'] !== '#')
                      <a href="{{ $item['url'] }}" target="{{ $item['target'] ?? '_self' }}" @click="open=false" class="site-mobile-nav-sub-link">{{ $item['label'] }}</a>
                    @endif
                    @foreach($children as $child)
                      <a href="{{ $child['url'] }}" target="{{ $child['target'] ?? '_self' }}" @click="open=false" class="site-mobile-nav-sub-link">{{ $child['label'] }}</a>
                    @endforeach
                  </div>
                </details>
              @else
                <a href="{{ $item['url'] }}" target="{{ $item['target'] ?? '_self' }}" @click="open=false" class="site-mobile-nav-chip">{{ $item['label'] }}</a>
              @endif
            @empty
              <p class="col-span-2 py-3 text-sm text-ink-muted">No menu items yet.</p>
            @endforelse

            @if($member && $dashboardUrl)
              <a href="{{ $dashboardUrl }}" @click="open=false" class="site-mobile-nav-chip">My account</a>
              @if($logoutUrl)
                <form method="POST" action="{{ $logoutUrl }}">@csrf
                  <button type="submit" class="site-mobile-nav-chip w-full text-left">Sign out</button>
                </form>
              @endif
            @elseif($loginUrl)
              <a href="{{ $loginUrl }}" @click="open=false" class="site-mobile-nav-chip">Sign in</a>
            @endif
          </nav>
        </div>

        <div class="site-mobile-nav-drawer-foot">
          @if($donateUrl)
            <a href="{{ $donateUrl }}" @click="open=false" class="btn-ghost w-full text-sm justify-center">{{ $donateLabel }}</a>
          @endif
          <a href="{{ $visitCtaUrl ?: $contactUrl }}" @click="open=false" class="btn-primary shine-btn glow-primary w-full text-sm justify-center"><span>{{ $visitCtaLabel }}</span></a>
        </div>
      </div>
    </div>
  </template>

  <x-site.ask-question />
</header>
