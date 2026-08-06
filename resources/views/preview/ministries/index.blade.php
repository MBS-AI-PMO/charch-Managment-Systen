@extends('layouts.site')

@section('title', 'Ministries · Grace Community')

@section('content')
  @php include resource_path('views/preview/_seed.php'); @endphp
  <x-site.header :nav="$siteNav" />

  <x-site.hero
    image="https://picsum.photos/seed/ministries-hero/1800/700"
    eyebrow="Ways to belong"
    heading="Find your place to grow and serve."
    sub="Every season of life has a place here. Pick one that fits where you are right now."
    size="sm"
  />

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('preview.home')],
      ['label' => 'Ministries'],
    ]" />
  </div>

  <section class="max-w-container mx-auto px-4 py-16">
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
      @foreach($ministries as $m)
        <a href="{{ route('preview.ministries.show') }}" class="card overflow-hidden block group hover:-translate-y-1 hover:shadow-md transition">
          <div class="aspect-[4/3] bg-cover bg-center" style="background-image:url('{{ $m['image'] }}')"></div>
          <div class="p-6">
            <h3 class="font-serif text-xl group-hover:text-brand-primary transition-colors">{{ $m['name'] }}</h3>
            <p class="text-sm text-ink-muted mt-2 leading-relaxed">{{ $m['tagline'] }}</p>
            <div class="mt-4 text-brand-primary font-medium text-sm">Learn more →</div>
          </div>
        </a>
      @endforeach
    </div>
  </section>

  <x-site.cta-band
    heading="Not sure where to start?"
    sub="Reach out and we will help you find something that fits."
    :cta="['url' => route('preview.contact'), 'label' => 'Get in touch']"
  />

  @include('preview._footer')
@endsection
