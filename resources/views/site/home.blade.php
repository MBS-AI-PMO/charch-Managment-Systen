@extends('layouts.site')

@section('title', $page->meta_title ?: ($page->title.settings('seo.default_title_suffix', '')))

@include('site._meta')

@php
  $heroImg = site_img($page->hero_image_path, 'home-hero-'.$page->id, 1800, 900);
  $serviceTimes = preg_split("/\r?\n/", trim((string) settings('contact.service_times', '')));
  $welcomeImage = settings('home.welcome_image', 'uploads/yRT3rmrD_amjid-and-nazia.jpeg');
  $welcomeEyebrow = settings('home.welcome.eyebrow', 'A word of welcome');
  $welcomeHeading = settings('home.welcome.heading', 'However you got here, we are glad.');
  $welcomeLede = settings('home.welcome.lede', 'Whether you are exploring faith for the first time or have walked with Christ for decades, you will find a place here.');
  $welcomeQuote = settings('home.welcome.quote', 'Come to me, all you who are weary…');
@endphp

@section('content')
  <x-site.header :nav="$siteNav" />

  @if(isset($heroSlides) && $heroSlides->isNotEmpty())
    <x-site.hero-carousel :slides="$heroSlides" />
  @else
    <x-site.hero
      :image="$heroImg"
      eyebrow="Welcome home"
      :heading="$page->hero_heading ?: $page->title"
      :sub="$page->hero_subheading"
      :primaryCta="['url' => route('site.contact'), 'label' => 'Plan your visit']"
      :secondaryCta="['url' => route('site.sermons.index'), 'label' => 'Watch a sermon']"
    />
  @endif

  {{-- Service times strip --}}
  @if(count(array_filter($serviceTimes)))
    <section class="bg-brand-primary text-white">
      <div class="max-w-container mx-auto px-4 py-6 grid grid-cols-1 md:grid-cols-3 gap-6 text-center md:text-left">
        @foreach(array_slice($serviceTimes, 0, 3) as $i => $line)
          <div class="reveal delay-{{ $i + 1 }} flex items-center justify-center md:justify-start gap-3">
            <span class="text-brand-secondary font-serif text-xl">·</span>
            <span class="font-medium">{{ $line }}</span>
          </div>
        @endforeach
      </div>
    </section>
  @endif

  {{-- Welcome 2-col --}}
  <section class="max-w-container mx-auto px-4 py-12 md:py-16 grid md:grid-cols-2 gap-8 md:gap-12 items-center">
    <div class="reveal-left">
      <x-site.section-heading
        :eyebrow="$welcomeEyebrow"
        :heading="$welcomeHeading"
        :lede="$welcomeLede"
      />
      <div class="text-ink-muted leading-relaxed prose max-w-none">
        {!! site_render_html($page->body) !!}
      </div>
      <div class="mt-8 flex flex-wrap gap-3">
        <a href="{{ route('site.about') }}" class="btn-primary shine-btn glow-primary">About us</a>
        <a href="{{ route('site.contact') }}" class="btn-ghost">Plan a visit</a>
      </div>
    </div>
    <div class="reveal-right relative">
      <div class="aspect-[4/5] rounded-2xl bg-cover bg-center shadow-lg tilt-on-hover" style="background-image:url('{{ site_img($welcomeImage, 'home-welcome-'.$page->id, 700, 900) }}')"></div>
      <div class="float-quote absolute -bottom-6 -left-6 hidden md:block bg-brand-secondary text-ink rounded-xl px-6 py-4 shadow-lg font-serif text-lg max-w-xs">
        &ldquo;{{ $welcomeQuote }}&rdquo;
      </div>
    </div>
  </section>

  {{-- Upcoming events --}}
  @if($upcomingEvents->isNotEmpty())
    <section class="max-w-container mx-auto px-4 py-10 md:py-12">
      <div class="reveal flex flex-wrap items-end justify-between gap-4 mb-6 md:mb-8">
        <div>
          <div class="uppercase tracking-[0.18em] text-xs text-brand-primary font-semibold mb-2">Coming up</div>
          <h2 class="font-serif text-3xl md:text-4xl leading-tight">Events &amp; Gatherings</h2>
        </div>
        <a href="{{ route('site.events.index') }}" class="text-brand-primary font-medium hover:underline">All events →</a>
      </div>
      <div class="grid md:grid-cols-3 gap-6">
        @foreach($upcomingEvents as $i => $event)
          <div class="reveal delay-{{ ($i % 3) + 1 }}">
            <x-site.card
              :image="site_img($event->cover_image_path, 'event-'.$event->id, 1200, 700)"
              eyebrow="Event"
              :title="$event->title"
              :meta="$event->starts_at->format('M j, Y · g:i A').' · '.$event->location"
              :href="route('site.events.show', $event)"
            />
          </div>
        @endforeach
      </div>
    </section>
  @endif

  {{-- Latest sermon --}}
  @if($latestSermon)
    <section class="bg-white border-y border-[rgb(var(--border))] my-10 md:my-14">
      <div class="max-w-container mx-auto px-4 py-12 md:py-16">
        <div class="reveal"><x-site.section-heading eyebrow="This week" heading="From the pulpit" /></div>
        <div class="grid lg:grid-cols-3 gap-8">
          <a href="{{ route('site.sermons.show', $latestSermon) }}" class="reveal-left lg:col-span-2 block group tilt-on-hover">
            <div class="aspect-video rounded-2xl bg-cover bg-center shadow-md overflow-hidden relative" style="background-image:url('{{ site_img($latestSermon->thumbnail_path, 'sermon-'.$latestSermon->id, 1200, 675) }}')">
              <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-6">
                <div class="text-white">
                  @if($latestSermon->series)
                    <div class="text-xs uppercase tracking-wider text-brand-secondary mb-2">{{ $latestSermon->series->name }}</div>
                  @endif
                  <h3 class="font-serif text-2xl md:text-3xl group-hover:text-brand-secondary transition-colors">{{ $latestSermon->title }}</h3>
                  <div class="text-white/80 text-sm mt-2">{{ $latestSermon->speaker?->name }} · {{ $latestSermon->preached_on?->format('M j, Y') }}</div>
                </div>
              </div>
              <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-16 h-16 rounded-full bg-white/90 flex items-center justify-center shadow-lg group-hover:scale-110 transition">
                  <svg class="w-6 h-6 text-brand-primary ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </div>
              </div>
            </div>
          </a>
          <div class="reveal-right flex flex-col gap-5">
            @foreach($recentSermons->where('id', '!=', $latestSermon->id)->take(2) as $sermon)
              <a href="{{ route('site.sermons.show', $sermon) }}" class="card lift-card flex gap-4 p-4">
                <div class="w-24 h-24 rounded-lg bg-cover bg-center flex-shrink-0" style="background-image:url('{{ site_img($sermon->thumbnail_path, 'sermon-'.$sermon->id, 400, 400) }}')"></div>
                <div class="min-w-0">
                  @if($sermon->series)
                    <div class="text-xs uppercase tracking-wider text-brand-primary font-semibold">{{ $sermon->series->name }}</div>
                  @endif
                  <h4 class="font-serif text-lg leading-snug mt-1 truncate">{{ $sermon->title }}</h4>
                  <div class="text-xs text-ink-muted mt-2">{{ $sermon->speaker?->name }} · {{ $sermon->preached_on?->format('M j, Y') }}</div>
                </div>
              </a>
            @endforeach
          </div>
        </div>
      </div>
    </section>
  @endif

  {{-- Ministries --}}
  @if($ministries->isNotEmpty())
    <section class="max-w-container mx-auto px-4 py-10 md:py-12">
      <div class="reveal"><x-site.section-heading
        eyebrow="Ways to belong"
        heading="Ministries & Communities"
        lede="From kids and students to outreach and small groups — find your place to grow."
        align="center"
      /></div>
      <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($ministries->take(4) as $i => $ministry)
          <a href="{{ route('site.ministries.show', $ministry) }}" class="reveal-zoom delay-{{ ($i % 4) + 1 }} group lift-card block rounded-xl">
            <div class="aspect-[4/3] rounded-xl overflow-hidden shadow-sm group-hover:shadow-lg transition">
              <div class="lift-image w-full h-full bg-cover bg-center" style="background-image:url('{{ site_img($ministry->cover_image_path, 'ministry-'.$ministry->id, 800, 600) }}')"></div>
            </div>
            <div class="px-4 pb-4">
              <h3 class="font-serif text-xl mt-4 group-hover:text-brand-primary transition-colors">{{ $ministry->name }}</h3>
              <p class="text-sm text-ink-muted mt-1 leading-relaxed">{{ $ministry->summary }}</p>
            </div>
          </a>
        @endforeach
      </div>
      <div class="text-center mt-10">
        <a href="{{ route('site.ministries.index') }}" class="btn-ghost">All ministries</a>
      </div>
    </section>
  @endif

  {{-- Latest news --}}
  @if($morePosts->isNotEmpty())
    <section class="max-w-container mx-auto px-4 py-10 md:py-14">
      <div class="reveal flex flex-wrap items-end justify-between gap-4 mb-6 md:mb-8">
        <div>
          <div class="uppercase tracking-[0.18em] text-xs text-brand-primary font-semibold mb-2">News &amp; updates</div>
          <h2 class="font-serif text-3xl md:text-4xl leading-tight">Latest news</h2>
        </div>
        <a href="{{ route('site.blog.index') }}" class="text-brand-primary font-medium hover:underline">All news →</a>
      </div>
      <div class="grid md:grid-cols-3 gap-6">
        @foreach($morePosts as $i => $post)
          <div class="reveal delay-{{ ($i % 3) + 1 }}">
            <x-site.card
              :image="site_img($post->featured_image_path, 'post-'.$post->id, 1200, 700)"
              :eyebrow="$post->category?->name"
              :title="$post->title"
              :meta="($post->author?->name ?: 'Staff').' · '.optional($post->published_at)->format('M j, Y')"
              :href="route('site.blog.show', $post)"
            />
          </div>
        @endforeach
      </div>
    </section>
  @endif

  <div class="reveal-zoom">
    <x-site.cta-band
      heading="New here? Let us know."
      sub="Tell us a little about you and we will follow up before Sunday with anything you need."
      :cta="['url' => route('site.contact'), 'label' => 'Plan your visit']"
    />
  </div>

  @include('site._footer')
@endsection
