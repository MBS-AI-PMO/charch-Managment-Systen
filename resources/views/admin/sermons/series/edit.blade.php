@php
    $isNew = !$series->exists;
    $action = $isNew ? route('admin.sermons.series.store') : route('admin.sermons.series.update', $series);
@endphp
<x-admin.layout title="{{ $isNew ? 'New series' : 'Edit series' }}">
    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6 max-w-3xl">
        @csrf
        @unless($isNew) @method('PUT') @endunless

        <div>
            <div class="text-xs text-ink-muted mb-1">
                <a href="{{ route('admin.sermons.series.index') }}" class="hover:underline">Series</a>
                <span class="mx-1">/</span>
                <span>{{ $isNew ? 'New' : $series->name }}</span>
            </div>
            <h1 class="text-2xl font-serif">{{ $isNew ? 'New series' : 'Edit series' }}</h1>
        </div>

        <div class="card p-5 space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1.5">Name</label>
                <input type="text" name="name" class="input" value="{{ old('name', $series->name) }}" required>
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Slug</label>
                <input type="text" name="slug" class="input font-mono text-sm" value="{{ old('slug', $series->slug) }}" placeholder="auto-generated">
                @error('slug')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Description</label>
                <textarea name="description" rows="4" class="input ck-editor">{{ old('description', $series->description) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Cover image</label>
                <img id="series_cover_preview"
                     src="{{ $series->cover_image_path ? asset('storage/'.$series->cover_image_path) : '' }}"
                     alt=""
                     class="w-48 aspect-video rounded-lg object-cover border border-[rgb(var(--border))] mb-3 {{ $series->cover_image_path ? '' : 'hidden' }}">
                <input type="hidden" id="series_cover_path" name="cover_image_path" value="{{ old('cover_image_path', $series->cover_image_path) }}">
                <div class="flex gap-2 items-start">
                    <input type="file" name="cover_image_file" accept="image/*" class="text-xs flex-1">
                    <button type="button"
                            onclick="openMediaPicker((url, alt) => { const img=document.getElementById('series_cover_preview'); img.src=url; img.classList.remove('hidden'); document.getElementById('series_cover_path').value=url.replace(/^.*\/storage\//,''); })"
                            class="btn-ghost text-xs whitespace-nowrap">Library</button>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.sermons.series.index') }}" class="btn-ghost text-sm">Cancel</a>
            <button type="submit" class="btn-primary text-sm">{{ $isNew ? 'Create series' : 'Save changes' }}</button>
        </div>
    </form>
</x-admin.layout>
