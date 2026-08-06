@extends('layouts.site')

@section('title', 'Post · Grace Community')

@section('content')
  @php include resource_path('views/preview/_seed.php'); @endphp
  @php $post = $posts[0]; @endphp
  <x-site.header :nav="$siteNav" />

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('preview.home')],
      ['label' => 'Blog', 'url' => route('preview.blog')],
      ['label' => $post['title']],
    ]" />
  </div>

  <article class="max-w-3xl mx-auto px-4 pt-4 pb-16">
    <div class="flex flex-wrap items-center gap-3 mb-4">
      <span class="px-3 py-1 rounded-full bg-brand-secondary/30 text-ink font-medium text-xs uppercase tracking-wider">{{ $post['category'] }}</span>
      <span class="text-sm text-ink-muted">{{ $post['date'] }}</span>
    </div>
    <h1 class="font-serif text-4xl md:text-5xl leading-tight">{{ $post['title'] }}</h1>
    <div class="flex items-center gap-3 mt-6 pb-8 border-b border-[rgb(var(--border))]">
      <img src="{{ $post['authorPhoto'] }}" class="w-12 h-12 rounded-full" alt="">
      <div>
        <div class="font-medium">{{ $post['author'] }}</div>
        <div class="text-sm text-ink-muted">Senior Pastor · 6 min read</div>
      </div>
    </div>

    <div class="aspect-video rounded-2xl bg-cover bg-center my-10 shadow-md" style="background-image:url('{{ $post['image'] }}')"></div>

    <div class="prose prose-lg max-w-none">
      <p class="lead">{{ $post['excerpt'] }}</p>
      <p>Every now and then I run into one of those lists — "Ten habits of highly effective Christians," or whatever — and I close the tab feeling vaguely exhausted. The trouble with most of those lists is not that they are wrong. It is that they are loud. They promise transformation in language borrowed from the productivity world, and so the spiritual life ends up feeling like one more inbox to keep at zero.</p>
      <p>What I have actually watched do its slow, steady work in the lives of people I admire is much quieter. None of these will go viral. They will not feel heroic on any given Tuesday. But after a year, they leave a mark.</p>
      <h2>1. A small fixed time of day with the Bible open</h2>
      <p>Not an hour. Fifteen minutes is plenty. Same chair, same drink, same notebook. The point is not to feel something every time. The point is to keep showing up.</p>
      <h2>2. One honest friendship</h2>
      <p>Someone who has full permission to ask any question about your life and expect an unvarnished answer. We mostly do not have these. We do not need a dozen — just one.</p>
      <h2>3. A weekly Sabbath you actually take</h2>
      <p>It will feel inefficient and slightly anxious for the first month. Stay with it.</p>
      <h2>4. Generosity that costs you something</h2>
      <p>Not just leftover money. A planned, percentage-based giving practice that you actually notice.</p>
      <h2>5. Confession out loud, to a person</h2>
      <p>This is the hardest one. It is also the most freeing.</p>
      <blockquote>You will not recognize yourself a year from now — not because of any one of these, but because all five of them together quietly bend a life toward Christ.</blockquote>
    </div>

    {{-- Author block --}}
    <div class="card p-6 mt-12 flex flex-col sm:flex-row gap-5">
      <img src="{{ $post['authorPhoto'] }}" class="w-20 h-20 rounded-full flex-shrink-0" alt="">
      <div>
        <div class="text-xs uppercase tracking-wider text-brand-primary font-semibold mb-1">Author</div>
        <h3 class="font-serif text-xl">{{ $post['author'] }}</h3>
        <p class="text-sm text-ink-muted mt-2 leading-relaxed">Pastor David has served Grace Community since 2008. He and his wife Rachel have three kids and one stubborn golden retriever.</p>
      </div>
    </div>
  </article>

  {{-- Related posts --}}
  <section class="bg-white border-t border-[rgb(var(--border))] py-16">
    <div class="max-w-container mx-auto px-4">
      <x-site.section-heading eyebrow="Keep reading" heading="Related posts" />
      <div class="grid md:grid-cols-3 gap-6">
        @foreach(array_slice($posts, 1, 3) as $p)
          <x-site.card
            :image="$p['image']"
            :eyebrow="$p['category']"
            :title="$p['title']"
            :meta="$p['author'] . ' · ' . $p['date']"
            :href="route('preview.blog.show')"
          />
        @endforeach
      </div>
    </div>
  </section>

  @include('preview._footer')
@endsection
