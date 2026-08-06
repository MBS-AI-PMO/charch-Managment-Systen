<x-admin.layout title="New menu">
    <form method="POST" action="{{ route('admin.menus.store') }}" class="space-y-6 max-w-2xl">
        @csrf

        <div>
            <div class="text-xs text-ink-muted mb-1">
                <a href="{{ route('admin.menus.index') }}" class="hover:underline">Menus</a>
                <span class="mx-1">/</span>
                <span>New</span>
            </div>
            <h1 class="text-2xl font-serif">New menu</h1>
        </div>

        <div class="card p-5 space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1.5">Name</label>
                <input type="text" name="name" class="input" value="{{ old('name', $menu->name) }}" placeholder="e.g. Main navigation" required>
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Slug</label>
                <input type="text" name="slug" class="input font-mono text-sm" value="{{ old('slug', $menu->slug) }}" placeholder="e.g. main, footer" required>
                @error('slug')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                <p class="text-xs text-ink-muted mt-1">Used in templates to render this menu.</p>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.menus.index') }}" class="btn-ghost text-sm">Cancel</a>
            <button type="submit" class="btn-primary text-sm">Create menu</button>
        </div>
    </form>
</x-admin.layout>
