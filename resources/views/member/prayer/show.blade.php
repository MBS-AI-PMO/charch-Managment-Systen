@php
    $author = $prayer->displayName();
    $isOwner = $prayer->user_id === auth()->id();
    $statusMap = [
        'pending'  => ['bg-slate-100','text-slate-700'],
        'praying'  => ['bg-amber-50','text-amber-700'],
        'answered' => ['bg-emerald-50','text-emerald-700'],
        'closed'   => ['bg-slate-100','text-slate-600'],
    ];
    [$bg,$tx] = $statusMap[$prayer->status ?? 'pending'] ?? $statusMap['pending'];
@endphp
<x-member.layout title="{{ $prayer->title }}">
    <div class="member-shell space-y-6">
        <a href="{{ route('member.prayer.index') }}" class="text-sm text-ink-muted hover:text-brand-primary">&larr; Back to prayer wall</a>

        <article class="card p-6 md:p-8 space-y-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-full bg-brand-primary/10 text-brand-primary flex items-center justify-center font-medium">{{ mb_substr($author, 0, 1) }}</span>
                    <div>
                        <div class="text-sm font-medium">{{ $author }}</div>
                        <div class="text-xs text-ink-muted">{{ $prayer->created_at?->diffForHumans() }}</div>
                    </div>
                </div>
                <span class="inline-flex px-3 py-1 rounded-full text-xs {{ $bg }} {{ $tx }} capitalize">{{ $prayer->status ?? 'pending' }}</span>
            </div>

            <h1 class="font-serif text-3xl">{{ $prayer->title }}</h1>

            <div class="prose prose-sm max-w-none text-ink/90 leading-relaxed whitespace-pre-wrap">{{ $prayer->body }}</div>

            @if(!$isOwner && $prayer->is_public)
                <div class="border-t border-[rgb(var(--border))] pt-5">
                    <form method="POST" action="{{ route('member.prayer.pray', $prayer) }}">
                        @csrf
                        <button type="submit" class="w-full sm:w-auto text-sm border border-[rgb(var(--border))] hover:border-brand-primary hover:text-brand-primary rounded-md px-6 py-2.5 transition-colors flex items-center justify-center gap-2">
                            <span>&#128591;</span>
                            <span>I'm praying for {{ $author }}</span>
                            <span class="text-xs opacity-70">({{ $prayer->pray_count }})</span>
                        </button>
                    </form>
                </div>
            @else
                <div class="border-t border-[rgb(var(--border))] pt-5 text-sm text-ink-muted">
                    <span class="inline-flex items-center gap-2">&#128591; {{ $prayer->pray_count }} {{ \Illuminate\Support\Str::plural('person', $prayer->pray_count) }} praying.</span>
                </div>
            @endif

            @if($isOwner)
                <div class="pt-2">
                    <form method="POST" action="{{ route('member.prayer.destroy', $prayer) }}" onsubmit="return confirm('Delete this prayer request?')">
                        @csrf @method('DELETE')
                        <x-row-action type="delete" />
                    </form>
                </div>
            @endif
        </article>
    </div>
</x-member.layout>
