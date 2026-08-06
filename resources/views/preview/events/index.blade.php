@extends('layouts.site')

@section('title', 'Events · Grace Community')

@section('content')
  @php include resource_path('views/preview/_seed.php'); @endphp
  <x-site.header :nav="$siteNav" />

  <x-site.hero
    image="https://picsum.photos/seed/events-hero/1800/700"
    eyebrow="Calendar"
    heading="Gather, serve, celebrate."
    sub="From kids camp to community dinners — there is always something happening at Grace."
    size="sm"
  />

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('preview.home')],
      ['label' => 'Events'],
    ]" />
  </div>

  <section class="max-w-container mx-auto px-4 py-12" x-data="{tab:'upcoming'}">
    {{-- Tabs --}}
    <div class="inline-flex bg-white border border-[rgb(var(--border))] rounded-full p-1 mb-10 shadow-sm">
      <button @click="tab='upcoming'" :class="tab==='upcoming' ? 'bg-brand-primary text-white' : 'text-ink-muted hover:text-ink'" class="px-5 py-2 rounded-full text-sm font-medium transition">Upcoming</button>
      <button @click="tab='past'" :class="tab==='past' ? 'bg-brand-primary text-white' : 'text-ink-muted hover:text-ink'" class="px-5 py-2 rounded-full text-sm font-medium transition">Past</button>
    </div>

    {{-- Upcoming --}}
    <div x-show="tab==='upcoming'" x-cloak>
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach(array_filter($events, fn($e) => $e['status'] === 'upcoming') as $event)
          <a href="{{ route('preview.events.show') }}" class="card overflow-hidden block hover:-translate-y-1 hover:shadow-md transition relative">
            <div class="aspect-[16/10] bg-cover bg-center" style="background-image:url('{{ $event['image'] }}')"></div>
            <div class="absolute top-4 left-4 bg-white rounded-lg shadow-md text-center px-3 py-2 leading-none">
              <div class="font-serif text-2xl text-brand-primary leading-none">{{ $event['day'] }}</div>
              <div class="text-[10px] uppercase tracking-widest text-ink-muted mt-1">{{ $event['month'] }}</div>
            </div>
            <div class="p-5">
              <div class="text-xs uppercase tracking-wider text-brand-primary mb-1">{{ $event['tag'] }}</div>
              <h3 class="font-serif text-xl leading-snug">{{ $event['title'] }}</h3>
              <div class="text-sm text-ink-muted mt-2 space-y-1">
                <div>{{ $event['time'] }}</div>
                <div>{{ $event['location'] }}</div>
              </div>
              <div class="mt-4 text-brand-primary font-medium text-sm">Details →</div>
            </div>
          </a>
        @endforeach
      </div>
    </div>

    {{-- Past --}}
    <div x-show="tab==='past'" x-cloak>
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach(array_filter($events, fn($e) => $e['status'] === 'past') as $event)
          <a href="{{ route('preview.events.show') }}" class="card overflow-hidden block hover:-translate-y-1 hover:shadow-md transition relative opacity-90">
            <div class="aspect-[16/10] bg-cover bg-center grayscale" style="background-image:url('{{ $event['image'] }}')"></div>
            <div class="absolute top-4 left-4 bg-white rounded-lg shadow-md text-center px-3 py-2 leading-none">
              <div class="font-serif text-2xl text-ink-muted leading-none">{{ $event['day'] }}</div>
              <div class="text-[10px] uppercase tracking-widest text-ink-muted mt-1">{{ $event['month'] }}</div>
            </div>
            <div class="p-5">
              <div class="text-xs uppercase tracking-wider text-ink-muted mb-1">Past · {{ $event['tag'] }}</div>
              <h3 class="font-serif text-xl leading-snug">{{ $event['title'] }}</h3>
              <div class="text-sm text-ink-muted mt-2">{{ $event['date'] }}</div>
            </div>
          </a>
        @endforeach
      </div>
    </div>
  </section>

  @include('preview._footer')
@endsection
