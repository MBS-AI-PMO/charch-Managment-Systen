@extends('layouts.site')

@section('title', $page?->meta_title ?: (($page?->title ?: 'Sermons').settings('seo.default_title_suffix', '')))

@include('site._meta')

@php
  $heroImg = site_img($page?->hero_image_path, 'sermons-hero', 1800, 700);
@endphp

@section('content')
  <x-site.header :nav="$siteNav" />

  <x-site.hero
    :image="$heroImg"
    eyebrow="Watch · Listen · Reflect"
    :heading="$page?->hero_heading ?: 'Sermons that meet you where you are.'"
    :sub="$page?->hero_subheading ?: 'A growing library of teachings from our pastors.'"
    size="sm"
  />

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('site.home')],
      ['label' => 'Sermons'],
    ]" />
  </div>

  <section class="max-w-container mx-auto px-4 py-8 md:py-12">
    {{-- Filter row --}}
    <form method="get" class="card p-5 mb-10">
      <div class="grid md:grid-cols-4 gap-3">
        <select name="series" class="input" onchange="this.form.submit()">
          <option value="">All series</option>
          @foreach($allSeries as $s)
            <option value="{{ $s->slug }}" @selected(request('series') === $s->slug)>{{ $s->name }}</option>
          @endforeach
        </select>
        <select name="speaker" class="input" onchange="this.form.submit()">
          <option value="">All speakers</option>
          @foreach($allSpeakers as $sp)
            <option value="{{ $sp->slug }}" @selected(request('speaker') === $sp->slug)>{{ $sp->name }}</option>
          @endforeach
        </select>
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search sermons…" class="input md:col-span-2" />
      </div>
    </form>

    <div class="grid lg:grid-cols-4 gap-10">
      <div class="lg:col-span-3">
        @if($sermons->isEmpty())
          <div class="card p-10 text-center text-ink-muted">No sermons published yet.</div>
        @else
          <div class="grid sm:grid-cols-2 gap-6">
            @foreach($sermons as $sermon)
              <x-site.card
                :image="site_img($sermon->thumbnail_path, 'sermon-'.$sermon->id, 1200, 675)"
                :eyebrow="$sermon->series?->name"
                :title="$sermon->title"
                :meta="($sermon->speaker?->name ?: 'Staff').' · '.($sermon->preached_on?->format('M j, Y'))"
                :href="route('site.sermons.show', $sermon)"
              />
            @endforeach
          </div>
          <div class="mt-8 md:mt-10">
            {{ $sermons->links() }}
          </div>
        @endif
      </div>

      <aside class="lg:sticky lg:top-24 lg:self-start space-y-8">
        @if($allSeries->isNotEmpty())
          <div>
            <div class="uppercase tracking-[0.18em] text-xs text-brand-primary font-semibold mb-4">Series</div>
            <div class="space-y-4">
              @foreach($allSeries->take(6) as $s)
                <a href="{{ route('site.sermons.index', ['series' => $s->slug]) }}" class="flex gap-3 group items-center">
                  <div class="w-16 h-16 rounded-lg bg-cover bg-center flex-shrink-0" style="background-image:url('{{ site_img($s->cover_image_path, 'series-'.$s->id, 200, 200) }}')"></div>
                  <div class="min-w-0">
                    <div class="font-serif text-base leading-snug group-hover:text-brand-primary transition-colors">{{ $s->name }}</div>
                  </div>
                </a>
              @endforeach
            </div>
          </div>
        @endif
      </aside>
    </div>
  </section>

  @include('site._footer')
@endsection
