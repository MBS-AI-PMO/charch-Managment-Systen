@extends('layouts.site')

@section('title', $post->title.settings('seo.default_title_suffix', ''))

@push('head')
  @if($post->meta_description)
    <meta name="description" content="{{ $post->meta_description }}">
  @elseif($post->excerpt)
    <meta name="description" content="{{ $post->excerpt }}">
  @endif
  <meta property="og:title" content="{{ $post->meta_title ?: $post->title }}">
  @if($post->excerpt)
    <meta property="og:description" content="{{ $post->excerpt }}">
  @endif
@endpush


@section('content')
  <x-site.header :nav="$siteNav" />

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('site.home')],
      ['label' => 'News', 'url' => route('site.blog.index')],
      ['label' => $post->title],
    ]" />
  </div>

  <article class="max-w-3xl mx-auto px-4 pt-4 pb-10 md:pb-14">
    <div class="flex flex-wrap items-center gap-3 mb-4">
      @if($post->category)
        <span class="px-3 py-1 rounded-full bg-brand-secondary/30 text-ink font-medium text-xs uppercase tracking-wider">{{ $post->category->name }}</span>
      @endif
      <span class="text-sm text-ink-muted">{{ optional($post->published_at)->format('M j, Y') }}</span>
    </div>
    <h1 class="font-serif text-4xl md:text-5xl leading-tight">{{ $post->title }}</h1>
    <div class="flex items-center gap-3 mt-6 pb-8 border-b border-[rgb(var(--border))]">
      <div class="w-12 h-12 rounded-full bg-brand-primary/15 text-brand-primary flex items-center justify-center font-semibold text-lg">{{ strtoupper(substr($post->author?->name ?: 'S', 0, 1)) }}</div>
      <div>
        <div class="font-medium">{{ $post->author?->name ?: 'Staff' }}</div>
        <div class="text-sm text-ink-muted">{{ str_word_count(strip_tags($post->body)) > 0 ? ceil(str_word_count(strip_tags($post->body)) / 200).' min read' : '' }}</div>
      </div>
    </div>

    <div class="aspect-video rounded-2xl bg-cover bg-center my-10 shadow-md" style="background-image:url('{{ site_img($post->featured_image_path, 'post-'.$post->id, 1200, 700) }}')"></div>

    <div class="prose prose-lg max-w-none">
      @if($post->excerpt)
        <p class="lead">{{ $post->excerpt }}</p>
      @endif
      {!! site_render_html($post->body) !!}
    </div>
  </article>

  @if($related->isNotEmpty())
    <section class="bg-white border-t border-[rgb(var(--border))] py-10 md:py-14">
      <div class="max-w-container mx-auto px-4">
        <x-site.section-heading eyebrow="Keep reading" heading="Related posts" />
        <div class="grid md:grid-cols-3 gap-6">
          @foreach($related as $p)
            <x-site.card
              :image="site_img($p->featured_image_path, 'post-'.$p->id, 1200, 700)"
              :eyebrow="$p->category?->name"
              :title="$p->title"
              :meta="($p->author?->name ?: 'Staff').' · '.optional($p->published_at)->format('M j, Y')"
              :href="route('site.blog.show', $p)"
            />
          @endforeach
        </div>
      </div>
    </section>
  @endif

  @include('site._footer')
@endsection
