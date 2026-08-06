@php $editing = isset($tithe); @endphp

<form method="POST" action="{{ $editing ? route('admin.tithes.update', $tithe) : route('admin.tithes.store') }}" class="card p-6 flex flex-col gap-4 max-w-xl">
    @csrf
    @if($editing) @method('PUT') @endif

    <div x-data="giverPicker(@js($editing ? ['id' => $tithe->user_id, 'name' => $tithe->giverDisplayName()] : null))" class="flex flex-col gap-1">
        <label class="text-sm font-medium">Giver</label>
        <input type="hidden" name="user_id" :value="picked.id || ''">
        <input type="hidden" name="giver_name" :value="picked.id ? '' : query">
        <input type="text" x-model="query" @input.debounce.300="search" placeholder="Search member or type a name" class="border rounded p-2">
        <ul x-show="results.length" class="border rounded mt-1 bg-white shadow text-sm">
            <template x-for="r in results" :key="r.id">
                <li @click="pick(r)" class="px-3 py-1 cursor-pointer hover:bg-surface" x-text="r.name"></li>
            </template>
        </ul>
        <p class="text-xs text-ink-muted">Members get linked automatically; free-text names are saved as-is.</p>
    </div>

    <label class="flex flex-col gap-1">
        <span class="text-sm font-medium">Fund</span>
        <select name="fund_id" class="border rounded p-2" required>
            @foreach($funds as $f)
                <option value="{{ $f->id }}" @selected(($tithe->fund_id ?? '') === $f->id)>{{ $f->name }}</option>
            @endforeach
        </select>
    </label>

    <div class="grid sm:grid-cols-2 gap-4">
        <label class="flex flex-col gap-1">
            <span class="text-sm font-medium">Amount ({{ settings('finance.currency_symbol', '$') }})</span>
            <input type="number" name="amount" step="0.01" min="0.01" value="{{ $editing ? number_format($tithe->amount_cents / 100, 2, '.', '') : old('amount') }}" class="border rounded p-2" required>
        </label>

        <label class="flex flex-col gap-1">
            <span class="text-sm font-medium">Method</span>
            <select name="method" class="border rounded p-2" required>
                @foreach(['cash' => 'Cash', 'bank_transfer' => 'Bank transfer', 'cheque' => 'Cheque', 'other' => 'Other'] as $k => $v)
                    <option value="{{ $k }}" @selected(($tithe->method ?? '') === $k)>{{ $v }}</option>
                @endforeach
            </select>
        </label>

        <label class="flex flex-col gap-1">
            <span class="text-sm font-medium">Received at</span>
            <input type="date" name="received_at" value="{{ $editing ? $tithe->received_at->toDateString() : now()->toDateString() }}" class="border rounded p-2" required>
        </label>

        <label class="flex flex-col gap-1">
            <span class="text-sm font-medium">Reference (cheque #, bank ref)</span>
            <input type="text" name="reference" value="{{ $tithe->reference ?? '' }}" class="border rounded p-2">
        </label>
    </div>

    <label class="flex flex-col gap-1">
        <span class="text-sm font-medium">Note</span>
        <textarea name="note" rows="3" class="border rounded p-2">{{ $tithe->note ?? '' }}</textarea>
    </label>

    <div class="flex gap-2 justify-end">
        <a href="{{ route('admin.tithes.index') }}" class="text-sm">Cancel</a>
        <button class="btn-primary text-sm">{{ $editing ? 'Save changes' : 'Record gift' }}</button>
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
