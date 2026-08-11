@extends('layouts.site')

@section('title', $folder->name.settings('seo.default_title_suffix', ''))

@include('site._meta')

@php
  $heroImg = site_img($photos->first()?->path ?? $page?->hero_image_path, 'album-'.$folder->id, 1800, 700);
@endphp

@section('content')
  <x-site.header :nav="$siteNav" />

  <x-site.hero
    :image="$heroImg"
    eyebrow="Gallery album"
    :heading="$folder->name"
    :sub="$photos->total().' '.Str::plural('photo', $photos->total()).' from our church family.'"
    size="sm"
  />

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('site.home')],
      ['label' => 'Gallery', 'url' => route('site.gallery.index')],
      ['label' => $folder->name],
    ]" />
  </div>

  <section class="gallery-page max-w-container mx-auto px-4 py-8 md:py-12">
    <div class="mb-6">
      <a href="{{ route('site.gallery.index') }}" class="text-sm font-medium text-brand-primary hover:underline">← All albums</a>
    </div>

    @include('site.gallery._lightbox', ['photos' => $photos])

    <div class="mt-8 md:mt-10">{{ $photos->withQueryString()->links() }}</div>
  </section>

  @include('site._footer')
@endsection
