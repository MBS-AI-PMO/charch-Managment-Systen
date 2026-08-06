<x-admin.layout title="Menus">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-serif">Menus</h1>
            <p class="text-sm text-ink-muted mt-1">Configure the navigation that appears in your header and footer.</p>
        </div>
        @can('manage-menus')
            <a href="{{ route('admin.menus.create') }}" class="btn-primary text-sm">+ New menu</a>
        @endcan
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($menus as $menu)
            <div class="card p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-base font-serif">{{ $menu->name }}</h2>
                        <p class="text-xs text-ink-muted mt-1 font-mono">slug: {{ $menu->slug }}</p>
                    </div>
                    <span class="text-xs text-ink-muted">{{ $menu->items_count ?? 0 }} item{{ ($menu->items_count ?? 0) === 1 ? '' : 's' }}</span>
                </div>
                <div class="mt-4 flex items-center gap-2">
                    <a href="{{ route('admin.menus.edit', $menu) }}" class="btn-primary text-sm">Edit items</a>
                    @can('manage-menus')
                        <form method="POST" action="{{ route('admin.menus.destroy', $menu) }}" onsubmit="return confirm('Delete this menu?')">
                            @csrf @method('DELETE')
                            <button class="btn-ghost text-sm text-red-600">Delete</button>
                        </form>
                    @endcan
                </div>
            </div>
        @empty
            <div class="card p-10 text-center text-sm text-ink-muted md:col-span-2">No menus yet. <a href="{{ route('admin.menus.create') }}" class="text-brand-primary hover:underline">Create one</a>.</div>
        @endforelse
    </div>
</x-admin.layout>
