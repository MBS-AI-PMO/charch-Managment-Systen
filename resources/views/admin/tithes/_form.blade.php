@php
    $editing = isset($tithe);
    $currency = settings('finance.currency_symbol', '$');
    $giverInitial = $editing
        ? ['id' => $tithe->user_id, 'name' => $tithe->giverDisplayName()]
        : null;
@endphp

<form
    method="POST"
    action="{{ $editing ? route('admin.tithes.update', $tithe) : route('admin.tithes.store') }}"
    class="w-full space-y-6"
>
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="card p-5 md:p-6 xl:p-8 space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 xl:gap-6">
            {{-- Giver --}}
            <div
                x-data="giverPicker(@js($giverInitial))"
                class="relative flex flex-col gap-1.5 lg:col-span-2 xl:col-span-1"
            >
                <label class="text-[11px] uppercase tracking-wider text-ink-muted font-semibold">Giver</label>
                <input type="hidden" name="user_id" :value="picked.id || ''">
                <input type="hidden" name="giver_name" :value="picked.id ? '' : query">
                <input
                    type="text"
                    x-model="query"
                    @input.debounce.300="search"
                    @keydown.escape="results = []"
                    placeholder="Search member or type a name"
                    class="input w-full"
                    autocomplete="off"
                >
                <ul
                    x-show="results.length"
                    x-cloak
                    @click.away="results = []"
                    class="absolute left-0 right-0 top-full z-20 mt-1 max-h-56 overflow-y-auto rounded-lg border border-[rgb(var(--border))] bg-white shadow-lg text-sm"
                >
                    <template x-for="r in results" :key="r.id">
                        <li
                            @click="pick(r)"
                            class="px-3 py-2.5 cursor-pointer hover:bg-brand-primary/5 hover:text-brand-primary border-b border-[rgb(var(--border))] last:border-0"
                            x-text="r.name"
                        ></li>
                    </template>
                </ul>
                <p class="text-xs text-ink-muted">Members get linked automatically; free-text names are saved as-is.</p>
                @error('user_id')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                @error('giver_name')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Fund --}}
            <div class="flex flex-col gap-1.5">
                <label for="fund_id" class="text-[11px] uppercase tracking-wider text-ink-muted font-semibold">Fund</label>
                <select id="fund_id" name="fund_id" class="input w-full" required>
                    @foreach($funds as $f)
                        <option value="{{ $f->id }}" @selected((string) old('fund_id', $tithe->fund_id ?? '') === (string) $f->id)>{{ $f->name }}</option>
                    @endforeach
                </select>
                @error('fund_id')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 xl:gap-6">
            <div class="flex flex-col gap-1.5">
                <label for="amount" class="text-[11px] uppercase tracking-wider text-ink-muted font-semibold">Amount ({{ $currency }})</label>
                <input
                    id="amount"
                    type="number"
                    name="amount"
                    step="0.01"
                    min="0.01"
                    value="{{ old('amount', $editing ? number_format($tithe->amount_cents / 100, 2, '.', '') : '') }}"
                    class="input w-full"
                    required
                >
                @error('amount')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="method" class="text-[11px] uppercase tracking-wider text-ink-muted font-semibold">Method</label>
                <select id="method" name="method" class="input w-full" required>
                    @foreach(['cash' => 'Cash', 'bank_transfer' => 'Bank transfer', 'cheque' => 'Cheque', 'other' => 'Other'] as $k => $v)
                        <option value="{{ $k }}" @selected(old('method', $tithe->method ?? '') === $k)>{{ $v }}</option>
                    @endforeach
                </select>
                @error('method')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="received_at" class="text-[11px] uppercase tracking-wider text-ink-muted font-semibold">Received at</label>
                <input
                    id="received_at"
                    type="date"
                    name="received_at"
                    value="{{ old('received_at', $editing ? $tithe->received_at->toDateString() : now()->toDateString()) }}"
                    class="input w-full"
                    required
                >
                @error('received_at')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="reference" class="text-[11px] uppercase tracking-wider text-ink-muted font-semibold">Reference</label>
                <input
                    id="reference"
                    type="text"
                    name="reference"
                    value="{{ old('reference', $tithe->reference ?? '') }}"
                    placeholder="Cheque #, bank ref"
                    class="input w-full"
                >
                @error('reference')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="note" class="text-[11px] uppercase tracking-wider text-ink-muted font-semibold">Note</label>
            <textarea id="note" name="note" rows="4" class="input w-full" placeholder="Optional note about this gift…">{{ old('note', $tithe->note ?? '') }}</textarea>
            @error('note')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex flex-wrap items-center justify-end gap-3 pt-2 border-t border-[rgb(var(--border))]">
            <a href="{{ route('admin.tithes.index') }}" class="btn-ghost text-sm">Cancel</a>
            <button type="submit" class="btn-primary shine-btn glow-primary text-sm">
                <span>{{ $editing ? 'Save changes' : 'Record gift' }}</span>
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
function giverPicker(initial) {
    return {
        query: initial?.name || '',
        picked: initial || { id: null, name: '' },
        results: [],
        async search() {
            if (this.query.length < 2) { this.results = []; return; }
            const r = await fetch(`{{ route('admin.tithes.member-lookup') }}?q=${encodeURIComponent(this.query)}`, { headers: { Accept: 'application/json' } });
            this.results = await r.json();
        },
        pick(r) { this.picked = r; this.query = r.name; this.results = []; }
    };
}
</script>
@endpush
