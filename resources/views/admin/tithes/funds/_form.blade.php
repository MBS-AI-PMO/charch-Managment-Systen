@php $editing = isset($fund); @endphp

<form method="POST" action="{{ $editing ? route('admin.tithes.funds.update', $fund) : route('admin.tithes.funds.store') }}" class="card p-6 max-w-xl flex flex-col gap-4">
    @csrf
    @if($editing) @method('PUT') @endif

    <label class="flex flex-col gap-1">
        <span class="text-sm font-medium">Name</span>
        <input type="text" name="name" value="{{ $fund->name ?? old('name') }}" class="border rounded p-2" required>
    </label>

    <label class="flex flex-col gap-1">
        <span class="text-sm font-medium">Description</span>
        <textarea name="description" rows="3" class="border rounded p-2">{{ $fund->description ?? '' }}</textarea>
    </label>

    <div class="grid sm:grid-cols-2 gap-4">
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" @checked($editing ? $fund->is_active : true)>
            Active
        </label>
        <label class="flex flex-col gap-1">
            <span class="text-sm font-medium">Sort order</span>
            <input type="number" name="sort_order" value="{{ $fund->sort_order ?? 0 }}" class="border rounded p-2">
        </label>
    </div>

    <div class="flex gap-2 justify-end">
        <a href="{{ route('admin.tithes.funds.index') }}" class="text-sm">Cancel</a>
        <button class="btn-primary text-sm">{{ $editing ? 'Save' : 'Create' }}</button>
    </div>
</form>
