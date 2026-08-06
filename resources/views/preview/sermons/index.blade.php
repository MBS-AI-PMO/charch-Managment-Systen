@extends('layouts.site')

@section('title', 'Sermons · Grace Community')

@section('content')
  @php include resource_path('views/preview/_seed.php'); @endphp
  <x-site.header :nav="$siteNav" />

  <x-site.hero
    image="https://picsum.photos/seed/sermons-hero/1800/700"
    eyebrow="Watch · Listen · Reflect"
    heading="Sermons that meet you where you are."
    sub="A growing library of teachings from our pastors. Subscribe, share, or come back anytime."
    size="sm"
  />

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('preview.home')],
      ['label' => 'Sermons'],
    ]" />
  </div>

  <section class="max-w-container mx-auto px-4 py-12">
    {{-- Filter row --}}
    <div class="card p-5 mb-10">
      <div class="grid md:grid-cols-4 gap-3">
        <select class="input">
          <option>All series</option>
          @foreach($series as $s)
            <option>{{ $s['title'] }}</option>
          @endforeach
        </select>
        <select class="input">
          <option>All speakers</option>
          @foreach($speakers as $sp)
            <option>{{ $sp['name'] }}</option>
          @endforeach
        </select>
        <select class="input">
          <option>All books</option>
          <option>John</option><option>Matthew</option><option>Exodus</option><option>Psalms</option>
        </select>
        <input type="search" placeholder="Search sermons…" class="input" />
      </div>
    </div>

    <div class="grid lg:grid-cols-4 gap-10">
      {{-- Sermon grid --}}
      <div class="lg:col-span-3">
        <div class="grid sm:grid-cols-2 gap-6">
          @foreach($sermons as $sermon)
            <x-site.card
              :image="$sermon['image']"
              :eyebrow="$sermon['series']"
              :title="$sermon['title']"
              :meta="$sermon['speaker'] . ' · ' . $sermon['date'] . ' · ' . $sermon['duration']"
              :href="route('preview.sermons.show')"
            />
          @endforeach
        </div>
        {{-- Pagination placeholder --}}
        <div class="flex justify-center gap-1 mt-12">
          <button class="w-9 h-9 rounded-md border border-[rgb(var(--border))] text-ink-muted hover:bg-white" disabled>‹</button>
          <button class="w-9 h-9 rounded-md bg-brand-primary text-white">1</button>
          <button class="w-9 h-9 rounded-md border border-[rgb(var(--border))] hover:bg-white">2</button>
          <button class="w-9 h-9 rounded-md border border-[rgb(var(--border))] hover:bg-white">3</button>
          <button class="w-9 h-9 rounded-md border border-[rgb(var(--border))] hover:bg-white">›</button>
        </div>
      </div>

      {{-- Sidebar --}}
      <aside class="lg:sticky lg:top-24 lg:self-start space-y-8">
        <div>
          <div class="uppercase tracking-[0.18em] text-xs text-brand-primary font-semibold mb-4">Latest series</div>
          <div class="space-y-4">
            @foreach($series as $s)
              <a href="#" class="flex gap-3 group items-center">
                <div class="w-16 h-16 rounded-lg bg-cover bg-center flex-shrink-0" style="background-image:url('{{ $s['image'] }}')"></div>
                <div class="min-w-0">
                  <div class="font-serif text-base leading-snug group-hover:text-brand-primary transition-colors">{{ $s['title'] }}</div>
                </div>
              </a>
            @endforeach
          </div>
        </div>
        <div class="card p-6 bg-brand-secondary/20 border-brand-secondary/30">
          <h4 class="font-serif text-lg">Subscribe</h4>
          <p class="text-sm text-ink-muted mt-1">Get new sermons in your podcast app of choice.</p>
          <div class="flex flex-wrap gap-2 mt-3 text-sm">
            <a href="#" class="px-3 py-1 rounded-full bg-white border border-[rgb(var(--border))] hover:border-brand-primary">Apple</a>
            <a href="#" class="px-3 py-1 rounded-full bg-white border border-[rgb(var(--border))] hover:border-brand-primary">Spotify</a>
            <a href="#" class="px-3 py-1 rounded-full bg-white border border-[rgb(var(--border))] hover:border-brand-primary">YouTube</a>
          </div>
        </div>
      </aside>
    </div>
  </section>

  @include('preview._footer')
@endsection
