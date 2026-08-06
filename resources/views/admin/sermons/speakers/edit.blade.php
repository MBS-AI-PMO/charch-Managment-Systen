@php
    $isNew = !$speaker->exists;
    $action = $isNew ? route('admin.sermons.speakers.store') : route('admin.sermons.speakers.update', $speaker);
@endphp
<x-admin.layout title="{{ $isNew ? 'New speaker' : 'Edit speaker' }}">
    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6 max-w-3xl">
        @csrf
        @unless($isNew) @method('PUT') @endunless

        <div>
            <div class="text-xs text-ink-muted mb-1">
                <a href="{{ route('admin.sermons.speakers.index') }}" class="hover:underline">Speakers</a>
                <span class="mx-1">/</span>
                <span>{{ $isNew ? 'New' : $speaker->name }}</span>
            </div>
            <h1 class="text-2xl font-serif">{{ $isNew ? 'New speaker' : 'Edit speaker' }}</h1>
        </div>

        <div class="card p-5 space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1.5">Name</label>
                <input type="text" name="name" class="input" value="{{ old('name', $speaker->name) }}" required>
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Slug</label>
                <input type="text" name="slug" class="input font-mono text-sm" value="{{ old('slug', $speaker->slug) }}" placeholder="auto-generated">
                @error('slug')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Role / title</label>
                <input type="text" name="role" class="input" value="{{ old('role', $speaker->role) }}" placeholder="e.g. Senior pastor">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Bio</label>
                <textarea name="bio" rows="5" class="input ck-editor">{{ old('bio', $speaker->bio) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Photo</label>
                <img id="speaker_photo_preview"
                     src="{{ $speaker->photo_path ? asset('storage/'.$speaker->photo_path) : '' }}"
                     alt=""
                     class="w-24 h-24 rounded-full object-cover border border-[rgb(var(--border))] mb-3 {{ $speaker->photo_path ? '' : 'hidden' }}">
                <input type="hidden" id="speaker_photo_path" name="photo_path" value="{{ old('photo_path', $speaker->photo_path) }}">
                <div class="flex gap-2 items-start">
                    <input type="file" name="photo_file" accept="image/*" class="text-xs flex-1">
                    <button type="button"
                            onclick="openMediaPicker((url, alt) => { const img=document.getElementById('speaker_photo_preview'); img.src=url; img.classList.remove('hidden'); document.getElementById('speaker_photo_path').value=url.replace(/^.*\/storage\//,''); })"
                            class="btn-ghost text-xs whitespace-nowrap">Library</button>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.sermons.speakers.index') }}" class="btn-ghost text-sm">Cancel</a>
            <button type="submit" class="btn-primary text-sm">{{ $isNew ? 'Create speaker' : 'Save changes' }}</button>
        </div>
    </form>
</x-admin.layout>
