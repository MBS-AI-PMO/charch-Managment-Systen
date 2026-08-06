@extends('layouts.site')

@section('title', 'Ministry · Grace Community')

@section('content')
  @php include resource_path('views/preview/_seed.php'); @endphp
  @php $ministry = $ministries[0]; @endphp
  <x-site.header :nav="$siteNav" />

  <x-site.hero
    :image="$ministry['image']"
    eyebrow="Ministry"
    :heading="$ministry['name']"
    :sub="$ministry['tagline']"
    size="sm"
  />

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('preview.home')],
      ['label' => 'Ministries', 'url' => route('preview.ministries')],
      ['label' => $ministry['name']],
    ]" />
  </div>

  <section class="max-w-container mx-auto px-4 py-12 grid lg:grid-cols-3 gap-10 items-start">
    <div class="lg:col-span-2 prose prose-lg max-w-none">
      <p class="lead">{{ $ministry['body'] }}</p>
      <h3>When we meet</h3>
      <p>Sunday mornings during both services. Mid-week activities by age group, including our monthly family dinner on the first Friday.</p>
      <h3>Want to volunteer?</h3>
      <p>Our team is always looking for steady, kind adults to walk alongside the next generation. Background check and a coffee with the director are the first two steps.</p>
      <h3>FAQ</h3>
      <ul>
        <li><strong>Do I need to sign my child up in advance?</strong> No — just come to the welcome tent on Sunday.</li>
        <li><strong>What about kids with allergies or special needs?</strong> Tell us in advance and we will partner with you on a plan.</li>
        <li><strong>Are your volunteers screened?</strong> Yes — every Sunday team member completes a background check.</li>
      </ul>
    </div>

    <aside class="card p-6 lg:sticky lg:top-24 text-center">
      <div class="w-24 h-24 rounded-full bg-cover bg-center mx-auto mb-4 shadow-sm" style="background-image:url('{{ $ministry['leader']['photo'] }}')"></div>
      <h4 class="font-serif text-xl">{{ $ministry['leader']['name'] }}</h4>
      <p class="text-sm text-ink-muted">{{ $ministry['leader']['role'] }}</p>
      <a href="mailto:{{ $ministry['leader']['email'] }}" class="btn-primary w-full mt-5">Contact leader</a>
      <a href="#" class="btn-ghost w-full mt-2">Sign up to serve</a>
    </aside>
  </section>

  {{-- Related ministries --}}
  <section class="bg-white border-y border-[rgb(var(--border))] py-16">
    <div class="max-w-container mx-auto px-4">
      <x-site.section-heading eyebrow="Other ways to belong" heading="More ministries" />
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach(array_slice($ministries, 1, 3) as $m)
          <x-site.card
            :image="$m['image']"
            :title="$m['name']"
            :meta="$m['tagline']"
            :href="route('preview.ministries.show')"
          />
        @endforeach
      </div>
    </div>
  </section>

  @include('preview._footer')
@endsection
