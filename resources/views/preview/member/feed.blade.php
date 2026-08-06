@php
$posts = [
    ['author' => 'Pastor Greg', 'initials' => 'PG', 'time' => '2 hours ago', 'pinned' => true,
     'title' => 'Welcome to the new portal',
     'body' => '<p>We are so glad to have you here. Take a few minutes to explore — submit a prayer request, knock for help if you need pastoral care, or just say hi.</p><p>This space is for our community to stay close even when we are not in the same room.</p>',
     'image' => 'https://picsum.photos/seed/feed-welcome/1200/600',
     'counts' => ['heart' => 24, 'pray' => 12, 'amen' => 18]],
    ['author' => 'Sarah Admin', 'initials' => 'SA', 'time' => 'Yesterday', 'pinned' => false,
     'title' => 'Sunday gathering: a special evening',
     'body' => '<p>Join us for an extended worship time at 6pm this Sunday. We will have communion together and time for personal prayer.</p>',
     'image' => null,
     'counts' => ['heart' => 8, 'pray' => 3, 'amen' => 5]],
    ['author' => 'Mike Davis', 'initials' => 'MD', 'time' => '3 days ago', 'pinned' => false,
     'title' => 'Volunteer call: kids ministry',
     'body' => '<p>We need 2 more volunteers for Sunday mornings. If you have a heart for kids, please reach out — even one Sunday a month makes a huge difference.</p>',
     'image' => 'https://picsum.photos/seed/feed-kids/1200/600',
     'counts' => ['heart' => 15, 'pray' => 2, 'amen' => 7]],
    ['author' => 'Pastor Greg', 'initials' => 'PG', 'time' => 'Last week', 'pinned' => false,
     'title' => null,
     'body' => '<p>A short reflection from Psalm 23 this morning: even in the valley, we are not alone. He restores my soul.</p>',
     'image' => null,
     'counts' => ['heart' => 32, 'pray' => 9, 'amen' => 14]],
    ['author' => 'Anna Wright', 'initials' => 'AW', 'time' => '2 weeks ago', 'pinned' => false,
     'title' => 'Easter photos are up',
     'body' => '<p>Massive thanks to everyone who came out for Easter. Photos from the morning are now in the gallery — find yourself and tag a friend.</p>',
     'image' => 'https://picsum.photos/seed/feed-easter/1200/600',
     'counts' => ['heart' => 41, 'pray' => 4, 'amen' => 19]],
];
@endphp
<x-member.layout title="Community Feed">
    <div class="max-w-2xl mx-auto px-4 py-10">
        <div class="mb-6 flex items-end justify-between">
            <div>
                <h1 class="font-serif text-3xl">Community Feed</h1>
                <p class="text-sm text-ink-muted mt-1">Latest from your church family.</p>
            </div>
        </div>

        @foreach($posts as $i => $post)
            <article x-data="{active:null, counts:{heart:{{ $post['counts']['heart'] }}, pray:{{ $post['counts']['pray'] }}, amen:{{ $post['counts']['amen'] }}}}" class="card p-6 mb-6">
                @if($post['pinned'])
                    <div class="inline-flex items-center gap-1 text-xs text-brand-primary font-medium mb-3">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2 9 9H4l4 3-1.5 7L12 15l5.5 4L16 12l4-3h-5z"/></svg>
                        Pinned
                    </div>
                @endif

                <header class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-brand-primary text-white flex items-center justify-center text-sm font-semibold">{{ $post['initials'] }}</div>
                    <div>
                        <div class="font-medium">{{ $post['author'] }}</div>
                        <div class="text-xs text-ink-muted">{{ $post['time'] }}</div>
                    </div>
                </header>

                @if($post['title'])
                    <h2 class="font-serif text-xl mb-2">{{ $post['title'] }}</h2>
                @endif

                <div class="prose prose-sm max-w-none text-ink">{!! $post['body'] !!}</div>

                @if($post['image'])
                    <img src="{{ $post['image'] }}" alt="" loading="lazy" class="mt-4 rounded-lg w-full max-h-96 object-cover">
                @endif

                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach(['heart' => '♥', 'pray' => '🙏', 'amen' => '👏'] as $kind => $emoji)
                        <button type="button"
                                @click="
                                    if (active === '{{ $kind }}') { counts['{{ $kind }}']--; active = null; }
                                    else { if (active) counts[active]--; counts['{{ $kind }}']++; active = '{{ $kind }}'; }
                                "
                                :class="active === '{{ $kind }}' ? 'bg-brand-primary text-white' : 'bg-surface hover:bg-brand-primary/10 text-ink'"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm transition">
                            <span>{{ $emoji }}</span>
                            <span x-text="counts['{{ $kind }}']">{{ $post['counts'][$kind] }}</span>
                        </button>
                    @endforeach
                </div>
            </article>
        @endforeach

        <div class="text-center mt-8 text-sm text-ink-muted">You've reached the end of the feed.</div>
    </div>
</x-member.layout>
