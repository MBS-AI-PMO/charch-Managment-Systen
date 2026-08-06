<x-admin.layout title="Funds">
    <div class="flex items-center justify-between mb-6">
        <h1 class="font-serif text-2xl">Tithe funds</h1>
        <a href="{{ route('admin.tithes.funds.create') }}" class="btn-primary text-sm">+ New fund</a>
    </div>

    <div class="card p-0 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-ink-muted border-b border-[rgb(var(--border))]">
                <tr>
                    <th class="py-2 px-3">Name</th>
                    <th class="py-2 px-3">Slug</th>
                    <th class="py-2 px-3">Status</th>
                    <th class="py-2 px-3">Sort</th>
                    <th class="py-2 px-3"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($funds as $f)
                    <tr class="border-b border-[rgb(var(--border))]">
                        <td class="py-2 px-3">{{ $f->name }}</td>
                        <td class="py-2 px-3 font-mono text-xs">{{ $f->slug }}</td>
                        <td class="py-2 px-3">{{ $f->is_active ? 'Active' : 'Inactive' }}</td>
                        <td class="py-2 px-3">{{ $f->sort_order }}</td>
                        <td class="py-2 px-3 text-right">
                            <a href="{{ route('admin.tithes.funds.edit', $f) }}" class="text-brand-primary text-xs">edit</a>
                            <form method="POST" action="{{ route('admin.tithes.funds.destroy', $f) }}" class="inline" onsubmit="return confirm('Delete or deactivate fund?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 text-xs ml-2">delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin.layout>
