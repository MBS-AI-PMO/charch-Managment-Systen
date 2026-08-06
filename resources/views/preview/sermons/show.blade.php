@extends('layouts.site')

@section('title', 'Sermon · Grace Community')

@section('content')
  @php include resource_path('views/preview/_seed.php'); @endphp
  @php $sermon = $sermons[0]; @endphp
  <x-site.header :nav="$siteNav" />

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('preview.home')],
      ['label' => 'Sermons', 'url' => route('preview.sermons')],
      ['label' => $sermon['title']],
    ]" />
  </div>

  {{-- Title + meta strip --}}
  <section class="max-w-container mx-auto px-4 pt-2 pb-10">
    <div class="uppercase tracking-[0.18em] text-xs text-brand-primary font-semibold mb-3">{{ $sermon['series'] }}</div>
    <h1 class="font-serif text-3xl md:text-5xl leading-tight max-w-3xl">{{ $sermon['title'] }}</h1>
    <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-ink-muted mt-5">
      <span class="flex items-center gap-2">
        <img src="https://picsum.photos/seed/speaker-david/64/64" class="w-8 h-8 rounded-full" alt="">
        <span class="font-medium text-ink">{{ $sermon['speaker'] }}</span>
      </span>
      <span>{{ $sermon['date'] }}</span>
      <span>{{ $sermon['duration'] }}</span>
      <span class="px-3 py-1 rounded-full bg-brand-secondary/30 text-ink font-medium text-xs">{{ $sermon['scripture'] }}</span>
    </div>
  </section>

  {{-- Video embed --}}
  <section class="max-w-container mx-auto px-4">
    <div class="aspect-video rounded-2xl overflow-hidden shadow-md bg-ink">
      <iframe class="w-full h-full" src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="Sermon video" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
  </section>

  {{-- Audio + scripture --}}
  <section class="max-w-container mx-auto px-4 py-10 grid lg:grid-cols-3 gap-8 items-start">
    <div class="lg:col-span-2 space-y-6">
      <div class="card p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-brand-primary text-white flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
        </div>
        <div class="flex-1 min-w-0">
          <div class="font-medium">Listen instead</div>
          <audio controls preload="none" class="w-full mt-2">
            <source src="#" type="audio/mpeg">
          </audio>
        </div>
      </div>

      <div class="prose prose-lg max-w-none">
        <h2>Summary</h2>
        <p>{{ $sermon['summary'] }}</p>
        <p>In John 15, Jesus tells his disciples that he is the vine and they are the branches. The image is rural and tactile — a vinedresser pruning back what once flourished. It is a striking metaphor for those of us who have walked through seasons of feeling stripped back, less fruitful than we used to be, wondering what God is doing.</p>
        <blockquote>
          &ldquo;Apart from me you can do nothing.&rdquo;
        </blockquote>
        <p>The good news of this passage is not that we are required to muscle our way back into productivity. The good news is that the gardener is good, attentive, and at work — even now.</p>
        <h3>Discussion questions</h3>
        <ol>
          <li>Where in your life do you feel pruned right now?</li>
          <li>What would it look like to abide rather than perform this week?</li>
          <li>Who in your life is bearing fruit you would like to learn from?</li>
        </ol>
      </div>
    </div>

    <aside class="card p-6 space-y-4 lg:sticky lg:top-24">
      <h4 class="font-serif text-lg">Scripture for today</h4>
      <p class="text-ink-muted text-sm leading-relaxed">
        <span class="font-medium text-ink block mb-1">{{ $sermon['scripture'] }}</span>
        &ldquo;I am the true vine, and my Father is the gardener…&rdquo;
      </p>
      <a href="#" class="btn-ghost w-full">Read full passage</a>
      <div class="pt-4 border-t border-[rgb(var(--border))]">
        <h4 class="font-serif text-lg">Share</h4>
        <div class="flex gap-2 mt-3 text-sm">
          <a href="#" class="px-3 py-1 rounded-full border border-[rgb(var(--border))] hover:border-brand-primary">Copy link</a>
          <a href="#" class="px-3 py-1 rounded-full border border-[rgb(var(--border))] hover:border-brand-primary">Email</a>
        </div>
      </div>
    </aside>
  </section>

  {{-- More from this series — Alpine horizontal scroll --}}
  <section class="bg-white border-y border-[rgb(var(--border))] py-16" x-data="{scrollBy(d){this.$refs.row.scrollBy({left:d,behavior:'smooth'})}}">
    <div class="max-w-container mx-auto px-4">
      <div class="flex items-end justify-between mb-6 gap-4">
        <div>
          <div class="uppercase tracking-[0.18em] text-xs text-brand-primary font-semibold mb-2">More from this series</div>
          <h2 class="font-serif text-3xl">Rooted: Living Faith Daily</h2>
        </div>
        <div class="hidden md:flex gap-2">
          <button @click="scrollBy(-340)" class="w-10 h-10 rounded-full border border-[rgb(var(--border))] hover:bg-surface" aria-label="Scroll left">‹</button>
          <button @click="scrollBy(340)" class="w-10 h-10 rounded-full border border-[rgb(var(--border))] hover:bg-surface" aria-label="Scroll right">›</button>
        </div>
      </div>
      <div x-ref="row" class="flex gap-6 overflow-x-auto pb-4 snap-x snap-mandatory scroll-smooth -mx-4 px-4">
        @foreach($sermons as $s)
          <a href="{{ route('preview.sermons.show') }}" class="card overflow-hidden flex-shrink-0 w-80 snap-start hover:-translate-y-0.5 transition">
            <div class="aspect-video bg-cover bg-center" style="background-image:url('{{ $s['image'] }}')"></div>
            <div class="p-4">
              <div class="text-xs uppercase tracking-wider text-brand-primary mb-1">{{ $s['series'] }}</div>
              <h3 class="font-serif text-lg leading-snug">{{ $s['title'] }}</h3>
              <div class="text-xs text-ink-muted mt-2">{{ $s['speaker'] }} · {{ $s['date'] }}</div>
            </div>
          </a>
        @endforeach
      </div>
    </div>
  </section>

  @include('preview._footer')
@endsection
