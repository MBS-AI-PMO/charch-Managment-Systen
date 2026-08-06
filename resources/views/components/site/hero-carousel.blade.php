@props(['slides' => collect()])
@php
    $count = $slides->count();
@endphp

<section
    class="hero-carousel hero-stage relative isolate overflow-hidden"
    x-data="heroCarousel({ count: {{ $count }}, interval: 6000 })"
    x-init="init()"
    @mouseenter="pause()"
    @mouseleave="resume()"
    role="region"
    aria-roledescription="carousel"
    aria-label="Featured highlights"
>
    @foreach($slides as $i => $slide)
        @php
            $heroImg = site_img($slide->image_path, 'hero-slide-'.$slide->id, 1800, 900);
        @endphp
        <div
            class="hero-slide absolute inset-0"
            x-show="currentSlide === {{ $i }}"
            x-transition:enter="transition ease-out duration-700"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-700"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-data="{ y: 0 }"
            x-init="
                const update = () => {
                    const r = $el.getBoundingClientRect();
                    if (r.bottom < 0 || r.top > window.innerHeight) return;
                    y = Math.max(0, window.scrollY) * 0.35;
                    if ($refs.bg) $refs.bg.style.setProperty('--parallax', y + 'px');
                };
                update();
                window.addEventListener('scroll', () => requestAnimationFrame(update), { passive: true });
            "
            :aria-hidden="currentSlide !== {{ $i }}"
        >
            <div x-ref="bg" class="hero-bg-layer kenburns absolute inset-0 -top-16 -bottom-16">
                <img src="{{ $heroImg }}" alt="" class="w-full h-full object-cover">
            </div>

            {{-- gradient overlay --}}
            <div class="absolute inset-0 bg-gradient-to-r from-black/75 via-black/45 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>

            {{-- Decorative animated cross --}}
            <svg class="hero-cross w-56 md:w-80 lg:w-[26rem] right-4 md:right-16 top-1/2 -translate-y-1/2 opacity-80"
                 viewBox="0 0 100 160" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <defs>
                    <linearGradient id="crossGold-{{ $i }}" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%"   stop-color="#E5C879"/>
                        <stop offset="50%"  stop-color="#C9A961"/>
                        <stop offset="100%" stop-color="#8E743A"/>
                    </linearGradient>
                    <linearGradient id="crossHighlight-{{ $i }}" x1="0" y1="0" x2="1" y2="0">
                        <stop offset="0%"   stop-color="rgba(255,255,255,0)"/>
                        <stop offset="50%"  stop-color="rgba(255,255,255,0.65)"/>
                        <stop offset="100%" stop-color="rgba(255,255,255,0)"/>
                    </linearGradient>
                </defs>
                <path d="M40 0 L60 0 L60 55 L100 55 L100 75 L60 75 L60 160 L40 160 L40 75 L0 75 L0 55 L40 55 Z"
                      fill="url(#crossGold-{{ $i }})"
                      stroke="rgba(255,255,255,0.35)" stroke-width="0.6"/>
                <path d="M40 0 L60 0 L60 55 L100 55 L100 75 L60 75 L60 160 L40 160 L40 75 L0 75 L0 55 L40 55 Z"
                      fill="url(#crossHighlight-{{ $i }})" opacity="0.35"/>
            </svg>

            <div class="relative max-w-container mx-auto px-4 py-20 md:py-36 text-white">
                @if($slide->eyebrow)
                    <div class="hero-anim uppercase tracking-[0.2em] text-xs text-brand-secondary font-semibold mb-4">{{ $slide->eyebrow }}</div>
                @endif
                <h1 class="hero-anim delay-1 font-serif text-4xl md:text-6xl max-w-3xl leading-[1.05]">{{ $slide->heading }}</h1>
                @if($slide->sub)
                    <p class="hero-anim delay-2 mt-5 text-lg md:text-xl max-w-xl text-white/85 leading-relaxed">{{ $slide->sub }}</p>
                @endif
                <div class="hero-anim delay-3 mt-8 flex flex-wrap gap-3">
                    @if($slide->primary_cta_url && $slide->primary_cta_label)
                        <a href="{{ $slide->primary_cta_url }}" class="btn-primary shine-btn glow-primary">{{ $slide->primary_cta_label }}</a>
                    @endif
                    @if($slide->secondary_cta_url && $slide->secondary_cta_label)
                        <a href="{{ $slide->secondary_cta_url }}" class="btn-ghost shine-btn text-white border-white/40 hover:bg-white/10 backdrop-blur-sm">{{ $slide->secondary_cta_label }}</a>
                    @endif
                </div>
            </div>
        </div>
    @endforeach

    {{-- Spacer to give the absolutely-positioned slides a stage height --}}
    <div class="hero-carousel-spacer relative invisible pointer-events-none py-20 md:py-36 px-4 max-w-container mx-auto">
        <div class="hero-anim uppercase tracking-[0.2em] text-xs font-semibold mb-4">spacer</div>
        <h1 class="font-serif text-4xl md:text-6xl max-w-3xl leading-[1.05]">{{ $slides->first()?->heading ?? 'Spacer' }}</h1>
        <p class="mt-5 text-lg md:text-xl max-w-xl leading-relaxed">{{ $slides->first()?->sub ?? '' }}</p>
        <div class="mt-8 flex flex-wrap gap-3"><span class="btn-primary">x</span></div>
    </div>

    @if($count > 1)
        {{-- Prev / Next arrows --}}
        <button type="button"
                @click="prev()"
                class="hero-arrow hero-arrow-prev"
                aria-label="Previous slide">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-6 h-6">
                <path d="M15 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <button type="button"
                @click="next()"
                class="hero-arrow hero-arrow-next"
                aria-label="Next slide">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-6 h-6">
                <path d="M9 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

        {{-- Dots --}}
        <div class="hero-dots" role="tablist" aria-label="Slide selector">
            @foreach($slides as $i => $slide)
                <button type="button"
                        @click="goTo({{ $i }})"
                        :class="currentSlide === {{ $i }} ? 'hero-dot is-active' : 'hero-dot'"
                        role="tab"
                        :aria-selected="currentSlide === {{ $i }}"
                        aria-label="Go to slide {{ $i + 1 }}"></button>
            @endforeach
        </div>
    @endif
</section>

<script>
    if (typeof window.heroCarousel === 'undefined') {
        window.heroCarousel = function ({ count, interval }) {
            return {
                currentSlide: 0,
                count: count,
                interval: interval,
                timer: null,
                paused: false,
                reducedMotion: false,
                init() {
                    this.reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                    if (this.count > 1 && !this.reducedMotion) {
                        this.start();
                    }
                },
                start() {
                    if (this.timer) clearInterval(this.timer);
                    this.timer = setInterval(() => {
                        if (!this.paused) this.next();
                    }, this.interval);
                },
                pause() { this.paused = true; },
                resume() { this.paused = false; },
                next() { this.currentSlide = (this.currentSlide + 1) % this.count; },
                prev() { this.currentSlide = (this.currentSlide - 1 + this.count) % this.count; },
                goTo(i) { this.currentSlide = i; },
            };
        };
    }
</script>
