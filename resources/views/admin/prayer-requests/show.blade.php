@php
    $isSiteAdmin = auth('admin')->user()?->hasRole('Site Admin') ?? false;
    $statusMap = [
        'pending'  => ['bg-slate-100','text-slate-700'],
        'praying'  => ['bg-amber-50','text-amber-700'],
        'answered' => ['bg-emerald-50','text-emerald-700'],
        'closed'   => ['bg-slate-100','text-slate-600'],
    ];
    [$bg,$tx] = $statusMap[$p->status ?? 'pending'] ?? $statusMap['pending'];
    $requester = $isSiteAdmin
        ? ($p->user?->name ?? $p->name ?? 'Unknown')
        : $p->displayName();
    $initials = collect(preg_split('/\s+/', trim($requester) ?: 'P'))
        ->filter()
        ->take(2)
        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
        ->implode('');
@endphp
<x-admin.layout title="Prayer: {{ $p->title }}">
    <div class="space-y-6 w-full">
        {{-- Header --}}
        <div class="flex items-end justify-between flex-wrap gap-4">
            <div class="min-w-0">
                <div class="text-xs text-ink-muted mb-1">
                    <a href="{{ route('admin.prayer-requests.index') }}" class="hover:underline">Prayer requests</a>
                    <span class="mx-1">/</span>
                    <span>#{{ $p->id }}</span>
                </div>
                <h1 class="text-2xl font-semibold tracking-tight text-ink">{{ $p->title }}</h1>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium {{ $bg }} {{ $tx }} capitalize">{{ $p->status ?? 'pending' }}</span>
                <form method="POST" action="{{ route('admin.prayer-requests.destroy', $p) }}" onsubmit="return confirm('Delete this prayer request?')">
                    @csrf @method('DELETE')
                    <x-row-action type="delete" />
                </form>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.prayer-requests.update', $p) }}" class="space-y-6">
            @csrf @method('PUT')

            <div class="grid xl:grid-cols-5 gap-6 items-start">
                {{-- Left: requester + request --}}
                <div class="xl:col-span-3 space-y-6 min-w-0">
                    <div class="card p-5 md:p-6">
                        <div class="flex items-start gap-4 flex-wrap">
                            <div class="w-12 h-12 rounded-full bg-brand-primary/10 text-brand-primary border border-brand-primary/15 flex items-center justify-center text-sm font-semibold shrink-0">
                                {{ $initials ?: 'P' }}
                            </div>
                            <div class="min-w-0 flex-1 grid sm:grid-cols-2 gap-4">
                                <div>
                                    <div class="text-[11px] uppercase tracking-wider text-ink-muted font-semibold">Requester</div>
                                    <div class="font-medium text-ink mt-1">{{ $requester }}</div>
                                    @if($isSiteAdmin && $p->user)
                                        <div class="text-ink-muted text-sm mt-0.5 break-all">{{ $p->user->email }}</div>
                                        @if($p->user->phone)
                                            <div class="text-ink-muted text-sm">{{ $p->user->phone }}</div>
                                        @endif
                                        @if($p->is_anonymous)
                                            <div class="text-xs text-amber-700 italic mt-1.5">Posted anonymously on community board</div>
                                        @endif
                                    @endif
                                </div>
                                <div>
                                    <div class="text-[11px] uppercase tracking-wider text-ink-muted font-semibold">Submitted</div>
                                    <div class="font-medium text-ink mt-1">{{ $p->created_at?->format('M j, Y g:i A') }}</div>
                                    <div class="text-ink-muted text-sm mt-0.5">{{ $p->created_at?->diffForHumans() }}</div>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-medium {{ $p->is_public ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $p->is_public ? 'Public on board' : 'Private' }}
                                        </span>
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-medium bg-brand-primary/10 text-brand-primary">
                                            {{ $p->pray_count }} {{ \Illuminate\Support\Str::plural('pray', $p->pray_count) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card overflow-hidden border-brand-primary/15">
                        <div class="px-5 md:px-6 py-3 bg-brand-primary/5 border-b border-brand-primary/10 flex items-center gap-2">
                            <span class="inline-flex w-7 h-7 rounded-lg bg-brand-primary/10 text-brand-primary items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </span>
                            <div>
                                <div class="text-[11px] uppercase tracking-wider text-brand-primary font-semibold">Request</div>
                                <div class="text-xs text-ink-muted">Prayer details</div>
                            </div>
                        </div>
                        <div class="p-5 md:p-6">
                            <p class="whitespace-pre-wrap text-base leading-relaxed text-ink">{{ $p->body }}</p>
                        </div>
                    </div>
                </div>

                {{-- Right: manage controls --}}
                <aside class="xl:col-span-2 xl:sticky xl:top-24 space-y-4">
                    <div class="card p-5 md:p-6 space-y-4 border-brand-primary/10">
                        <div>
                            <h2 class="text-base font-semibold text-ink">Manage</h2>
                            <p class="text-xs text-ink-muted mt-1 leading-relaxed">Update status and assignment for this request.</p>
                        </div>

                        <div class="grid sm:grid-cols-2 xl:grid-cols-1 gap-4">
                            <div>
                                <label class="block text-[11px] uppercase tracking-wider text-ink-muted font-semibold mb-1.5">Status</label>
                                <select name="status" class="input w-full">
                                    @foreach(['pending','praying','answered','closed'] as $s)
                                        <option value="{{ $s }}" @selected(old('status', $p->status) === $s)>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                                @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-[11px] uppercase tracking-wider text-ink-muted font-semibold mb-1.5">Assigned to</label>
                                <select name="assigned_to" class="input w-full">
                                    <option value="">— Unassigned —</option>
                                    @foreach($assignees as $a)
                                        <option value="{{ $a->id }}" @selected(old('assigned_to', $p->assigned_to) == $a->id)>{{ $a->name }}</option>
                                    @endforeach
                                </select>
                                @error('assigned_to')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <button type="submit" class="btn-primary shine-btn glow-primary text-sm w-full">
                            <span>Save changes</span>
                        </button>
                    </div>
                </aside>
            </div>

            {{-- Full-width notes — keeps columns balanced --}}
            <div class="card p-5 md:p-6 space-y-4">
                <div>
                    <label for="admin_notes" class="block text-[11px] uppercase tracking-wider text-ink-muted font-semibold">Admin notes</label>
                    <p class="text-xs text-ink-muted mt-1">Internal only — not shown to the member.</p>
                </div>
                <div class="ck-compact">
                    <textarea id="admin_notes" name="admin_notes" rows="5" class="ck-editor input">{{ old('admin_notes', $p->admin_notes) }}</textarea>
                </div>
                @error('admin_notes')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary shine-btn glow-primary text-sm">
                        <span>Save changes</span>
                    </button>
                </div>
            </div>
        </form>

        <div>
            <a href="{{ route('admin.prayer-requests.index') }}" class="text-xs text-brand-primary hover:underline">&larr; Back to prayer requests</a>
        </div>
    </div>
</x-admin.layout>
