@extends('layouts.site')

@section('title', 'Grace Community Church')

@section('content')
  @php include resource_path('views/preview/_seed.php'); @endphp
  <x-site.header :nav="$siteNav" />

  <x-site.hero
    image="https://picsum.photos/seed/home-hero/1800/900"
    eyebrow="Welcome home"
    heading="A place to belong, become, and be loved."
    sub="Grace Community is a church for the curious, the worn out, and everyone in between. Wherever you are, you are welcome here."
    :primaryCta="['url' => route('preview.contact'), 'label' => 'Plan your visit']"
    :secondaryCta="['url' => route('preview.sermons'), 'label' => 'Watch a sermon']"
  />

  {{-- Service times strip --}}
  <section class="bg-brand-primary text-white">
    <div class="max-w-container mx-auto px-4 py-6 grid grid-cols-1 md:grid-cols-3 gap-6 text-center md:text-left">
      <div class="flex items-center justify-center md:justify-start gap-3">
        <span class="text-brand-secondary font-serif text-xl">Sun</span>
        <span class="font-medium">9:00 AM Traditional</span>
      </div>
      <div class="flex items-center justify-center md:justify-start gap-3">
        <span class="text-brand-secondary font-serif text-xl">Sun</span>
        <span class="font-medium">11:00 AM Contemporary</span>
      </div>
      <div class="flex items-center justify-center md:justify-start gap-3">
        <span class="text-brand-secondary font-serif text-xl">Wed</span>
        <span class="font-medium">7:00 PM Prayer & Worship</span>
      </div>
    </div>
  </section>

  {{-- Welcome 2-col --}}
  <section class="max-w-container mx-auto px-4 py-20 grid md:grid-cols-2 gap-12 items-center">
    <div>
      <x-site.section-heading
        eyebrow="A word of welcome"
        heading="However you got here, we are glad."
        lede="Maybe you have been part of a church your whole life. Maybe it has been a while, or maybe this is brand new. Either way, you do not need to clean yourself up first."
      />
      <p class="text-ink-muted leading-relaxed">
        Our prayer is that Grace Community is a place where honesty is welcome, questions are encouraged, and Jesus is at the center. Come visit — there is a seat saved for you.
      </p>
      <div class="mt-8 flex flex-wrap gap-3">
        <a href="{{ route('preview.about') }}" class="btn-primary">About us</a>
        <a href="{{ route('preview.contact') }}" class="btn-ghost">Plan a visit</a>
      </div>
    </div>
    <div class="relative">
      <div class="aspect-[4/5] rounded-2xl bg-cover bg-center shadow-lg" style="background-image:url('https://picsum.photos/seed/home-welcome/700/900')"></div>
      <div class="absolute -bottom-6 -left-6 hidden md:block bg-brand-secondary text-ink rounded-xl px-6 py-4 shadow-lg font-serif text-lg max-w-xs">
        &ldquo;Come to me, all you who are weary&hellip;&rdquo;
      </div>
    </div>
  </section>

  {{-- Upcoming events --}}
  <section class="max-w-container mx-auto px-4 py-12">
    <div class="flex flex-wrap items-end justify-between gap-4 mb-10">
      <div>
        <div class="uppercase tracking-[0.18em] text-xs text-brand-primary font-semibold mb-2">Coming up</div>
        <h2 class="font-serif text-3xl md:text-4xl leading-tight">Events & Gatherings</h2>
      </div>
      <a href="{{ route('preview.events') }}" class="text-brand-primary font-medium hover:underline">All events →</a>
    </div>
    <div class="grid md:grid-cols-3 gap-6">
      @foreach(array_slice(array_filter($events, fn($e) => $e['status'] === 'upcoming'), 0, 3) as $event)
        <x-site.card
          :image="$event['image']"
          :eyebrow="$event['tag']"
          :title="$event['title']"
          :meta="$event['date'] . ' · ' . $event['location']"
          :href="route('preview.events.show')"
        />
      @endforeach
    </div>
  </section>

  {{-- Latest sermon --}}
  <section class="bg-white border-y border-[rgb(var(--border))] my-20">
    <div class="max-w-container mx-auto px-4 py-20">
      <x-site.section-heading eyebrow="This week" heading="From the pulpit" />
      <div class="grid lg:grid-cols-3 gap-8">
        <a href="{{ route('preview.sermons.show') }}" class="lg:col-span-2 block group">
          <div class="aspect-video rounded-2xl bg-cover bg-center shadow-md overflow-hidden relative" style="background-image:url('{{ $sermons[0]['image'] }}')">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-6">
              <div class="text-white">
                <div class="text-xs uppercase tracking-wider text-brand-secondary mb-2">{{ $sermons[0]['series'] }}</div>
                <h3 class="font-serif text-2xl md:text-3xl group-hover:text-brand-secondary transition-colors">{{ $sermons[0]['title'] }}</h3>
                <div class="text-white/80 text-sm mt-2">{{ $sermons[0]['speaker'] }} · {{ $sermons[0]['date'] }}</div>
              </div>
            </div>
            <div class="absolute inset-0 flex items-center justify-center">
              <div class="w-16 h-16 rounded-full bg-white/90 flex items-center justify-center shadow-lg group-hover:scale-110 transition">
                <svg class="w-6 h-6 text-brand-primary ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
              </div>
            </div>
          </div>
        </a>
        <div class="flex flex-col gap-5">
          @foreach(array_slice($sermons, 1, 2) as $sermon)
            <a href="{{ route('preview.sermons.show') }}" class="card flex gap-4 p-4 hover:-translate-y-0.5 transition">
              <div class="w-24 h-24 rounded-lg bg-cover bg-center flex-shrink-0" style="background-image:url('{{ $sermon['image'] }}')"></div>
              <div class="min-w-0">
                <div class="text-xs uppercase tracking-wider text-brand-primary font-semibold">{{ $sermon['series'] }}</div>
                <h4 class="font-serif text-lg leading-snug mt-1 truncate">{{ $sermon['title'] }}</h4>
                <div class="text-xs text-ink-muted mt-2">{{ $sermon['speaker'] }} · {{ $sermon['date'] }}</div>
              </div>
            </a>
          @endforeach
        </div>
      </div>
    </div>
  </section>

  {{-- Ministries 4-col --}}
  <section class="max-w-container mx-auto px-4 py-12">
    <x-site.section-heading
      eyebrow="Ways to belong"
      heading="Ministries & Communities"
      lede="From kids and students to outreach and small groups — find your place to grow."
      align="center"
    />
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
      @foreach(array_slice($ministries, 0, 4) as $ministry)
        <a href="{{ route('preview.ministries.show') }}" class="group">
          <div class="aspect-[4/3] rounded-xl bg-cover bg-center overflow-hidden shadow-sm group-hover:shadow-lg transition" style="background-image:url('{{ $ministry['image'] }}')"></div>
          <h3 class="font-serif text-xl mt-4 group-hover:text-brand-primary transition-colors">{{ $ministry['name'] }}</h3>
          <p class="text-sm text-ink-muted mt-1 leading-relaxed">{{ $ministry['tagline'] }}</p>
        </a>
      @endforeach
    </div>
    <div class="text-center mt-10">
      <a href="{{ route('preview.ministries') }}" class="btn-ghost">All ministries</a>
    </div>
  </section>

  {{-- Latest blog --}}
  <section class="max-w-container mx-auto px-4 py-20">
    <div class="flex flex-wrap items-end justify-between gap-4 mb-10">
      <div>
        <div class="uppercase tracking-[0.18em] text-xs text-brand-primary font-semibold mb-2">Read & reflect</div>
        <h2 class="font-serif text-3xl md:text-4xl leading-tight">Latest from the blog</h2>
      </div>
      <a href="{{ route('preview.blog') }}" class="text-brand-primary font-medium hover:underline">All posts →</a>
    </div>
    <div class="grid md:grid-cols-3 gap-6">
      @foreach(array_slice($posts, 0, 3) as $post)
        <x-site.card
          :image="$post['image']"
          :eyebrow="$post['category']"
          :title="$post['title']"
          :meta="$post['author'] . ' · ' . $post['date']"
          :href="route('preview.blog.show')"
        />
      @endforeach
    </div>
  </section>

  <x-site.cta-band
    heading="New here? Let us know."
    sub="Tell us a little about you and we will follow up before Sunday with anything you need."
    :cta="['url' => route('preview.contact'), 'label' => 'Plan your visit']"
  />

  @include('preview._footer')
@endsection
