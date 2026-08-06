@extends('layouts.site')

@section('title', $event->title.settings('seo.default_title_suffix', ''))

@php
  $cover = site_img($event->cover_image_path, 'event-'.$event->id, 1800, 900);
@endphp

@section('content')
  <x-site.header :nav="$siteNav" />

  <section class="relative isolate overflow-hidden">
    <img src="{{ $cover }}" alt="" class="absolute inset-0 w-full h-full object-cover">
    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/50 to-black/30"></div>
    <div class="relative max-w-container mx-auto px-4 py-16 md:py-24 text-white">
      <div class="flex items-start gap-6">
        <div class="bg-white rounded-xl text-center px-5 py-4 shadow-lg leading-none flex-shrink-0">
          <div class="font-serif text-4xl text-brand-primary">{{ $event->starts_at->format('j') }}</div>
          <div class="text-xs uppercase tracking-widest text-ink-muted mt-1">{{ $event->starts_at->format('M') }}</div>
        </div>
        <div>
          <div class="uppercase tracking-[0.2em] text-xs text-brand-secondary font-semibold mb-2">Event</div>
          <h1 class="font-serif text-3xl md:text-5xl leading-tight max-w-3xl">{{ $event->title }}</h1>
          <div class="mt-4 text-white/85">
            {{ $event->starts_at->format('D, M j, Y · g:i A') }}
            @if($event->ends_at)
              – {{ $event->ends_at->format($event->ends_at->isSameDay($event->starts_at) ? 'g:i A' : 'D, M j · g:i A') }}
            @endif
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('site.home')],
      ['label' => 'Events', 'url' => route('site.events.index')],
      ['label' => $event->title],
    ]" />
  </div>

  <section class="max-w-container mx-auto px-4 py-10 grid lg:grid-cols-3 gap-10 items-start">
    <div class="lg:col-span-2 prose prose-lg max-w-none">
      {!! site_render_html($event->description) !!}
    </div>

    <aside class="card p-6 space-y-5 lg:sticky lg:top-24">
      <div>
        <div class="text-xs uppercase tracking-wider text-brand-primary font-semibold mb-1">When</div>
        <div class="font-medium">{{ $event->starts_at->format('l, F j, Y') }}</div>
        <div class="text-sm text-ink-muted">
          {{ $event->starts_at->format('g:i A') }}
          @if($event->ends_at)
            – {{ $event->ends_at->format($event->ends_at->isSameDay($event->starts_at) ? 'g:i A' : 'M j, g:i A') }}
          @endif
        </div>
      </div>
      <div>
        <div class="text-xs uppercase tracking-wider text-brand-primary font-semibold mb-1">Where</div>
        <div class="font-medium">{{ $event->location }}</div>
      </div>
      <div class="space-y-2 pt-2">
        @if($event->registration_url)
          <a href="{{ $event->registration_url }}" target="_blank" rel="noopener" class="btn-primary w-full">Register</a>
        @endif
        <a href="{{ route('site.events.ics', $event) }}" class="btn-ghost w-full">Add to calendar</a>
      </div>
    </aside>
  </section>

  <div class="max-w-container mx-auto px-4 pb-8 md:pb-10">
    @include('site.events._rsvp_card')
  </div>

  @include('site._footer')
@endsection
