<x-member.layout title="Community Feed">
    <div class="max-w-2xl mx-auto px-4 py-10">
        <div class="mb-6 flex items-end justify-between">
            <div>
                <h1 class="font-serif text-3xl">Community Feed</h1>
                <p class="text-sm text-ink-muted mt-1">Latest from your church family.</p>
            </div>
        </div>

        @forelse($posts as $post)
            @php
                $counts = $post->reactionCountsByKind();
                $my = $myReactions[$post->id] ?? null;
                $authorName = $post->author?->name ?? 'Church';
                $initials = collect(explode(' ', $authorName))
                    ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                    ->take(2)
                    ->join('');
            @endphp
            <article class="card p-6 mb-6">
                @if($post->pinned)
                    <div class="inline-flex items-center gap-1 text-xs text-brand-primary font-medium mb-3">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2 9 9H4l4 3-1.5 7L12 15l5.5 4L16 12l4-3h-5z"/></svg>
                        Pinned
                    </div>
                @endif

                <header class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-brand-primary text-white flex items-center justify-center text-sm font-semibold">{{ $initials ?: 'CH' }}</div>
                    <div>
                        <div class="font-medium">{{ $authorName }}</div>
                        <div class="text-xs text-ink-muted">{{ $post->published_at?->diffForHumans() }}</div>
                    </div>
                </header>

                @if($post->title)
                    <h2 class="font-serif text-xl mb-2">{{ $post->title }}</h2>
                @endif

                <div class="prose prose-sm max-w-none text-ink">{!! $post->body !!}</div>

                @if($post->image_path)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($post->image_path) }}" alt="" loading="lazy" class="mt-4 rounded-lg w-full max-h-96 object-cover">
                @endif

                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach(['heart' => '♥', 'pray' => '🙏', 'amen' => '👏'] as $k => $emoji)
                        <form method="POST" action="{{ route('member.feed.react', $post) }}" class="inline">
                            @csrf
                            <input type="hidden" name="kind" value="{{ $k }}">
                            <button type="submit" @class([
                                'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm transition',
                                'bg-brand-primary text-white' => $my === $k,
                                'bg-surface hover:bg-brand-primary/10 text-ink' => $my !== $k,
                            ])>
                                <span>{{ $emoji }}</span>
                                <span>{{ $counts[$k] ?? 0 }}</span>
                            </button>
                        </form>
                    @endforeach
                </div>
            </article>
        @empty
            <div class="card p-10 text-center text-ink-muted">
                <p class="text-base">The feed is quiet right now.</p>
                <p class="text-sm mt-1">Check back later for updates from the team.</p>
            </div>
        @endforelse

        @if($posts->hasPages())
            <div class="mt-8">{{ $posts->links() }}</div>
        @else
            @if($posts->isNotEmpty())
                <div class="text-center mt-8 text-sm text-ink-muted">You've reached the end of the feed.</div>
            @endif
        @endif
    </div>
</x-member.layout>
