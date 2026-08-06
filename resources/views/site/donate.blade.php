@extends('layouts.site')

@section('title', $page->meta_title ?: ($page->title.settings('seo.default_title_suffix', '')))

@include('site._meta')

@php
  $heroImg = site_img($page->hero_image_path, 'donate-hero-'.$page->id, 1800, 700);
@endphp

@section('content')
  <x-site.header :nav="$siteNav" />

  <x-site.hero
    :image="$heroImg"
    eyebrow="Give"
    :heading="$page->hero_heading ?: 'Support our mission'"
    :sub="$page->hero_subheading ?: 'Your generosity keeps the lights on, the doors open, and the ministry going.'"
    size="sm"
  />

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('site.home')],
      ['label' => 'Give'],
    ]" />
  </div>

  <section class="donate-page relative overflow-hidden">
    <div class="donate-page-bg" aria-hidden="true"></div>

    <div class="relative max-w-container mx-auto px-4 sm:px-6 py-10 md:py-14">
      <div class="donate-intro reveal mb-8 md:mb-10 max-w-3xl">
        <p class="text-xs uppercase tracking-[0.2em] text-brand-primary font-semibold">Ways to give</p>
        <p class="mt-2 text-ink-muted text-sm md:text-base leading-relaxed">
          Choose the option that works best for you. Every gift helps sustain worship, outreach, and care in our community.
        </p>
      </div>

      <div class="donate-content reveal prose prose-lg max-w-none">
        {!! site_render_html($page->body) !!}
      </div>

      <div class="donate-cta reveal mt-8 md:mt-10">
        <div class="donate-cta-inner">
          <div>
            <p class="font-serif text-xl md:text-2xl text-ink">Need help with giving?</p>
            <p class="text-ink-muted text-sm mt-1.5 max-w-xl">
              For gift aid, planned giving, or any other questions, our team is happy to help.
            </p>
          </div>
          <a href="{{ route('site.contact') }}" class="btn-primary shine-btn glow-primary shrink-0">
            <span>Contact us</span>
          </a>
        </div>
      </div>
    </div>
  </section>

  @include('site._footer')
@endsection
