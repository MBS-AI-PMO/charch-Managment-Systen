@extends('layouts.site')

@section('title', $page?->meta_title ?: (($page?->title ?: 'Ministries').settings('seo.default_title_suffix', '')))

@include('site._meta')

@php
  $heroImg = site_img($page?->hero_image_path, 'ministries-hero', 1800, 700);
@endphp

@section('content')
  <x-site.header :nav="$siteNav" />

  <x-site.hero
    :image="$heroImg"
    eyebrow="Ways to belong"
    :heading="$page?->hero_heading ?: 'Find your place to grow and serve.'"
    :sub="$page?->hero_subheading ?: 'Every season of life has a place here.'"
    size="sm"
  />

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('site.home')],
      ['label' => 'Ministries'],
    ]" />
  </div>

  <section class="max-w-container mx-auto px-4 py-8 md:py-12">
    @if($ministries->isEmpty())
      <div class="card p-10 text-center text-ink-muted">No ministries published yet.</div>
    @else
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($ministries as $m)
          <a href="{{ route('site.ministries.show', $m) }}" class="card overflow-hidden block group hover:-translate-y-1 hover:shadow-md transition">
            <div class="aspect-[4/3] bg-cover bg-center" style="background-image:url('{{ site_img($m->cover_image_path, 'ministry-'.$m->id, 800, 600) }}')"></div>
            <div class="p-6">
              <h3 class="font-serif text-xl group-hover:text-brand-primary transition-colors">{{ $m->name }}</h3>
              <p class="text-sm text-ink-muted mt-2 leading-relaxed">{{ $m->summary }}</p>
              <div class="mt-4 text-brand-primary font-medium text-sm">Learn more →</div>
            </div>
          </a>
        @endforeach
      </div>
      <div class="mt-8 md:mt-10">{{ $ministries->links() }}</div>
    @endif
  </section>

  <x-site.cta-band
    heading="Not sure where to start?"
    sub="Reach out and we will help you find something that fits."
    :cta="['url' => route('site.contact'), 'label' => 'Get in touch']"
  />

  @include('site._footer')
@endsection
