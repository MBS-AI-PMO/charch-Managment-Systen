@extends('layouts.site')

@section('title', 'Event · Grace Community')

@section('content')
  @php include resource_path('views/preview/_seed.php'); @endphp
  @php $event = $events[0]; @endphp
  <x-site.header :nav="$siteNav" />

  {{-- Hero with date badge --}}
  <section class="relative isolate overflow-hidden">
    <img src="{{ $event['image'] }}" alt="" class="absolute inset-0 w-full h-full object-cover">
    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/50 to-black/30"></div>
    <div class="relative max-w-container mx-auto px-4 py-24 md:py-32 text-white">
      <div class="flex items-start gap-6">
        <div class="bg-white rounded-xl text-center px-5 py-4 shadow-lg leading-none flex-shrink-0">
          <div class="font-serif text-4xl text-brand-primary">{{ $event['day'] }}</div>
          <div class="text-xs uppercase tracking-widest text-ink-muted mt-1">{{ $event['month'] }}</div>
        </div>
        <div>
          <div class="uppercase tracking-[0.2em] text-xs text-brand-secondary font-semibold mb-2">{{ $event['tag'] }} · Event</div>
          <h1 class="font-serif text-3xl md:text-5xl leading-tight max-w-3xl">{{ $event['title'] }}</h1>
          <div class="mt-4 text-white/85">{{ $event['date'] }} · {{ $event['time'] }}</div>
        </div>
      </div>
    </div>
  </section>

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('preview.home')],
      ['label' => 'Events', 'url' => route('preview.events')],
      ['label' => $event['title']],
    ]" />
  </div>

  {{-- Body + sidebar --}}
  <section class="max-w-container mx-auto px-4 py-10 grid lg:grid-cols-3 gap-10 items-start">
    <div class="lg:col-span-2 prose prose-lg max-w-none">
      <p class="lead">{{ $event['summary'] }}</p>
      <p>Bring the whole family. Bring a friend. Bring an empty stomach — there will be more food than you can carry. We close down a block of Maple Street for the afternoon and turn it into the best kind of neighborhood mess: kids running everywhere, music in the air, and a few hundred new conversations starting on the lawn.</p>
      <h3>What to expect</h3>
      <ul>
        <li>Live music from the Worship & Arts team</li>
        <li>Free hot dogs, snow cones, and a coffee bar</li>
        <li>Bounce houses, face painting, and a games corner for kids</li>
        <li>A welcome tent for anyone who is new to the neighborhood</li>
      </ul>
      <h3>Want to help?</h3>
      <p>We need volunteers for setup, food, kids zone, and tear down. Sign up below — even an hour helps.</p>
    </div>

    <aside class="card p-6 space-y-5 lg:sticky lg:top-24">
      <div>
        <div class="text-xs uppercase tracking-wider text-brand-primary font-semibold mb-1">When</div>
        <div class="font-medium">{{ $event['date'] }}</div>
        <div class="text-sm text-ink-muted">{{ $event['time'] }}</div>
      </div>
      <div>
        <div class="text-xs uppercase tracking-wider text-brand-primary font-semibold mb-1">Where</div>
        <div class="font-medium">{{ $event['location'] }}</div>
        <div class="text-sm text-ink-muted">124 Maple Street, Springfield IL</div>
      </div>
      <div class="space-y-2 pt-2">
        <a href="#" class="btn-primary w-full">Register</a>
        <a href="#" class="btn-ghost w-full">Add to calendar</a>
        <a href="#" class="btn-ghost w-full">Volunteer</a>
      </div>
      <div class="rounded-lg overflow-hidden border border-[rgb(var(--border))] aspect-square">
        <iframe class="w-full h-full" loading="lazy" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12136.654!2d-89.6437!3d39.7817!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x88754ed8f3aa17d3%3A0x9a8e8d36cb84e80!2sSpringfield%2C%20IL!5e0!3m2!1sen!2sus!4v1716000000000"></iframe>
      </div>
    </aside>
  </section>

  @include('preview._footer')
@endsection
