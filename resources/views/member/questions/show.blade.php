<x-member.layout title="Question #{{ $question->id }}">
    <div class="max-w-3xl mx-auto px-4 py-10 space-y-6">
        <div class="text-xs text-ink-muted">
            <a href="{{ route('member.questions.index') }}" class="hover:text-brand-primary">&larr; Back to My questions</a>
        </div>

        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="font-serif text-3xl">{{ $question->subject }}</h1>
                <p class="text-ink-muted text-sm mt-1">{{ $question->created_at?->format('M j, Y · g:i A') }} &middot; {{ $question->created_at?->diffForHumans() }}</p>
            </div>
            @if($question->replied_at)
                <span class="inline-flex px-3 py-1 rounded-full text-xs bg-emerald-50 text-emerald-700">Replied</span>
            @else
                <span class="inline-flex px-3 py-1 rounded-full text-xs bg-amber-50 text-amber-700">Awaiting reply</span>
            @endif
        </div>

        <article class="card p-6 md:p-8 space-y-5">
            <div>
                <div class="text-xs text-ink-muted uppercase tracking-wider mb-2">Your question</div>
                <blockquote class="whitespace-pre-wrap text-sm leading-relaxed border-l-4 border-brand-secondary/40 pl-4 italic text-ink/90">{{ $question->message }}</blockquote>
            </div>

            <div class="border-t border-[rgb(var(--border))] pt-5 space-y-4">
                <div class="text-xs text-ink-muted uppercase tracking-wider">Conversation</div>

                @forelse($question->replies as $reply)
                    <div @class([
                        'rounded-lg border p-4',
                        'border-brand-primary/20 bg-brand-primary/[0.04]' => $reply->is_staff,
                        'border-[rgb(var(--border))] bg-surface' => ! $reply->is_staff,
                    ])>
                        <div class="flex items-center justify-between gap-3 mb-2">
                            <div class="text-sm font-medium">
                                {{ $reply->is_staff ? ($reply->author?->name ?? 'Church team') : 'You' }}
                            </div>
                            <div class="text-xs text-ink-muted whitespace-nowrap">{{ $reply->created_at?->format('M j, Y g:i A') }}</div>
                        </div>
                        <div class="whitespace-pre-wrap text-sm leading-relaxed">{{ $reply->body }}</div>
                    </div>
                @empty
                    <p class="text-sm text-ink-muted italic">No reply yet — our team will get back to you soon.</p>
                @endforelse
            </div>
        </article>
    </div>
</x-member.layout>
