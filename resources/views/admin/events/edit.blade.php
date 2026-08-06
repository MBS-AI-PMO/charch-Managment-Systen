@php
    $isNew = !$event->exists;
    $action = $isNew ? route('admin.events.store') : route('admin.events.update', $event);
@endphp
<x-admin.layout title="{{ $isNew ? 'New event' : 'Edit event' }}">
    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @unless($isNew) @method('PUT') @endunless

        <div class="flex items-end justify-between flex-wrap gap-3">
            <div>
                <div class="text-xs text-ink-muted mb-1">
                    <a href="{{ route('admin.events.index') }}" class="hover:underline">Events</a>
                    <span class="mx-1">/</span>
                    <span>{{ $isNew ? 'New' : $event->title }}</span>
                </div>
                <h1 class="text-2xl font-serif">{{ $isNew ? 'New event' : 'Edit event' }}</h1>
                @unless($isNew)
                    <a href="{{ route('admin.events.attendance', $event) }}" class="text-sm text-brand-primary mt-1 inline-block">Manage attendance →</a>
                @endunless
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.events.index') }}" class="btn-ghost text-sm">Cancel</a>
                <button type="submit" name="is_published" value="0" class="btn-ghost text-sm">Save Draft</button>
                <button type="submit" name="is_published" value="1" class="btn-primary text-sm">Save &amp; Publish</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-5">
                <div class="card p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Title</label>
                        <input type="text" name="title" class="input text-lg" value="{{ old('title', $event->title) }}" required>
                        @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Slug</label>
                        <input type="text" name="slug" class="input font-mono text-sm" value="{{ old('slug', $event->slug) }}" placeholder="auto-generated">
                        @error('slug')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Starts at</label>
                            <input type="datetime-local" name="starts_at" class="input" value="{{ old('starts_at', $event->starts_at?->format('Y-m-d\TH:i')) }}" required>
                            @error('starts_at')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Ends at</label>
                            <input type="datetime-local" name="ends_at" class="input" value="{{ old('ends_at', $event->ends_at?->format('Y-m-d\TH:i')) }}">
                            @error('ends_at')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Location</label>
                        <input type="text" name="location" class="input" value="{{ old('location', $event->location) }}" placeholder="e.g. Main Sanctuary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Registration URL</label>
                        <input type="url" name="registration_url" class="input" value="{{ old('registration_url', $event->registration_url) }}" placeholder="https://…">
                        @error('registration_url')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="card p-5 space-y-3">
                    <h2 class="text-base font-serif">Description</h2>
                    <textarea name="description" rows="10" class="input ck-editor font-mono text-sm">{{ old('description', $event->description) }}</textarea>
                </div>
            </div>

            <aside class="space-y-5">
                <div class="card p-5 space-y-3">
                    <h3 class="text-sm font-medium">Visibility</h3>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $event->is_featured) ? 'checked' : '' }} class="rounded border-[rgb(var(--border))] text-brand-primary focus:ring-brand-primary">
                        <span>Featured on home</span>
                    </label>
                </div>
                <div class="card p-5">
                    <h3 class="text-sm font-medium mb-3">Cover image</h3>
                    <img id="event_cover_preview"
                         src="{{ $event->cover_image_path ? asset('storage/'.$event->cover_image_path) : '' }}"
                         alt=""
                         class="w-full aspect-video rounded-lg object-cover border border-[rgb(var(--border))] mb-3 {{ $event->cover_image_path ? '' : 'hidden' }}">
                    @unless($event->cover_image_path)
                        <div id="event_cover_placeholder" class="w-full aspect-video rounded-lg bg-gradient-to-br from-brand-primary/20 to-brand-secondary/30 border border-[rgb(var(--border))] mb-3"></div>
                    @endunless
                    <input type="hidden" id="event_cover_path" name="cover_image_path" value="{{ old('cover_image_path', $event->cover_image_path) }}">
                    <div class="flex gap-2 items-start">
                        <input type="file" name="cover_image_file" accept="image/*" class="text-xs flex-1">
                        <button type="button"
                                onclick="openMediaPicker((url, alt) => { const img=document.getElementById('event_cover_preview'); img.src=url; img.classList.remove('hidden'); const ph=document.getElementById('event_cover_placeholder'); if(ph) ph.classList.add('hidden'); document.getElementById('event_cover_path').value=url.replace(/^.*\/storage\//,''); })"
                                class="btn-ghost text-xs whitespace-nowrap">Library</button>
                    </div>
                </div>
            </aside>
        </div>
    </form>
</x-admin.layout>
