@extends('layouts.site')

@section('title', $page?->meta_title ?: (($page?->title ?: 'News').settings('seo.default_title_suffix', '')))

@include('site._meta')

@php
  $heroImg = site_img($page?->hero_image_path, 'blog-hero', 1800, 700);
  $featured = $posts->isNotEmpty() && $posts->currentPage() === 1 ? $posts->first() : null;
  $rest = $featured ? $posts->slice(1) : $posts;
@endphp

@section('content')
  <x-site.header :nav="$siteNav" />

  <x-site.hero
    :image="$heroImg"
    eyebrow="News &amp; updates"
    :heading="$page?->hero_heading ?: 'Stories and updates from our church.'"
    :sub="$page?->hero_subheading ?: 'Stay current with what God is doing among us.'"
    size="sm"
  />

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('site.home')],
      ['label' => 'News'],
    ]" />
  </div>

  <section class="max-w-container mx-auto px-4 py-8 md:py-12">
    {{-- Category chips --}}
    <div class="flex flex-wrap gap-2 mb-8">
      <a href="{{ route('site.blog.index') }}"
         class="px-4 py-1.5 rounded-full text-sm font-medium border transition
         {{ ! $activeCategory ? 'bg-brand-primary text-white border-brand-primary' : 'bg-white border-[rgb(var(--border))] text-ink hover:border-brand-primary hover:text-brand-primary' }}">
        All
      </a>
      @foreach($categories as $cat)
        <a href="{{ route('site.blog.index', ['category' => $cat->slug]) }}"
           class="px-4 py-1.5 rounded-full text-sm font-medium border transition
           {{ $activeCategory === $cat->slug ? 'bg-brand-primary text-white border-brand-primary' : 'bg-white border-[rgb(var(--border))] text-ink hover:border-brand-primary hover:text-brand-primary' }}">
          {{ $cat->name }}
        </a>
      @endforeach
    </div>

    @if($posts->isEmpty())
      <div class="card p-10 text-center text-ink-muted">No posts yet.</div>
    @else
      @if($featured)
        <a href="{{ route('site.blog.show', $featured) }}" class="card grid md:grid-cols-2 overflow-hidden mb-8 hover:-translate-y-1 hover:shadow-md transition">
          <div class="aspect-[4/3] md:aspect-auto bg-cover bg-center" style="background-image:url('{{ site_img($featured->featured_image_path, 'post-'.$featured->id, 1200, 700) }}')"></div>
          <div class="p-8 md:p-10 flex flex-col justify-center">
            <div class="text-xs uppercase tracking-wider text-brand-primary font-semibold mb-2">Featured @if($featured->category) · {{ $featured->category->name }} @endif</div>
            <h2 class="font-serif text-2xl md:text-3xl leading-tight">{{ $featured->title }}</h2>
            <p class="text-ink-muted mt-4 leading-relaxed">{{ $featured->excerpt }}</p>
            <div class="flex items-center gap-3 mt-6">
              <div class="w-10 h-10 rounded-full bg-brand-primary/15 text-brand-primary flex items-center justify-center font-semibold">{{ strtoupper(substr($featured->author?->name ?: 'S', 0, 1)) }}</div>
              <div class="text-sm">
                <div class="font-medium">{{ $featured->author?->name ?: 'Staff' }}</div>
                <div class="text-ink-muted">{{ optional($featured->published_at)->format('M j, Y') }}</div>
              </div>
            </div>
          </div>
        </a>
      @endif

      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($rest as $post)
          <x-site.card
            :image="site_img($post->featured_image_path, 'post-'.$post->id, 1200, 700)"
            :eyebrow="$post->category?->name"
            :title="$post->title"
            :meta="($post->author?->name ?: 'Staff').' · '.optional($post->published_at)->format('M j, Y')"
            :href="route('site.blog.show', $post)"
          />
        @endforeach
      </div>

      <div class="mt-10">{{ $posts->links() }}</div>
    @endif
  </section>

  @include('site._footer')
@endsection
