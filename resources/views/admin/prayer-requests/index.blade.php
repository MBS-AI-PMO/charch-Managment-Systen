@php
    $isSiteAdmin = auth('admin')->user()?->hasRole('Site Admin') ?? false;
    $statusMap = [
        'pending'  => ['bg-slate-100','text-slate-700'],
        'praying'  => ['bg-amber-50','text-amber-700'],
        'answered' => ['bg-emerald-50','text-emerald-700'],
        'closed'   => ['bg-slate-100','text-slate-600'],
    ];
@endphp
<x-admin.layout title="Prayer Requests">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-serif">Prayer requests</h1>
            <p class="text-sm text-ink-muted mt-1">Manage and respond to prayer requests submitted by members.</p>
        </div>
    </div>

    <div class="card overflow-hidden">
        <form method="GET" class="px-5 py-3 border-b border-[rgb(var(--border))] flex items-center gap-2 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}" class="input w-64" placeholder="Search by title…">
            <select name="status" class="input w-40" onchange="this.form.submit()">
                <option value="">All statuses</option>
                <option value="pending"  @selected(request('status') === 'pending')>Pending</option>
                <option value="praying"  @selected(request('status') === 'praying')>Praying</option>
                <option value="answered" @selected(request('status') === 'answered')>Answered</option>
                <option value="closed"   @selected(request('status') === 'closed')>Closed</option>
            </select>
            <button type="submit" class="btn-ghost text-sm">Filter</button>
            <span class="ml-auto text-xs text-ink-muted">{{ $items->total() }} requests</span>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface text-ink-muted text-xs uppercase tracking-wider">
                    <tr>
                        <th class="text-left font-medium px-5 py-3">Title</th>
                        <th class="text-left font-medium px-5 py-3">Requester</th>
                        <th class="text-left font-medium px-5 py-3">Status</th>
                        <th class="text-left font-medium px-5 py-3">Prays</th>
                        <th class="text-left font-medium px-5 py-3">Created</th>
                        <th class="text-right font-medium px-5 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[rgb(var(--border))]">
                    @forelse($items as $p)
                        @php
                            [$bg,$tx] = $statusMap[$p->status ?? 'pending'] ?? $statusMap['pending'];
                            $requester = $isSiteAdmin
                                ? ($p->user?->name ?? $p->name ?? 'Unknown')
                                : $p->displayName();
                        @endphp
                        <tr class="hover:bg-surface/60">
                            <td class="px-5 py-3 font-medium">
                                <a href="{{ route('admin.prayer-requests.show', $p) }}" class="hover:text-brand-primary">{{ $p->title }}</a>
                            </td>
                            <td class="px-5 py-3">
                                <div>{{ $requester }}</div>
                                @if($isSiteAdmin && $p->is_anonymous)
                                    <div class="text-xs text-ink-muted italic">posted anonymously</div>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs {{ $bg }} {{ $tx }} capitalize">{{ $p->status ?? 'pending' }}</span>
                            </td>
                            <td class="px-5 py-3 text-ink-muted">{{ $p->pray_count }}</td>
                            <td class="px-5 py-3 text-ink-muted">{{ $p->created_at?->diffForHumans() }}</td>
                            <td class="px-5 py-3 text-right">
                                <div class="row-actions">
                                    <x-row-action type="view" href="{{ route('admin.prayer-requests.show', $p) }}" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-ink-muted">No prayer requests found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="px-5 py-3 border-t border-[rgb(var(--border))]">{{ $items->links() }}</div>
        @endif
    </div>
</x-admin.layout>
