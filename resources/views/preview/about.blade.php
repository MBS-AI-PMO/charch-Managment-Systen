@extends('layouts.site')

@section('title', 'About Us · Grace Community')

@section('content')
  @php include resource_path('views/preview/_seed.php'); @endphp
  <x-site.header :nav="$siteNav" />

  <x-site.hero
    image="https://picsum.photos/seed/about-hero/1800/700"
    eyebrow="Our story"
    heading="A community shaped by grace, sent on mission."
    sub="For more than fifty years, Grace Community has been a home for people learning to follow Jesus together in Springfield."
    size="sm"
  />

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('preview.home')],
      ['label' => 'About'],
    ]" />
  </div>

  {{-- Our story 2-col --}}
  <section class="max-w-container mx-auto px-4 py-20 grid md:grid-cols-2 gap-12 items-center">
    <div class="relative">
      <div class="aspect-square rounded-2xl bg-cover bg-center shadow-lg" style="background-image:url('https://picsum.photos/seed/about-story/800/800')"></div>
      <div class="absolute -bottom-6 -right-6 hidden md:block bg-brand-primary text-white rounded-xl px-6 py-4 shadow-lg font-serif text-lg max-w-xs">
        Est. 1973
      </div>
    </div>
    <div>
      <x-site.section-heading
        eyebrow="Our story"
        heading="Built on simple promises, kept over decades."
      />
      <div class="space-y-4 text-ink-muted leading-relaxed">
        <p>Grace Community began in 1973 as a small Bible study around a kitchen table. Five decades later, the table is bigger — but the heart is the same: open scripture together, follow Jesus together, and love our neighbors.</p>
        <p>We are a multi-generational, multi-ethnic family of about 1,200 people across two Sunday services. We are imperfect, in process, and grateful to belong to one another.</p>
        <p>Whether you have walked with Jesus for years or are wondering what all of this is even about, you will find a seat saved for you here.</p>
      </div>
    </div>
  </section>

  {{-- Beliefs --}}
  <section class="bg-white border-y border-[rgb(var(--border))] py-20">
    <div class="max-w-container mx-auto px-4">
      <x-site.section-heading
        eyebrow="What we believe"
        heading="The things we keep coming back to."
        align="center"
      />
      <div class="grid md:grid-cols-3 gap-6">
        @foreach($beliefs as $belief)
          <div class="card p-7 hover:shadow-md transition">
            <div class="w-12 h-12 rounded-full bg-brand-secondary/20 text-brand-primary flex items-center justify-center mb-4">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
              </svg>
            </div>
            <h3 class="font-serif text-xl">{{ $belief['title'] }}</h3>
            <p class="text-ink-muted text-sm mt-2 leading-relaxed">{{ $belief['body'] }}</p>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- Leadership --}}
  <section class="max-w-container mx-auto px-4 py-20">
    <x-site.section-heading
      eyebrow="Our team"
      heading="The people who serve us"
      lede="Pastors and staff who are honored to walk with this church."
      align="center"
    />
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
      @foreach($leaders as $leader)
        <div class="text-center">
          <div class="aspect-[4/5] rounded-xl bg-cover bg-center mb-4 shadow-sm" style="background-image:url('{{ $leader['photo'] }}')"></div>
          <h3 class="font-serif text-lg">{{ $leader['name'] }}</h3>
          <p class="text-sm text-ink-muted">{{ $leader['role'] }}</p>
        </div>
      @endforeach
    </div>
  </section>

  <x-site.cta-band
    heading="Come and see for yourself."
    sub="Sunday gatherings at 9 and 11 AM. There is a seat saved with your name on it."
    :cta="['url' => route('preview.contact'), 'label' => 'Plan your visit']"
  />

  @include('preview._footer')
@endsection
