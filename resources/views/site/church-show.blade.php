@extends('layouts.site')

@section('title', $branch->name.settings('seo.default_title_suffix', ''))

@include('site._meta')

@php
  $heroImg = site_img($page?->hero_image_path, 'churches-hero', 1800, 700);
@endphp

@section('content')
  <x-site.header :nav="$siteNav" />

  <x-site.hero
    :image="$heroImg"
    eyebrow="Our Churches"
    :heading="$branch->name"
    :sub="$branch->hero_sub ?: $branch->note"
    size="sm"
  />

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('site.home')],
      ['label' => 'Our Churches', 'url' => route('site.churches')],
      ['label' => $branch->name],
    ]" />
  </div>

  <section class="churches-page">
    <div class="churches-page-bg" aria-hidden="true"></div>

    <div class="relative max-w-container mx-auto px-4 sm:px-6 py-10 md:py-14">
      <a class="church-back" href="{{ route('site.churches') }}">← Back to Our Churches</a>
      <div class="church-detail-grid">
        <div class="church-detail-main reveal">
          <p class="about-kicker">{{ $branch->role }}</p>
          <h2 class="about-title mt-2">{{ $branch->name }}</h2>
          <p class="church-card-city mt-2">{{ $branch->city }}</p>

          <div class="about-lead-text mt-5">
            @foreach($branch->aboutParagraphs() as $paragraph)
              <p>{{ $paragraph }}</p>
            @endforeach
          </div>

          @if($branch->expectItems())
            <h3 class="church-detail-heading">What to expect on Sunday</h3>
            <ul class="church-detail-list">
              @foreach($branch->expectItems() as $item)
                <li>{{ $item }}</li>
              @endforeach
            </ul>
          @endif

          @if($branch->ministryItems())
            <h3 class="church-detail-heading">Ministries and life together</h3>
            <ul class="church-detail-list">
              @foreach($branch->ministryItems() as $item)
                <li>{{ $item }}</li>
              @endforeach
            </ul>
          @endif

          <div class="church-detail-blocks">
            @if($branch->families)
              <article class="church-card">
                <span class="church-card-role">Families</span>
                <p class="church-card-note">{{ $branch->families }}</p>
              </article>
            @endif
            @if($branch->getting_here)
              <article class="church-card">
                <span class="church-card-role">Getting here</span>
                <p class="church-card-note">{{ $branch->getting_here }}</p>
              </article>
            @endif
            @if($branch->visit)
              <article class="church-card">
                <span class="church-card-role">First visit</span>
                <p class="church-card-note">{{ $branch->visit }}</p>
              </article>
            @endif
          </div>
        </div>

        <aside class="church-card church-detail-card reveal delay-2">
          <span class="church-card-role">Visit details</span>
          @if($branch->pastor)
            <h3 class="church-card-name">{{ $branch->pastor }}</h3>
            <p class="church-card-city">Pastor</p>
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
            @if($branch->language)
              <li>
                <span class="church-card-meta-label">Language</span>
                <span>{{ $branch->language }}</span>
              </li>
            @endif
            @if($branch->displayPhone())
              <li>
                <span class="church-card-meta-label">Phone</span>
                <a href="tel:{{ preg_replace('/\s+/', '', $branch->displayPhone()) }}">{{ $branch->displayPhone() }}</a>
              </li>
            @endif
            @if($branch->displayEmail())
              <li>
                <span class="church-card-meta-label">Email</span>
                <a href="mailto:{{ $branch->displayEmail() }}">{{ $branch->displayEmail() }}</a>
              </li>
            @endif
          </ul>

          <a href="{{ route('site.contact') }}" class="btn-primary w-full mt-5">Contact this church</a>
          <a href="{{ route('site.churches') }}" class="btn-ghost w-full mt-2">All churches</a>
        </aside>
      </div>
    </div>
  </section>

  <x-site.cta-band
    heading="We would love to meet you"
    :sub="'Come this Sunday — there is a seat for you at '.$branch->name.'.'"
    :cta="['url' => route('site.contact'), 'label' => 'Get in touch']"
  />

  @include('site._footer')
@endsection
