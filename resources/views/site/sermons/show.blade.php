@extends('layouts.site')

@section('title', $sermon->title.settings('seo.default_title_suffix', ''))

@php
  // Convert common YouTube/Vimeo URLs to embed form.
  $embedUrl = null;
  if ($sermon->video_url) {
      $u = $sermon->video_url;
      if (preg_match('~youtube\.com/watch\?v=([\w-]+)~', $u, $m)) {
          $embedUrl = 'https://www.youtube.com/embed/'.$m[1];
      } elseif (preg_match('~youtu\.be/([\w-]+)~', $u, $m)) {
          $embedUrl = 'https://www.youtube.com/embed/'.$m[1];
      } elseif (preg_match('~vimeo\.com/(\d+)~', $u, $m)) {
          $embedUrl = 'https://player.vimeo.com/video/'.$m[1];
      } else {
          $embedUrl = $u;
      }
  }
@endphp

@section('content')
  <x-site.header :nav="$siteNav" />

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('site.home')],
      ['label' => 'Sermons', 'url' => route('site.sermons.index')],
      ['label' => $sermon->title],
    ]" />
  </div>

  <section class="max-w-container mx-auto px-4 pt-2 pb-8 md:pb-10">
    @if($sermon->series)
      <div class="uppercase tracking-[0.18em] text-xs text-brand-primary font-semibold mb-3">{{ $sermon->series->name }}</div>
    @endif
    <h1 class="font-serif text-3xl md:text-5xl leading-tight max-w-3xl">{{ $sermon->title }}</h1>
    <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-ink-muted mt-5">
      @if($sermon->speaker)
        <span class="flex items-center gap-2">
          <img src="{{ site_img($sermon->speaker->photo_path, 'speaker-'.$sermon->speaker->id, 64, 64) }}" class="w-8 h-8 rounded-full object-cover" alt="">
          <span class="font-medium text-ink">{{ $sermon->speaker->name }}</span>
        </span>
      @endif
      @if($sermon->preached_on)
        <span>{{ $sermon->preached_on->format('M j, Y') }}</span>
      @endif
      @if($sermon->scripture_reference)
        <span class="px-3 py-1 rounded-full bg-brand-secondary/30 text-ink font-medium text-xs">{{ $sermon->scripture_reference }}</span>
      @endif
    </div>
  </section>

  @if($embedUrl)
    <section class="max-w-container mx-auto px-4">
      <div class="aspect-video rounded-2xl overflow-hidden shadow-md bg-ink">
        <iframe class="w-full h-full" src="{{ $embedUrl }}" title="{{ $sermon->title }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
      </div>
    </section>
  @endif

  <section class="max-w-container mx-auto px-4 py-10 grid lg:grid-cols-3 gap-8 items-start">
    <div class="lg:col-span-2 space-y-6">
      @if($sermon->audio_url)
        <div class="card p-5 flex items-center gap-4">
          <div class="w-12 h-12 rounded-full bg-brand-primary text-white flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
          </div>
          <div class="flex-1 min-w-0">
            <div class="font-medium">Listen instead</div>
            <audio controls preload="none" class="w-full mt-2">
              <source src="{{ $sermon->audio_url }}" type="audio/mpeg">
            </audio>
          </div>
        </div>
      @endif

      <div class="prose prose-lg max-w-none">
        @if($sermon->summary)
          <p class="lead">{{ $sermon->summary }}</p>
        @endif
        {!! site_render_html($sermon->body) !!}
      </div>
    </div>

    <aside class="card p-6 space-y-4 lg:sticky lg:top-24">
      @if($sermon->scripture_reference)
        <h4 class="font-serif text-lg">Scripture for today</h4>
        <p class="text-ink-muted text-sm leading-relaxed">
          <span class="font-medium text-ink block mb-1">{{ $sermon->scripture_reference }}</span>
        </p>
      @endif
      @if($sermon->downloads_enabled && $sermon->audio_url)
        <a href="{{ $sermon->audio_url }}" download class="btn-ghost w-full">Download audio</a>
      @endif
      <div class="pt-4 border-t border-[rgb(var(--border))]">
        <h4 class="font-serif text-lg">Share</h4>
        <div class="flex gap-2 mt-3 text-sm">
          <a href="{{ url()->current() }}" class="px-3 py-1 rounded-full border border-[rgb(var(--border))] hover:border-brand-primary">Copy link</a>
        </div>
      </div>
    </aside>
  </section>

  @if($relatedInSeries->isNotEmpty())
    <section class="bg-white border-y border-[rgb(var(--border))] py-10 md:py-14" x-data="{scrollBy(d){this.$refs.row.scrollBy({left:d,behavior:'smooth'})}}">
      <div class="max-w-container mx-auto px-4">
        <div class="flex items-end justify-between mb-6 gap-4">
          <div>
            <div class="uppercase tracking-[0.18em] text-xs text-brand-primary font-semibold mb-2">More from this series</div>
            <h2 class="font-serif text-3xl">{{ $sermon->series->name }}</h2>
          </div>
          <div class="hidden md:flex gap-2">
            <button @click="scrollBy(-340)" class="w-10 h-10 rounded-full border border-[rgb(var(--border))] hover:bg-surface" aria-label="Scroll left">‹</button>
            <button @click="scrollBy(340)" class="w-10 h-10 rounded-full border border-[rgb(var(--border))] hover:bg-surface" aria-label="Scroll right">›</button>
          </div>
        </div>
        <div x-ref="row" class="flex gap-6 overflow-x-auto pb-4 snap-x snap-mandatory scroll-smooth -mx-4 px-4">
          @foreach($relatedInSeries as $s)
            <a href="{{ route('site.sermons.show', $s) }}" class="card overflow-hidden flex-shrink-0 w-80 snap-start hover:-translate-y-0.5 transition">
              <div class="aspect-video bg-cover bg-center" style="background-image:url('{{ site_img($s->thumbnail_path, 'sermon-'.$s->id, 800, 450) }}')"></div>
              <div class="p-4">
                <div class="text-xs uppercase tracking-wider text-brand-primary mb-1">{{ $s->series?->name }}</div>
                <h3 class="font-serif text-lg leading-snug">{{ $s->title }}</h3>
                <div class="text-xs text-ink-muted mt-2">{{ $s->speaker?->name }} · {{ $s->preached_on?->format('M j, Y') }}</div>
              </div>
            </a>
          @endforeach
        </div>
      </div>
    </section>
  @endif

  @include('site._footer')
@endsection
