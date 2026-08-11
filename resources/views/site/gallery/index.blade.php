@extends('layouts.site')

@section('title', $page?->meta_title ?: (($page?->title ?: 'Gallery').settings('seo.default_title_suffix', '')))

@include('site._meta')

@php
  $heroImg = site_img($page?->hero_image_path, 'gallery-hero', 1800, 700);
@endphp

@section('content')
  <x-site.header :nav="$siteNav" />

  <x-site.hero
    :image="$heroImg"
    eyebrow="Moments from our church family"
    :heading="$page?->hero_heading ?: 'Gallery'"
    :sub="$page?->hero_subheading ?: 'A look at worship, fellowship, and life together.'"
    size="sm"
  />

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('site.home')],
      ['label' => 'Gallery'],
    ]" />
  </div>

  <section class="gallery-page max-w-container mx-auto px-4 py-8 md:py-12">
    @if($albums->isNotEmpty())
      <div class="reveal mb-10 md:mb-12">
        <div class="uppercase tracking-[0.18em] text-xs text-brand-primary font-semibold mb-2">Albums</div>
        <h2 class="font-serif text-2xl md:text-3xl leading-tight">Browse by collection</h2>
      </div>

      <div class="gallery-album-grid mb-12 md:mb-16">
        @foreach($albums as $i => $album)
          <a href="{{ route('site.gallery.show', $album) }}" class="gallery-album-card reveal delay-{{ ($i % 4) + 1 }}">
            <div class="gallery-album-cover" style="background-image:url('{{ site_img($album->cover?->path, 'album-'.$album->id, 900, 700) }}')"></div>
            <div class="gallery-album-body">
              <h3 class="gallery-album-title">{{ $album->name }}</h3>
              <p class="gallery-album-meta">{{ $album->images_count }} {{ Str::plural('photo', $album->images_count) }}</p>
            </div>
          </a>
        @endforeach
      </div>
    @endif

    <div class="reveal flex flex-wrap items-end justify-between gap-3 mb-6 md:mb-8">
      <div>
        <div class="uppercase tracking-[0.18em] text-xs text-brand-primary font-semibold mb-2">All photos</div>
        <h2 class="font-serif text-2xl md:text-3xl leading-tight">Recent moments</h2>
      </div>
    </div>

    @if($photos->isEmpty())
      <div class="card p-10 text-center text-ink-muted">
        Photos will appear here once they are added in the media library.
      </div>
    @else
      @include('site.gallery._lightbox', ['photos' => $photos])
      <div class="mt-8 md:mt-10">{{ $photos->links() }}</div>
    @endif
  </section>

  <x-site.cta-band
    heading="Want to be part of the next chapter?"
    sub="Join us this Sunday — there is a place for you here."
    :cta="['url' => route('site.contact'), 'label' => 'Plan your visit']"
  />

  @include('site._footer')
@endsection
