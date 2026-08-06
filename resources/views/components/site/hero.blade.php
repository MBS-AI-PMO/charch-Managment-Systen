@props(['image'=>null,'heading'=>'','sub'=>null,'primaryCta'=>null,'secondaryCta'=>null,'eyebrow'=>null,'size'=>'lg'])
@php
    $padding = $size === 'sm' ? 'py-12 md:py-20' : 'py-20 md:py-36';
    $crossSize = $size === 'sm' ? 'w-40 md:w-56' : 'w-56 md:w-80 lg:w-[26rem]';
@endphp

<section
    class="hero-stage relative isolate overflow-hidden"
    x-data="{ y: 0 }"
    x-init="
        const update = () => {
            const r = $el.getBoundingClientRect();
            if (r.bottom < 0 || r.top > window.innerHeight) return;
            y = Math.max(0, window.scrollY) * 0.35;
            $refs.bg.style.setProperty('--parallax', y + 'px');
        };
        update();
        window.addEventListener('scroll', () => requestAnimationFrame(update), { passive: true });
    "
>
    @if($image)
        <div x-ref="bg" class="hero-bg-layer kenburns absolute inset-0 -top-16 -bottom-16">
            <img src="{{ $image }}" alt="" class="w-full h-full object-cover">
        </div>
    @else
        <div x-ref="bg" class="hero-bg-layer kenburns absolute inset-0 -top-16 -bottom-16 bg-gradient-to-br from-brand-primary to-ink"></div>
    @endif

    {{-- gradient overlay --}}
    <div class="absolute inset-0 bg-gradient-to-r from-black/75 via-black/45 to-transparent"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>

    {{-- Decorative animated cross --}}
    <svg class="hero-cross {{ $crossSize }} right-4 md:right-16 top-1/2 -translate-y-1/2 opacity-80"
         viewBox="0 0 100 160" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <defs>
            <linearGradient id="crossGold" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%"   stop-color="#E5C879"/>
                <stop offset="50%"  stop-color="#C9A961"/>
                <stop offset="100%" stop-color="#8E743A"/>
            </linearGradient>
            <linearGradient id="crossHighlight" x1="0" y1="0" x2="1" y2="0">
                <stop offset="0%"   stop-color="rgba(255,255,255,0)"/>
                <stop offset="50%"  stop-color="rgba(255,255,255,0.65)"/>
                <stop offset="100%" stop-color="rgba(255,255,255,0)"/>
            </linearGradient>
        </defs>
        <path d="M40 0 L60 0 L60 55 L100 55 L100 75 L60 75 L60 160 L40 160 L40 75 L0 75 L0 55 L40 55 Z"
              fill="url(#crossGold)"
              stroke="rgba(255,255,255,0.35)" stroke-width="0.6"/>
        <path d="M40 0 L60 0 L60 55 L100 55 L100 75 L60 75 L60 160 L40 160 L40 75 L0 75 L0 55 L40 55 Z"
              fill="url(#crossHighlight)" opacity="0.35"/>
    </svg>

    <div class="relative max-w-container mx-auto px-4 {{ $padding }} text-white">
        @if($eyebrow)
            <div class="hero-anim uppercase tracking-[0.2em] text-xs text-brand-secondary font-semibold mb-4">{{ $eyebrow }}</div>
        @endif
        <h1 class="hero-anim delay-1 font-serif text-4xl md:text-6xl max-w-3xl leading-[1.05]">{{ $heading }}</h1>
        @if($sub)
            <p class="hero-anim delay-2 mt-5 text-lg md:text-xl max-w-xl text-white/85 leading-relaxed">{{ $sub }}</p>
        @endif
        <div class="hero-anim delay-3 mt-8 flex flex-wrap gap-3">
            @if($primaryCta)<a href="{{ $primaryCta['url'] }}" class="btn-primary shine-btn glow-primary">{{ $primaryCta['label'] }}</a>@endif
            @if($secondaryCta)<a href="{{ $secondaryCta['url'] }}" class="btn-ghost shine-btn text-white border-white/40 hover:bg-white/10 backdrop-blur-sm">{{ $secondaryCta['label'] }}</a>@endif
        </div>
    </div>
</section>
