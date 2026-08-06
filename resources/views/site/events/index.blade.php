@extends('layouts.site')

@section('title', $page?->meta_title ?: (($page?->title ?: 'Events').settings('seo.default_title_suffix', '')))

@include('site._meta')

@php
  $heroImg = site_img($page?->hero_image_path, 'events-hero', 1800, 700);

  $myRsvpIds = auth('web')->check()
      ? \App\Models\EventRsvp::where('user_id', auth('web')->id())
          ->where('status', 'going')
          ->pluck('event_id')->all()
      : [];
@endphp

@section('content')
  <x-site.header :nav="$siteNav" />

  <x-site.hero
    :image="$heroImg"
    eyebrow="Calendar"
    :heading="$page?->hero_heading ?: 'Gather, serve, celebrate.'"
    :sub="$page?->hero_subheading ?: 'There is always something happening at our church.'"
    size="sm"
  />

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('site.home')],
      ['label' => 'Events'],
    ]" />
  </div>

  <section class="max-w-container mx-auto px-4 py-8 md:py-12" x-data="{tab:'upcoming'}">
    <div class="inline-flex bg-white border border-[rgb(var(--border))] rounded-full p-1 mb-10 shadow-sm">
      <button @click="tab='upcoming'" :class="tab==='upcoming' ? 'bg-brand-primary text-white' : 'text-ink-muted hover:text-ink'" class="px-5 py-2 rounded-full text-sm font-medium transition">Upcoming ({{ $upcoming->total() }})</button>
      <button @click="tab='past'" :class="tab==='past' ? 'bg-brand-primary text-white' : 'text-ink-muted hover:text-ink'" class="px-5 py-2 rounded-full text-sm font-medium transition">Past ({{ $past->total() }})</button>
    </div>

    {{-- Upcoming --}}
    <div x-show="tab==='upcoming'" x-cloak>
      @if($upcoming->isEmpty())
        <div class="card p-10 text-center text-ink-muted">Nothing on the calendar just yet — check back soon.</div>
      @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach($upcoming as $event)
            <a href="{{ route('site.events.show', $event) }}" class="card lift-card overflow-hidden block relative">
              <div class="aspect-[16/10] overflow-hidden">
                <div class="lift-image w-full h-full bg-cover bg-center" style="background-image:url('{{ site_img($event->cover_image_path, 'event-'.$event->id, 1200, 700) }}')"></div>
              </div>
              <div class="absolute top-4 left-4 bg-white rounded-lg shadow-md text-center px-3 py-2 leading-none">
                <div class="font-serif text-2xl text-brand-primary leading-none">{{ $event->starts_at->format('j') }}</div>
                <div class="text-[10px] uppercase tracking-widest text-ink-muted mt-1">{{ $event->starts_at->format('M') }}</div>
              </div>
              <div class="p-5">
                @if($event->is_featured)
                  <div class="text-xs uppercase tracking-wider text-brand-primary mb-1">Featured</div>
                @endif
                <h3 class="font-serif text-xl leading-snug">{{ $event->title }}</h3>
                <div class="text-sm text-ink-muted mt-2 space-y-1">
                  <div>{{ $event->starts_at->format('D, M j · g:i A') }}</div>
                  <div>{{ $event->location }}</div>
                </div>
                @if(in_array($event->id, $myRsvpIds))
                  <span class="inline-flex items-center mt-2 px-2 py-0.5 rounded-full bg-brand-secondary/15 text-brand-secondary text-xs font-semibold">✓ You're going</span>
                @elseif(auth('web')->check())
                  <span class="text-xs text-brand-primary mt-2 inline-block">RSVP →</span>
                @endif
                <div class="mt-4 text-brand-primary font-medium text-sm">Details →</div>
              </div>
            </a>
          @endforeach
        </div>
        <div class="mt-8 md:mt-10">{{ $upcoming->links() }}</div>
      @endif
    </div>

    {{-- Past --}}
    <div x-show="tab==='past'" x-cloak>
      @if($past->isEmpty())
        <div class="card p-10 text-center text-ink-muted">No past events on file.</div>
      @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach($past as $event)
            <a href="{{ route('site.events.show', $event) }}" class="card lift-card overflow-hidden block relative opacity-90">
              <div class="aspect-[16/10] overflow-hidden">
                <div class="lift-image w-full h-full bg-cover bg-center grayscale" style="background-image:url('{{ site_img($event->cover_image_path, 'event-'.$event->id, 1200, 700) }}')"></div>
              </div>
              <div class="absolute top-4 left-4 bg-white rounded-lg shadow-md text-center px-3 py-2 leading-none">
                <div class="font-serif text-2xl text-ink-muted leading-none">{{ $event->starts_at->format('j') }}</div>
                <div class="text-[10px] uppercase tracking-widest text-ink-muted mt-1">{{ $event->starts_at->format('M') }}</div>
              </div>
              <div class="p-5">
                <div class="text-xs uppercase tracking-wider text-ink-muted mb-1">Past event</div>
                <h3 class="font-serif text-xl leading-snug">{{ $event->title }}</h3>
                <div class="text-sm text-ink-muted mt-2">{{ $event->starts_at->format('M j, Y') }}</div>
              </div>
            </a>
          @endforeach
        </div>
        <div class="mt-8 md:mt-10">{{ $past->links() }}</div>
      @endif
    </div>
  </section>

  @include('site._footer')
@endsection
