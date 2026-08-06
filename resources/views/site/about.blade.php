@extends('layouts.site')

@section('title', $page->meta_title ?: ($page->title.settings('seo.default_title_suffix', '')))

@include('site._meta')

@php
  $heroImg = site_img($page->hero_image_path, 'about-hero-'.$page->id, 1800, 700);
@endphp

@section('content')
  <x-site.header :nav="$siteNav" />

  <x-site.hero
    :image="$heroImg"
    eyebrow="Our story"
    :heading="$page->hero_heading ?: $page->title"
    :sub="$page->hero_subheading"
    size="sm"
  />

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('site.home')],
      ['label' => 'About'],
    ]" />
  </div>

  <section class="max-w-container mx-auto px-4 py-12 md:py-16 grid md:grid-cols-2 gap-8 md:gap-12 items-center">
    <div class="relative">
      <div class="aspect-square rounded-2xl bg-cover bg-center shadow-lg" style="background-image:url('{{ site_img('uploads/pages/yp0EeGYMexBpkizJSy5KhUkpwljsA5xXk51ZQABI.jpg', 'about-story-'.$page->id, 800, 800) }}')"></div>
      <div class="absolute -bottom-6 -right-6 hidden md:block bg-brand-primary text-white rounded-xl px-6 py-4 shadow-lg font-serif text-lg max-w-xs">
        {{ settings('brand.name', 'Assemblies of God') }}
        @if(settings('brand.tagline'))
          <span class="block text-sm font-sans font-normal opacity-80 mt-0.5">{{ settings('brand.tagline') }}</span>
        @endif
      </div>
    </div>
    <div>
      <x-site.section-heading
        eyebrow="About us"
        :heading="$page->title"
      />
      <div class="space-y-4 text-ink-muted leading-relaxed">
        {!! site_render_html($page->body) !!}
      </div>
    </div>
  </section>

  <x-site.cta-band
    heading="Come and see for yourself."
    :sub="settings('contact.service_times', 'Sunday gatherings — there is a seat saved with your name on it.')"
    :cta="['url' => route('site.contact'), 'label' => 'Plan your visit']"
  />

  @include('site._footer')
@endsection
