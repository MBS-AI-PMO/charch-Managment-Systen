@extends('layouts.site')

@section('title', 'RSVP cancelled'.settings('seo.default_title_suffix', ''))

@section('content')
  <x-site.header :nav="$siteNav ?? collect()" />

  <section class="max-w-container mx-auto px-4 py-14 md:py-20">
    <div class="max-w-2xl mx-auto text-center">
      <div class="uppercase tracking-[0.2em] text-xs text-brand-secondary font-semibold mb-4">RSVP updated</div>
      <h1 class="font-serif text-3xl md:text-5xl leading-tight">
        We've cancelled your RSVP, {{ $firstName }}.
      </h1>
      <p class="mt-6 text-ink-muted text-lg">
        Sorry you can't make it to <span class="font-medium">{{ $event->title }}</span> &mdash; we'll miss you, and hope to see you next time.
      </p>

      <div class="mt-10">
        <a href="{{ route('site.events.index') }}" class="btn-primary inline-flex items-center">
          Back to events
        </a>
      </div>
    </div>
  </section>
@endsection
