@php
    $catBadge = [
        'illness'   => 'bg-rose-50 text-rose-700',
        'grief'     => 'bg-indigo-50 text-indigo-700',
        'financial' => 'bg-amber-50 text-amber-700',
        'food'      => 'bg-emerald-50 text-emerald-700',
        'other'     => 'bg-slate-100 text-slate-700',
    ];
    $statusBadge = [
        'open'       => 'bg-slate-100 text-slate-700',
        'responding' => 'bg-amber-50 text-amber-700',
        'closed'     => 'bg-emerald-50 text-emerald-700',
    ];
    $cb = $catBadge[$care->category] ?? $catBadge['other'];
    $sb = $statusBadge[$care->status] ?? $statusBadge['open'];
@endphp
<x-member.layout title="Care request">
    <div class="member-shell space-y-6">
        <div class="text-xs text-ink-muted">
            <a href="{{ route('member.care.index') }}" class="hover:text-brand-primary">&larr; Back to Knock for help</a>
        </div>

        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="font-serif text-3xl">Care request #{{ $care->id }}</h1>
                <p class="text-ink-muted text-sm mt-1">{{ $care->created_at?->format('M j, Y · g:i A') }} &middot; {{ $care->created_at?->diffForHumans() }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex px-3 py-1 rounded-full text-xs {{ $cb }} capitalize">{{ $care->category }}</span>
                <span class="inline-flex px-3 py-1 rounded-full text-xs {{ $sb }} capitalize">{{ $care->status }}</span>
            </div>
        </div>

        <article class="card p-6 md:p-8 space-y-5">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <div class="text-xs text-ink-muted uppercase tracking-wider">Category</div>
                    <div class="font-medium mt-1 capitalize">{{ $care->category }}</div>
                </div>
                <div>
                    <div class="text-xs text-ink-muted uppercase tracking-wider">Share with team</div>
                    <div class="font-medium mt-1">{{ $care->share_with_team ? 'Yes' : 'No (pastor only)' }}</div>
                </div>
            </div>

            <div class="border-t border-[rgb(var(--border))] pt-5">
                <div class="text-xs text-ink-muted uppercase tracking-wider mb-2">Your message</div>
                <blockquote class="whitespace-pre-wrap text-sm leading-relaxed border-l-4 border-brand-secondary/40 pl-4 italic text-ink/90">{{ $care->message }}</blockquote>
            </div>

            @if($care->responder_id || $care->closed_at)
                <div class="border-t border-[rgb(var(--border))] pt-5 space-y-3">
                    <div class="text-xs text-ink-muted uppercase tracking-wider">Status timeline</div>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                            <span class="text-ink-muted">Submitted {{ $care->created_at?->diffForHumans() }}</span>
                        </li>
                        @if($care->responder_id)
                            <li class="flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                <span class="text-ink-muted">A pastor is responding ({{ optional($care->responder)->name ?? 'pastoral team' }})</span>
                            </li>
                        @endif
                        @if($care->closed_at)
                            <li class="flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span class="text-ink-muted">Closed {{ $care->closed_at?->diffForHumans() }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif
        </article>
    </div>
</x-member.layout>
