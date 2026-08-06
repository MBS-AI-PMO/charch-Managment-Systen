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

    $activity = \App\Models\ActivityLog::where('subject_type', \App\Models\CareRequest::class)
        ->where('subject_id', $care->id)
        ->with('user')
        ->latest()
        ->limit(10)
        ->get();
@endphp
<x-admin.layout title="Care request #{{ $care->id }}">
    <div class="space-y-6 max-w-4xl">
        <div class="flex items-end justify-between flex-wrap gap-3">
            <div>
                <div class="text-xs text-ink-muted mb-1">
                    <a href="{{ route('admin.care.index') }}" class="hover:underline">Knock for Help</a>
                    <span class="mx-1">/</span>
                    <span>#{{ $care->id }}</span>
                </div>
                <h1 class="text-2xl font-serif">Care request from {{ $care->user?->name ?? 'Unknown' }}</h1>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex px-3 py-1 rounded-full text-xs {{ $cb }} capitalize">{{ $care->category }}</span>
                <span class="inline-flex px-3 py-1 rounded-full text-xs {{ $sb }} capitalize">{{ $care->status }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-5">
                <div class="card p-5 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <div class="text-xs text-ink-muted uppercase tracking-wider">Member</div>
                            <div class="font-medium mt-1">{{ $care->user?->name ?? 'Unknown' }}</div>
                            @if($care->user?->email)
                                <div class="text-ink-muted text-xs">
                                    <a href="mailto:{{ $care->user->email }}" class="hover:text-brand-primary">{{ $care->user->email }}</a>
                                </div>
                            @endif
                            @if($care->user?->phone)
                                <div class="text-ink-muted text-xs">
                                    <a href="tel:{{ $care->user->phone }}" class="hover:text-brand-primary">{{ $care->user->phone }}</a>
                                </div>
                            @endif
                        </div>
                        <div>
                            <div class="text-xs text-ink-muted uppercase tracking-wider">Submitted</div>
                            <div class="font-medium mt-1">{{ $care->created_at?->format('M j, Y g:i A') }}</div>
                            <div class="text-ink-muted text-xs">{{ $care->created_at?->diffForHumans() }}</div>
                            <div class="text-ink-muted text-xs mt-1">Share with team: {{ $care->share_with_team ? 'Yes' : 'No (pastor only)' }}</div>
                            @if($care->responder)
                                <div class="text-ink-muted text-xs mt-1">Responder: {{ $care->responder->name }}</div>
                            @endif
                            @if($care->closed_at)
                                <div class="text-ink-muted text-xs">Closed: {{ $care->closed_at->format('M j, Y g:i A') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="border-t border-[rgb(var(--border))] pt-4">
                        <div class="text-xs text-ink-muted uppercase tracking-wider mb-2">Message</div>
                        <blockquote class="whitespace-pre-wrap text-sm leading-relaxed border-l-4 border-brand-secondary/40 pl-4 italic text-ink/90">{{ $care->message }}</blockquote>
                    </div>
                </div>

                <div class="card p-5">
                    <h3 class="text-sm font-medium mb-3">Activity history</h3>
                    @if($activity->isEmpty())
                        <p class="text-sm text-ink-muted italic">No activity yet.</p>
                    @else
                        <ul class="space-y-3">
                            @foreach($activity as $a)
                                <li class="flex items-start gap-3 text-sm">
                                    <span class="w-2 h-2 rounded-full bg-brand-primary/60 mt-2 shrink-0"></span>
                                    <div>
                                        <div>
                                            <span class="font-medium">{{ $a->user?->name ?? 'System' }}</span>
                                            <span class="text-ink-muted">{{ $a->action }}</span>
                                        </div>
                                        <div class="text-xs text-ink-muted">{{ $a->created_at?->diffForHumans() }}</div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <aside class="space-y-5">
                <form method="POST" action="{{ route('admin.care.update', $care) }}" class="card p-5 space-y-4">
                    @csrf @method('PUT')
                    <h3 class="text-sm font-medium">Manage</h3>

                    <div>
                        <label class="block text-xs text-ink-muted uppercase tracking-wider mb-1.5">Status</label>
                        <select name="status" class="input w-full">
                            @foreach(['open','responding','closed'] as $s)
                                <option value="{{ $s }}" @selected(old('status', $care->status) === $s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                        @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs text-ink-muted uppercase tracking-wider mb-1.5">Response notes</label>
                        <textarea name="response_notes" rows="8" class="ck-editor input">{{ old('response_notes', $care->response_notes) }}</textarea>
                        @error('response_notes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="btn-primary text-sm w-full">Save changes</button>
                </form>
            </aside>
        </div>

        <div>
            <a href="{{ route('admin.care.index') }}" class="text-xs text-brand-primary hover:underline">&larr; Back to Knock for Help</a>
        </div>
    </div>
</x-admin.layout>
