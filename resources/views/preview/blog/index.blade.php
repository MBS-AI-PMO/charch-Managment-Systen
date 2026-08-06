@extends('layouts.site')

@section('title', 'Blog · Grace Community')

@section('content')
  @php include resource_path('views/preview/_seed.php'); @endphp
  <x-site.header :nav="$siteNav" />

  <x-site.hero
    image="https://picsum.photos/seed/blog-hero/1800/700"
    eyebrow="Read & reflect"
    heading="Words to carry into the week."
    sub="Reflections from our pastors and team on faith, family, work, and following Jesus in the everyday."
    size="sm"
  />

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('preview.home')],
      ['label' => 'Blog'],
    ]" />
  </div>

  <section class="max-w-container mx-auto px-4 py-12">
    {{-- Category chips --}}
    <div class="flex flex-wrap gap-2 mb-12">
      @foreach($blogCategories as $i => $cat)
        <a href="#" class="px-4 py-1.5 rounded-full text-sm font-medium border transition
                  {{ $i === 0 ? 'bg-brand-primary text-white border-brand-primary' : 'bg-white border-[rgb(var(--border))] text-ink hover:border-brand-primary hover:text-brand-primary' }}">
          {{ $cat }}
        </a>
      @endforeach
    </div>

    {{-- Featured post --}}
    @php $featured = $posts[0]; @endphp
    <a href="{{ route('preview.blog.show') }}" class="card grid md:grid-cols-2 overflow-hidden mb-12 hover:-translate-y-1 hover:shadow-md transition">
      <div class="aspect-[4/3] md:aspect-auto bg-cover bg-center" style="background-image:url('{{ $featured['image'] }}')"></div>
      <div class="p-8 md:p-10 flex flex-col justify-center">
        <div class="text-xs uppercase tracking-wider text-brand-primary font-semibold mb-2">Featured · {{ $featured['category'] }}</div>
        <h2 class="font-serif text-2xl md:text-3xl leading-tight">{{ $featured['title'] }}</h2>
        <p class="text-ink-muted mt-4 leading-relaxed">{{ $featured['excerpt'] }}</p>
        <div class="flex items-center gap-3 mt-6">
          <img src="{{ $featured['authorPhoto'] }}" class="w-10 h-10 rounded-full" alt="">
          <div class="text-sm">
            <div class="font-medium">{{ $featured['author'] }}</div>
            <div class="text-ink-muted">{{ $featured['date'] }}</div>
          </div>
        </div>
      </div>
    </a>

    {{-- Post grid --}}
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      @foreach(array_slice($posts, 1) as $post)
        <x-site.card
          :image="$post['image']"
          :eyebrow="$post['category']"
          :title="$post['title']"
          :meta="$post['author'] . ' · ' . $post['date']"
          :href="route('preview.blog.show')"
        />
      @endforeach
    </div>

    {{-- Pagination --}}
    <div class="flex justify-center gap-1 mt-16">
      <button class="w-9 h-9 rounded-md border border-[rgb(var(--border))] text-ink-muted hover:bg-white" disabled>‹</button>
      <button class="w-9 h-9 rounded-md bg-brand-primary text-white">1</button>
      <button class="w-9 h-9 rounded-md border border-[rgb(var(--border))] hover:bg-white">2</button>
      <button class="w-9 h-9 rounded-md border border-[rgb(var(--border))] hover:bg-white">3</button>
      <button class="w-9 h-9 rounded-md border border-[rgb(var(--border))] hover:bg-white">›</button>
    </div>
  </section>

  @include('preview._footer')
@endsection
