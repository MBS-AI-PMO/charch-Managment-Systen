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
@endphp
<x-admin.layout title="Prayer: {{ $p->title }}">
    <div class="space-y-6 max-w-4xl">
        <div class="flex items-end justify-between flex-wrap gap-3">
            <div>
                <div class="text-xs text-ink-muted mb-1">
                    <a href="{{ route('admin.prayer-requests.index') }}" class="hover:underline">Prayer requests</a>
                    <span class="mx-1">/</span>
                    <span>#{{ $p->id }}</span>
                </div>
                <h1 class="text-2xl font-serif">{{ $p->title }}</h1>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex px-3 py-1 rounded-full text-xs {{ $bg }} {{ $tx }} capitalize">{{ $p->status ?? 'pending' }}</span>
                <form method="POST" action="{{ route('admin.prayer-requests.destroy', $p) }}" onsubmit="return confirm('Delete this prayer request?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-ghost text-sm text-red-600">Delete</button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-5">
                <div class="card p-5 space-y-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <div class="text-xs text-ink-muted uppercase tracking-wider">Requester</div>
                            <div class="font-medium mt-1">{{ $requester }}</div>
                            @if($isSiteAdmin && $p->user)
                                <div class="text-ink-muted text-xs">{{ $p->user->email }}</div>
                                @if($p->user->phone)
                                    <div class="text-ink-muted text-xs">{{ $p->user->phone }}</div>
                                @endif
                                @if($p->is_anonymous)
                                    <div class="text-xs text-amber-700 italic mt-1">posted anonymously on community board</div>
                                @endif
                            @endif
                        </div>
                        <div>
                            <div class="text-xs text-ink-muted uppercase tracking-wider">Submitted</div>
                            <div class="font-medium mt-1">{{ $p->created_at?->format('M j, Y g:i A') }}</div>
                            <div class="text-ink-muted text-xs">{{ $p->created_at?->diffForHumans() }}</div>
                            <div class="text-ink-muted text-xs mt-1">{{ $p->is_public ? 'Public on board' : 'Private' }} &middot; {{ $p->pray_count }} {{ \Illuminate\Support\Str::plural('pray', $p->pray_count) }}</div>
                        </div>
                    </div>
                    <div class="border-t border-[rgb(var(--border))] pt-4">
                        <div class="text-xs text-ink-muted uppercase tracking-wider mb-2">Request</div>
                        <div class="whitespace-pre-wrap text-sm leading-relaxed">{{ $p->body }}</div>
                    </div>
                </div>
            </div>

            <aside class="space-y-5">
                <form method="POST" action="{{ route('admin.prayer-requests.update', $p) }}" class="card p-5 space-y-4">
                    @csrf @method('PUT')
                    <h3 class="text-sm font-medium">Manage</h3>

                    <div>
                        <label class="block text-xs text-ink-muted uppercase tracking-wider mb-1.5">Status</label>
                        <select name="status" class="input w-full">
                            @foreach(['pending','praying','answered','closed'] as $s)
                                <option value="{{ $s }}" @selected(old('status', $p->status) === $s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                        @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs text-ink-muted uppercase tracking-wider mb-1.5">Assigned to</label>
                        <select name="assigned_to" class="input w-full">
                            <option value="">— Unassigned —</option>
                            @foreach($assignees as $a)
                                <option value="{{ $a->id }}" @selected(old('assigned_to', $p->assigned_to) == $a->id)>{{ $a->name }}</option>
                            @endforeach
                        </select>
                        @error('assigned_to')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs text-ink-muted uppercase tracking-wider mb-1.5">Admin notes</label>
                        <textarea name="admin_notes" rows="6" class="ck-editor input">{{ old('admin_notes', $p->admin_notes) }}</textarea>
                        @error('admin_notes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="btn-primary text-sm w-full">Save changes</button>
                </form>
            </aside>
        </div>

        <div>
            <a href="{{ route('admin.prayer-requests.index') }}" class="text-xs text-brand-primary hover:underline">&larr; Back to prayer requests</a>
        </div>
    </div>
</x-admin.layout>
