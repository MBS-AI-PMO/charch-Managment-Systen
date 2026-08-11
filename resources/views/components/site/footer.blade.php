@props(['address'=>'','phone'=>'','email'=>'','services'=>''])
@php
  $brand = $brandName ?? (function_exists('settings') ? settings('brand.name', 'Assemblies of God') : 'Assemblies of God');
  $tagline = $brandTagline ?? (function_exists('settings') ? settings('brand.tagline', 'Rawalpindi') : 'Rawalpindi');
  $copyright = function_exists('settings') ? settings('footer.copyright') : null;
  $about = function_exists('settings')
    ? settings('footer.about', 'A welcoming community for everyone — wherever you are on the journey.')
    : 'A welcoming community for everyone — wherever you are on the journey.';
@endphp
<footer class="site-footer bg-ink text-white">
  <div class="site-footer-accent" aria-hidden="true"></div>

  <div class="max-w-container mx-auto px-4 sm:px-6 pt-10 md:pt-14 pb-8 md:pb-12">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-8 lg:gap-8">
      {{-- Brand --}}
      <div class="sm:col-span-2 lg:col-span-4">
        <a href="{{ \Illuminate\Support\Facades\Route::has('site.home') ? route('site.home') : url('/') }}" class="inline-flex items-center gap-3 group">
          @if($brandLogoUrl ?? null)
            <img src="{{ $brandLogoUrl }}" alt="{{ $brand }}" class="h-12 w-auto max-w-[110px] object-contain shrink-0">
          @endif
          <span>
            <span class="block font-serif text-xl md:text-2xl leading-tight">{{ $brand }}</span>
            @if($tagline)
              <span class="block text-white/50 text-xs tracking-[0.14em] uppercase mt-1 group-hover:text-brand-secondary transition-colors">{{ $tagline }}</span>
            @endif
          </span>
        </a>
        <p class="mt-5 text-white/60 text-sm leading-relaxed max-w-sm">{{ $about }}</p>
        @if($socials ?? false)
          <div class="flex flex-wrap gap-2.5 mt-6">{{ $socials }}</div>
        @endif
      </div>

      {{-- Explore --}}
      <div class="lg:col-span-3 lg:pl-2">
        <h4 class="site-footer-heading">Explore</h4>
        <ul class="site-footer-links columns-1 sm:columns-2 lg:columns-1 gap-x-8">
          {{ $links ?? '' }}
        </ul>
      </div>

      {{-- Services --}}
      <div class="lg:col-span-2">
        <h4 class="site-footer-heading">Services</h4>
        @if($services)
          <div class="flex gap-3 text-sm text-white/65 leading-relaxed group/item">
            <svg class="w-4 h-4 mt-0.5 shrink-0 text-white/40 group-hover/item:text-brand-secondary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="whitespace-pre-line">{{ $services }}</p>
          </div>
        @endif
        @if($address)
          <div class="flex gap-3 text-sm text-white/65 leading-relaxed {{ $services ? 'mt-4' : '' }} group/item">
            <svg class="w-4 h-4 mt-0.5 shrink-0 text-white/40 group-hover/item:text-brand-secondary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
            </svg>
            <p>{{ $address }}</p>
          </div>
        @endif
      </div>

      {{-- Connect --}}
      <div class="lg:col-span-3 lg:pl-2">
        <h4 class="site-footer-heading">Connect</h4>
        <ul class="space-y-3.5">
          @if($phone)
            <li>
              <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}" class="inline-flex items-start gap-3 text-sm text-white/65 hover:text-brand-secondary transition-colors group/link">
                <svg class="w-4 h-4 mt-0.5 shrink-0 text-white/40 group-hover/link:text-brand-secondary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                </svg>
                <span>{{ $phone }}</span>
              </a>
            </li>
          @endif
          @if($email)
            <li>
              <a href="mailto:{{ $email }}" class="inline-flex items-start gap-3 text-sm text-white/65 hover:text-brand-secondary transition-colors break-all group/link">
                <svg class="w-4 h-4 mt-0.5 shrink-0 text-white/40 group-hover/link:text-brand-secondary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                </svg>
                <span>{{ $email }}</span>
              </a>
            </li>
          @endif
        </ul>

        @if(\Illuminate\Support\Facades\Route::has('site.contact') || \Illuminate\Support\Facades\Route::has('site.donate'))
          <div class="flex flex-wrap gap-2.5 mt-6">
            @if(\Illuminate\Support\Facades\Route::has('site.contact'))
              <a href="{{ route('site.contact') }}" class="inline-flex items-center rounded-lg border border-white/15 bg-white/5 px-3.5 py-2 text-xs font-medium text-white/80 hover:border-brand-secondary hover:text-brand-secondary transition-colors">
                Plan a visit
              </a>
            @endif
            @if(\Illuminate\Support\Facades\Route::has('site.donate'))
              <a href="{{ route('site.donate') }}" class="inline-flex items-center rounded-lg border border-white/15 bg-white/5 px-3.5 py-2 text-xs font-medium text-white/80 hover:bg-brand-secondary hover:border-brand-secondary hover:text-ink transition-colors">
                {{ function_exists('settings') ? settings('donate.button_label', 'Donate') : 'Donate' }}
              </a>
            @endif
          </div>
        @endif
      </div>
    </div>
  </div>

  <div class="border-t border-white/10">
    <div class="max-w-container mx-auto px-4 sm:px-6 py-5 text-center">
      <p class="text-white/40 text-xs leading-relaxed">
        @if($copyright)
          {{ $copyright }}
        @else
          &copy; {{ date('Y') }} {{ $brand }}{{ $tagline ? ', '.$tagline : '' }}
        @endif
        <span class="text-white/25 mx-2">·</span>
        Built with care
      </p>
    </div>
  </div>
</footer>
