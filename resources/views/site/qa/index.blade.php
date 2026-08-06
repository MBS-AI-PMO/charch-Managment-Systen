@extends('layouts.site')

@section('title', 'Q&A'.settings('seo.default_title_suffix', ''))

@section('content')
  <x-site.header :nav="$siteNav" />

  <x-site.hero
    eyebrow="Questions &amp; answers"
    heading="What people asked — and how we answered."
    sub="A public history of questions from our community. Only names, questions, and answers are shown."
    size="sm"
  />

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('site.home')],
      ['label' => 'Q&A'],
    ]" />
  </div>

  <section class="qa-feed relative overflow-hidden">
    <div class="qa-feed-bg" aria-hidden="true"></div>

    <div class="relative max-w-container mx-auto px-4 sm:px-6 py-8 md:py-12">
      @if($items->isEmpty())
        <div class="qa-empty reveal text-center py-12 md:py-16 px-6 max-w-xl mx-auto">
          <div class="qa-empty-icon mx-auto mb-6" aria-hidden="true">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/>
            </svg>
          </div>
          <p class="font-serif text-2xl md:text-3xl text-ink">No answered questions yet.</p>
          <p class="text-ink-muted mt-3 leading-relaxed">When the team replies to a question and publishes it, it will appear here for everyone to learn from.</p>
          <button type="button" x-data @click="$dispatch('open-ask-question')" class="btn-primary shine-btn glow-primary mt-8">
            <span>Ask a question</span>
          </button>
        </div>
      @else
        <div class="qa-toolbar reveal mb-6 md:mb-8">
          <div>
            <p class="text-xs uppercase tracking-[0.2em] text-brand-primary font-semibold">Community Q&amp;A</p>
            <h2 class="font-serif text-2xl md:text-3xl text-ink mt-1">Shared answers</h2>
            <p class="mt-1.5 text-ink-muted text-sm">
              {{ $items->total() }} answered {{ $items->total() === 1 ? 'question' : 'questions' }}
              @if($items->hasPages())
                <span class="text-[rgb(var(--border))] mx-1.5">·</span>
                Page {{ $items->currentPage() }} of {{ $items->lastPage() }}
              @endif
            </p>
          </div>
          <button type="button" x-data @click="$dispatch('open-ask-question')" class="btn-primary shine-btn glow-primary text-sm shrink-0">
            <span>Ask a question</span>
          </button>
        </div>

        <ul class="qa-grid">
          @foreach($items as $item)
            @php
              $initials = collect(preg_split('/\s+/', trim($item->name) ?: 'A'))
                ->filter()
                ->take(2)
                ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                ->implode('');
              $delay = 'delay-'.(($loop->index % 4) + 1);
            @endphp
            <li class="qa-item reveal {{ $delay }}">
              <article class="qa-card">
                <header class="qa-card-head">
                  <div class="qa-avatar" aria-hidden="true">{{ $initials ?: 'A' }}</div>
                  <div class="min-w-0 flex-1">
                    <div class="font-serif text-lg text-ink leading-tight truncate">{{ $item->name }}</div>
                    <time class="mt-0.5 block text-xs text-ink-muted" datetime="{{ $item->replied_at?->toDateString() }}">
                      {{ $item->replied_at?->format('M j, Y') }}
                    </time>
                  </div>
                  <div class="qa-badge">Q&amp;A</div>
                </header>

                <div class="qa-question">
                  <div class="qa-label">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Question
                  </div>
                  <p class="qa-question-text">{{ $item->message }}</p>
                </div>

                <div class="qa-answer">
                  <div class="qa-label qa-label--answer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Answer from our team
                  </div>
                  @foreach($item->replies as $reply)
                    <p class="qa-answer-text {{ ! $loop->first ? 'mt-3 pt-3 border-t border-brand-primary/10' : '' }}">{{ $reply->body }}</p>
                  @endforeach
                </div>
              </article>
            </li>
          @endforeach
        </ul>

        <div class="qa-pagination mt-8 md:mt-10 reveal">
          {{ $items->onEachSide(1)->links() }}
        </div>

        <div class="qa-cta reveal mt-8 md:mt-10">
          <div>
            <p class="font-serif text-xl md:text-2xl text-ink">Have a question of your own?</p>
            <p class="text-ink-muted text-sm mt-1.5">Ask privately — we may share the answer here so others can benefit too.</p>
          </div>
          <button type="button" x-data @click="$dispatch('open-ask-question')" class="btn-primary shine-btn glow-primary shrink-0">
            <span>Ask a question</span>
          </button>
        </div>
      @endif
    </div>
  </section>

  @include('site._footer')
@endsection
