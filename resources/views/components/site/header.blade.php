@props(['nav' => []])
@php
  $homeUrl = \Illuminate\Support\Facades\Route::has('site.home') ? route('site.home') : (\Illuminate\Support\Facades\Route::has('preview.home') ? route('preview.home') : url('/'));
  $contactUrl = \Illuminate\Support\Facades\Route::has('site.contact') ? route('site.contact') : (\Illuminate\Support\Facades\Route::has('preview.contact') ? route('preview.contact') : url('/contact'));
  $donateUrl = \Illuminate\Support\Facades\Route::has('site.donate') ? route('site.donate') : null;
  $donateLabel = function_exists('settings') ? settings('donate.button_label', 'Donation') : 'Donation';
  $brand = $brandName ?? (function_exists('settings') ? settings('brand.name', 'Assemblies of God') : 'Assemblies of God');
  $tagline = $brandTagline ?? (function_exists('settings') ? settings('brand.tagline', 'Rawalpindi') : 'Rawalpindi');
  $member = auth('web')->user();
  $loginUrl = \Illuminate\Support\Facades\Route::has('login') ? route('login') : null;
  $dashboardUrl = \Illuminate\Support\Facades\Route::has('member.dashboard') ? route('member.dashboard') : null;
  $logoutUrl = \Illuminate\Support\Facades\Route::has('logout') ? route('logout') : null;
@endphp
<header
  x-data="{open:false}"
  @keydown.escape.window="open=false"
  @click.away="open=false"
  class="sticky top-0 z-40 bg-surface/95 backdrop-blur border-b border-[rgb(var(--border))]"
>
  <div class="site-header-bar max-w-container mx-auto flex items-center px-4 sm:px-6 py-3">
    <a href="{{ $homeUrl }}" class="site-header-brand inline-flex items-center gap-2 sm:gap-3 shrink min-w-0">
      @if($brandLogoUrl ?? null)
        <img src="{{ $brandLogoUrl }}" alt="{{ $brand }}" class="h-10 sm:h-12 md:h-14 w-auto max-w-[100px] sm:max-w-[140px] object-contain shrink-0">
      @endif
      <span class="leading-tight min-w-0">
        <span class="block font-serif text-sm sm:text-base md:text-lg font-semibold tracking-tight text-ink truncate">{{ $brand }}</span>
        @if($tagline)
          <span class="block text-xs text-ink-muted mt-0.5 truncate">{{ $tagline }}</span>
        @endif
      </span>
    </a>

    <nav class="site-header-nav hidden xl:flex flex-1 items-center justify-evenly min-w-0 mx-3 xl:mx-5">
      @foreach($nav as $item)
        @php $children = $item['children'] ?? []; @endphp
        @if(!empty($children))
          <div x-data="{open:false}" @mouseenter="open=true" @mouseleave="open=false" class="relative shrink-0 px-1.5 xl:px-2">
            <button type="button" @click="open=!open" :aria-expanded="open" class="text-ink hover:text-brand-primary text-[15px] transition-colors inline-flex items-center gap-1 whitespace-nowrap">
              {{ $item['label'] }}
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-cloak x-transition.opacity class="absolute left-0 top-full pt-2 min-w-48 z-50">
              <div class="bg-surface border border-[rgb(var(--border))] rounded-lg shadow-lg py-1.5">
                @if(!empty($item['url']) && $item['url'] !== '#')
                  <a href="{{ $item['url'] }}" target="{{ $item['target'] ?? '_self' }}" class="block px-4 py-2 text-[14px] text-ink hover:text-brand-primary hover:bg-[rgb(var(--surface-2,245,245,245))] transition-colors">{{ $item['label'] }}</a>
                @endif
                @foreach($children as $child)
                  <a href="{{ $child['url'] }}" target="{{ $child['target'] ?? '_self' }}" class="block px-4 py-2 text-[14px] text-ink hover:text-brand-primary hover:bg-[rgb(var(--surface-2,245,245,245))] transition-colors">{{ $child['label'] }}</a>
                @endforeach
              </div>
            </div>
          </div>
        @else
          <a href="{{ $item['url'] }}" target="{{ $item['target'] ?? '_self' }}" class="site-header-link text-ink hover:text-brand-primary text-[15px] whitespace-nowrap shrink-0 transition-colors px-1.5 xl:px-2">{{ $item['label'] }}</a>
        @endif
      @endforeach
    </nav>

    <div class="site-header-actions hidden xl:flex items-center shrink-0 gap-3 pl-2">
      @if($member && $dashboardUrl)
        <a href="{{ $dashboardUrl }}" class="text-ink hover:text-brand-primary text-[15px] whitespace-nowrap transition-colors">My account</a>
      @elseif($loginUrl)
        <a href="{{ $loginUrl }}" class="text-ink hover:text-brand-primary text-[15px] whitespace-nowrap transition-colors">Sign in</a>
      @endif
      <button type="button" @click="$dispatch('open-ask-question')" class="btn-primary shine-btn glow-primary text-sm px-4 py-2">
        <span>Ask a question</span>
      </button>
      @if($donateUrl)
        <a href="{{ $donateUrl }}" class="btn-primary shine-btn glow-primary text-sm px-4 py-2"><span>{{ $donateLabel }}</span></a>
      @endif
      <a href="{{ $contactUrl }}" class="btn-primary shine-btn glow-primary text-sm px-4 py-2"><span>Plan your visit</span></a>
    </div>

    <button
      type="button"
      @click="open=!open"
      :aria-expanded="open"
      aria-controls="site-mobile-nav"
      class="xl:hidden p-2 text-ink shrink-0 ml-auto rounded-md hover:bg-[rgb(var(--border)/0.55)]"
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

  {{-- Compact dropdown under header — does not cover the whole screen --}}
  <div
    id="site-mobile-nav"
    x-show="open"
    x-cloak
    x-transition:enter="transition ease-out duration-150"
    x-transition:enter-start="opacity-0 -translate-y-1"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 -translate-y-1"
    class="xl:hidden border-t border-[rgb(var(--border))] bg-surface"
  >
    <div class="max-w-container mx-auto px-3 sm:px-4 py-3 max-h-[min(70vh,26rem)] overflow-y-auto overscroll-contain">
      <nav class="site-mobile-nav-grid" aria-label="Mobile">
        @forelse($nav as $item)
          @php $children = $item['children'] ?? []; @endphp
          @if(!empty($children))
            <details class="site-mobile-nav-details site-mobile-nav-span">
              <summary class="site-mobile-nav-summary site-mobile-nav-chip">
                <span>{{ $item['label'] }}</span>
                <svg class="site-mobile-nav-chevron h-3.5 w-3.5 shrink-0 text-ink-muted transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </summary>
              <div class="site-mobile-nav-sub">
                @if(!empty($item['url']) && $item['url'] !== '#')
                  <a href="{{ $item['url'] }}" target="{{ $item['target'] ?? '_self' }}" @click="open=false">{{ $item['label'] }}</a>
                @endif
                @foreach($children as $child)
                  <a href="{{ $child['url'] }}" target="{{ $child['target'] ?? '_self' }}" @click="open=false">{{ $child['label'] }}</a>
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

      <div class="mt-3 flex flex-wrap gap-2 border-t border-[rgb(var(--border))] pt-3">
        <button
          type="button"
          @click="open=false; $dispatch('open-ask-question')"
          class="btn-primary shine-btn glow-primary text-xs px-3 py-2"
        ><span>Ask a question</span></button>
        @if($donateUrl)
          <a href="{{ $donateUrl }}" @click="open=false" class="btn-primary shine-btn glow-primary text-xs px-3 py-2"><span>{{ $donateLabel }}</span></a>
        @endif
        <a href="{{ $contactUrl }}" @click="open=false" class="btn-ghost text-xs px-3 py-2"><span>Plan your visit</span></a>
      </div>
    </div>
  </div>

  <x-site.ask-question />
</header>
