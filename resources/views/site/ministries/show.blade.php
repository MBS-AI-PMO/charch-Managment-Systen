@extends('layouts.site')

@section('title', $ministry->name.settings('seo.default_title_suffix', ''))

@php
  $cover = site_img($ministry->cover_image_path, 'ministry-'.$ministry->id, 1800, 700);
@endphp

@section('content')
  <x-site.header :nav="$siteNav" />

  <x-site.hero
    :image="$cover"
    eyebrow="Ministry"
    :heading="$ministry->name"
    :sub="$ministry->summary"
    size="sm"
  />

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('site.home')],
      ['label' => 'Ministries', 'url' => route('site.ministries.index')],
      ['label' => $ministry->name],
    ]" />
  </div>

  <section class="max-w-container mx-auto px-4 py-8 md:py-12 grid lg:grid-cols-3 gap-8 md:gap-10 items-start">
    <div class="lg:col-span-2 prose prose-lg max-w-none">
      {!! site_render_html($ministry->body) !!}
    </div>

    <aside class="card p-6 lg:sticky lg:top-24 text-center">
      <div class="w-24 h-24 rounded-full bg-cover bg-center mx-auto mb-4 shadow-sm" style="background-image:url('{{ site_img(null, 'leader-'.$ministry->id, 300, 300) }}')"></div>
      @if($ministry->leader_name)
        <h4 class="font-serif text-xl">{{ $ministry->leader_name }}</h4>
        <p class="text-sm text-ink-muted">Ministry Leader</p>
      @endif
      @if($ministry->contact_email)
        <a href="mailto:{{ $ministry->contact_email }}" class="btn-primary w-full mt-5">Contact leader</a>
      @endif
      <a href="{{ route('site.contact') }}" class="btn-ghost w-full mt-2">Sign up to serve</a>
    </aside>
  </section>

  @if($related->isNotEmpty())
    <section class="bg-white border-y border-[rgb(var(--border))] py-10 md:py-14">
      <div class="max-w-container mx-auto px-4">
        <x-site.section-heading eyebrow="Other ways to belong" heading="More ministries" />
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach($related as $m)
            <x-site.card
              :image="site_img($m->cover_image_path, 'ministry-'.$m->id, 800, 600)"
              :title="$m->name"
              :meta="$m->summary"
              :href="route('site.ministries.show', $m)"
            />
          @endforeach
        </div>
      </div>
    </section>
  @endif

  @include('site._footer')
@endsection
