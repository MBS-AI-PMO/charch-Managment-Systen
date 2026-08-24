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
@endphp
<x-admin.layout title="Knock for Help">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-serif">Knock for Help</h1>
            <p class="text-sm text-ink-muted mt-1">Pastoral-care alerts submitted by members.</p>
        </div>
    </div>

    <div class="card overflow-hidden">
        <form method="GET" class="px-5 py-3 border-b border-[rgb(var(--border))] flex items-center gap-2 flex-wrap">
            <select name="status" class="input w-40" onchange="this.form.submit()">
                <option value="">All statuses</option>
                <option value="open"       @selected(request('status') === 'open')>Open</option>
                <option value="responding" @selected(request('status') === 'responding')>Responding</option>
                <option value="closed"     @selected(request('status') === 'closed')>Closed</option>
            </select>
            <select name="category" class="input w-40" onchange="this.form.submit()">
                <option value="">All categories</option>
                <option value="illness"   @selected(request('category') === 'illness')>Illness</option>
                <option value="grief"     @selected(request('category') === 'grief')>Grief</option>
                <option value="financial" @selected(request('category') === 'financial')>Financial</option>
                <option value="food"      @selected(request('category') === 'food')>Food</option>
                <option value="other"     @selected(request('category') === 'other')>Other</option>
            </select>
            <button type="submit" class="btn-ghost text-sm">Filter</button>
            <span class="ml-auto text-xs text-ink-muted">{{ $items->total() }} requests</span>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface text-ink-muted text-xs uppercase tracking-wider">
                    <tr>
                        <th class="text-left font-medium px-5 py-3">Member</th>
                        <th class="text-left font-medium px-5 py-3">Category</th>
                        <th class="text-left font-medium px-5 py-3">Status</th>
                        <th class="text-left font-medium px-5 py-3">Message</th>
                        <th class="text-left font-medium px-5 py-3">Submitted</th>
                        <th class="text-right font-medium px-5 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[rgb(var(--border))]">
                    @forelse($items as $r)
                        @php
                            $cb = $catBadge[$r->category] ?? $catBadge['other'];
                            $sb = $statusBadge[$r->status] ?? $statusBadge['open'];
                        @endphp
                        <tr class="hover:bg-surface/60">
                            <td class="px-5 py-3 font-medium">
                                <a href="{{ route('admin.care.show', $r) }}" class="hover:text-brand-primary">{{ $r->user?->name ?? 'Unknown' }}</a>
                                @if($r->user?->email)
                                    <div class="text-xs text-ink-muted">{{ $r->user->email }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs {{ $cb }} capitalize">{{ $r->category }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs {{ $sb }} capitalize">{{ $r->status }}</span>
                            </td>
                            <td class="px-5 py-3 text-ink-muted max-w-md truncate">{{ \Illuminate\Support\Str::limit($r->message, 80) }}</td>
                            <td class="px-5 py-3 text-ink-muted whitespace-nowrap">{{ $r->created_at?->diffForHumans() }}</td>
                            <td class="px-5 py-3 text-right">
                                <div class="row-actions">
                                    <x-row-action type="view" href="{{ route('admin.care.show', $r) }}" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-ink-muted italic">No knock-for-help requests yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="px-5 py-3 border-t border-[rgb(var(--border))]">{{ $items->links() }}</div>
        @endif
    </div>
</x-admin.layout>
