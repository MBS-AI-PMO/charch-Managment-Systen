<x-admin.layout title="Tithes">
    <div class="flex items-center justify-between mb-6">
        <h1 class="font-serif text-2xl">Tithes</h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.tithes.export', request()->only(['fund'])) }}" class="btn-ghost text-sm">Export CSV</a>
            <a href="{{ route('admin.tithes.create') }}" class="btn-primary text-sm">+ Record gift</a>
        </div>
    </div>

    <form method="GET" class="card p-4 mb-4 flex flex-wrap items-center gap-3">
        <select name="fund" class="input w-44 text-sm">
            <option value="">All funds</option>
            @foreach($funds as $f)
                <option value="{{ $f->id }}" @selected(($filters['fund'] ?? null) == $f->id)>{{ $f->name }}</option>
            @endforeach
        </select>
        <select name="method" class="input w-40 text-sm">
            <option value="">All methods</option>
            @foreach(['cash', 'bank_transfer', 'cheque', 'other'] as $m)
                <option value="{{ $m }}" @selected(($filters['method'] ?? null) === $m)>{{ ucfirst(str_replace('_', ' ', $m)) }}</option>
            @endforeach
        </select>
        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search giver or reference" class="input w-56 text-sm">
        <button type="submit" class="btn-ghost text-sm">Filter</button>
    </form>

    <div class="card p-0 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-ink-muted border-b border-[rgb(var(--border))]">
                <tr>
                    <th class="py-2 px-3">Date</th>
                    <th class="py-2 px-3">Giver</th>
                    <th class="py-2 px-3">Fund</th>
                    <th class="py-2 px-3">Method</th>
                    <th class="py-2 px-3 text-right">Amount</th>
                    <th class="py-2 px-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($tithes as $t)
                    <tr class="border-b border-[rgb(var(--border))]">
                        <td class="py-2 px-3">{{ $t->received_at->format('M j, Y') }}</td>
                        <td class="py-2 px-3">
                            {{ $t->giverDisplayName() }}
                            @if($t->user_id)<span class="text-xs text-brand-secondary ml-1">(member)</span>@endif
                        </td>
                        <td class="py-2 px-3">{{ $t->fund->name }}</td>
                        <td class="py-2 px-3">{{ ucfirst(str_replace('_', ' ', $t->method)) }}</td>
                        <td class="py-2 px-3 text-right font-mono">{{ formatMoney($t->amount_cents) }}</td>
                        <td class="py-2 px-3 text-right">
                            <div class="row-actions">
                                <x-row-action type="edit" href="{{ route('admin.tithes.edit', $t) }}" />
                                <form method="POST" action="{{ route('admin.tithes.destroy', $t) }}" onsubmit="return confirm('Delete this gift record?')">
                                    @csrf @method('DELETE')
                                    <x-row-action type="delete" />
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-4 px-3 text-ink-muted">No records.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $tithes->links() }}</div>
</x-admin.layout>
