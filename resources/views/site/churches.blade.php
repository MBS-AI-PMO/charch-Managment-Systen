@extends('layouts.site')

@section('title', ($page?->meta_title) ?: (($page?->title ?: 'Our Churches').settings('seo.default_title_suffix', '')))

@include('site._meta')

@php
  $heroImg = site_img($page?->hero_image_path, 'churches-hero', 1800, 700);
  $brand = settings('brand.name', 'Assemblies of God');
@endphp

@section('content')
  <x-site.header :nav="$siteNav" />

  <x-site.hero
    :image="$heroImg"
    eyebrow="Family of churches"
    :heading="$page?->hero_heading ?: 'Our Churches'"
    :sub="$page?->hero_subheading ?: 'One family across every campus and fellowship — find a church near you.'"
    size="sm"
  />

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('site.home')],
      ['label' => 'Our Churches'],
    ]" />
  </div>

  <section class="churches-page">
    <div class="churches-page-bg" aria-hidden="true"></div>

    <div class="relative max-w-container mx-auto px-4 sm:px-6 py-10 md:py-14">
      <div class="churches-intro reveal max-w-3xl">
        <p class="about-kicker">Our Churches</p>
        <h2 class="about-title mt-2">{{ $page?->title ?: 'Find a place to belong' }}</h2>
        <div class="about-lead-text mt-4">
          @if($page?->body)
            {!! site_render_html($page->body) !!}
          @else
            <p>
              {{ $brand }} is a family of congregations and fellowships. Visit a campus below,
              check service times, and come as you are — there is a seat for you.
            </p>
          @endif
        </div>
      </div>

      <div class="churches-grid mt-8 md:mt-10">
        @foreach($branches as $i => $branch)
          <a class="church-card reveal delay-{{ ($i % 3) + 1 }}" href="{{ route('site.churches.show', $branch) }}">
            <div class="church-card-top">
              <span class="church-card-role">{{ $branch->role }}</span>
              <h3 class="church-card-name">{{ $branch->name }}</h3>
              <p class="church-card-city">{{ $branch->city }}</p>
            </div>

            @if($branch->note)
              <p class="church-card-note">{{ $branch->note }}</p>
            @endif

            <ul class="church-card-meta">
              @if($branch->address)
                <li>
                  <span class="church-card-meta-label">Address</span>
                  <span>{{ $branch->address }}</span>
                </li>
              @endif
              @if($branch->services)
                <li>
                  <span class="church-card-meta-label">Services</span>
                  <span class="whitespace-pre-line">{{ $branch->services }}</span>
                </li>
              @endif
              @if($branch->displayPhone())
                <li>
                  <span class="church-card-meta-label">Phone</span>
                  <span class="church-card-accent">{{ $branch->displayPhone() }}</span>
                </li>
              @endif
              @if($branch->displayEmail())
                <li>
                  <span class="church-card-meta-label">Email</span>
                  <span class="church-card-accent">{{ $branch->displayEmail() }}</span>
                </li>
              @endif
            </ul>
            <span class="church-card-cta">View church details →</span>
          </a>
        @endforeach
      </div>
    </div>
  </section>

  <x-site.cta-band
    heading="Planning a visit?"
    :sub="'We would love to welcome you at any of our churches.'"
    :cta="['url' => route('site.contact'), 'label' => 'Contact us']"
  />

  @include('site._footer')
@endsection
