@php
    $isNew = !$category->exists;
    $action = $isNew ? route('admin.blog.categories.store') : route('admin.blog.categories.update', $category);
@endphp
<x-admin.layout title="{{ $isNew ? 'New category' : 'Edit category' }}">
    <form method="POST" action="{{ $action }}" class="space-y-6 max-w-2xl">
        @csrf
        @unless($isNew) @method('PUT') @endunless

        <div>
            <div class="text-xs text-ink-muted mb-1">
                <a href="{{ route('admin.blog.categories.index') }}" class="hover:underline">Categories</a>
                <span class="mx-1">/</span>
                <span>{{ $isNew ? 'New' : $category->name }}</span>
            </div>
            <h1 class="text-2xl font-serif">{{ $isNew ? 'New category' : 'Edit category' }}</h1>
        </div>

        <div class="card p-5 space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1.5">Name</label>
                <input type="text" name="name" class="input" value="{{ old('name', $category->name) }}" required>
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Slug</label>
                <input type="text" name="slug" class="input font-mono text-sm" value="{{ old('slug', $category->slug) }}" placeholder="auto-generated from name if empty">
                @error('slug')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.blog.categories.index') }}" class="btn-ghost text-sm">Cancel</a>
            <button type="submit" class="btn-primary text-sm">{{ $isNew ? 'Create category' : 'Save changes' }}</button>
        </div>
    </form>
</x-admin.layout>
