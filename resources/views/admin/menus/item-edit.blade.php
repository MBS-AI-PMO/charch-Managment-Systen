<x-admin.layout title="Edit menu item">
    <form method="POST" action="{{ route('admin.items.update', $item) }}" class="space-y-6 max-w-2xl">
        @csrf @method('PUT')

        <div>
            <div class="text-xs text-ink-muted mb-1">
                <a href="{{ route('admin.menus.index') }}" class="hover:underline">Menus</a>
                <span class="mx-1">/</span>
                <a href="{{ route('admin.menus.edit', $menu) }}" class="hover:underline">{{ $menu->name }}</a>
                <span class="mx-1">/</span>
                <span>{{ $item->label }}</span>
            </div>
            <h1 class="text-2xl font-serif">Edit menu item</h1>
        </div>

        <div class="card p-5 space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1.5">Label</label>
                <input type="text" name="label" class="input" value="{{ old('label', $item->label) }}" required>
                @error('label')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Parent</label>
                <select name="parent_id" class="input">
                    <option value="">(top level)</option>
                    @foreach(($rootChoices ?? collect()) as $root)
                        <option value="{{ $root->id }}" @selected(old('parent_id', $item->parent_id) == $root->id)>{{ $root->label }}</option>
                    @endforeach
                </select>
                @error('parent_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Link type</label>
                <select name="link_type" class="input">
                    @foreach(['page' => 'Page', 'url' => 'Custom URL', 'route' => 'Named route'] as $val => $label)
                        <option value="{{ $val }}" @selected(old('link_type', $item->link_type) === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Link value</label>
                <input type="text" name="link_value" class="input font-mono text-sm" value="{{ old('link_value', $item->link_value) }}" required>
                @error('link_value')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Target</label>
                <select name="target" class="input">
                    <option value="_self" @selected(old('target', $item->target) === '_self')>Same tab</option>
                    <option value="_blank" @selected(old('target', $item->target) === '_blank')>New tab</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Sort order</label>
                <input type="number" name="sort_order" min="0" class="input" value="{{ old('sort_order', $item->sort_order) }}">
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.menus.edit', $menu) }}" class="btn-ghost text-sm">Cancel</a>
            <button type="submit" class="btn-primary text-sm">Save changes</button>
        </div>
    </form>
</x-admin.layout>
