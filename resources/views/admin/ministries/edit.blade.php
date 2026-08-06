@php
    $isNew = !$ministry->exists;
    $action = $isNew ? route('admin.ministries.store') : route('admin.ministries.update', $ministry);
@endphp
<x-admin.layout title="{{ $isNew ? 'New ministry' : 'Edit ministry' }}">
    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @unless($isNew) @method('PUT') @endunless

        <div class="flex items-end justify-between flex-wrap gap-3">
            <div>
                <div class="text-xs text-ink-muted mb-1">
                    <a href="{{ route('admin.ministries.index') }}" class="hover:underline">Ministries</a>
                    <span class="mx-1">/</span>
                    <span>{{ $isNew ? 'New' : $ministry->name }}</span>
                </div>
                <h1 class="text-2xl font-serif">{{ $isNew ? 'New ministry' : 'Edit ministry' }}</h1>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.ministries.index') }}" class="btn-ghost text-sm">Cancel</a>
                <button type="submit" name="is_published" value="0" class="btn-ghost text-sm">Save Draft</button>
                <button type="submit" name="is_published" value="1" class="btn-primary text-sm">Save &amp; Publish</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-5">
                <div class="card p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Name</label>
                        <input type="text" name="name" class="input text-lg" value="{{ old('name', $ministry->name) }}" required>
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Slug</label>
                        <input type="text" name="slug" class="input font-mono text-sm" value="{{ old('slug', $ministry->slug) }}" placeholder="auto-generated">
                        @error('slug')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Summary</label>
                        <textarea name="summary" rows="2" class="input">{{ old('summary', $ministry->summary) }}</textarea>
                    </div>
                </div>

                <div class="card p-5 space-y-3">
                    <h2 class="text-base font-serif">Body</h2>
                    <textarea name="body" rows="12" class="input ck-editor font-mono text-sm">{{ old('body', $ministry->body) }}</textarea>
                </div>
            </div>

            <aside class="space-y-5">
                <div class="card p-5 space-y-4">
                    <h3 class="text-sm font-medium">Leader</h3>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Name</label>
                        <input type="text" name="leader_name" class="input" value="{{ old('leader_name', $ministry->leader_name) }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Contact email</label>
                        <input type="email" name="contact_email" class="input" value="{{ old('contact_email', $ministry->contact_email) }}">
                        @error('contact_email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Sort order</label>
                        <input type="number" name="sort_order" min="0" class="input" value="{{ old('sort_order', $ministry->sort_order ?? 0) }}">
                    </div>
                </div>

                <div class="card p-5">
                    <h3 class="text-sm font-medium mb-3">Cover image</h3>
                    <img id="ministry_cover_preview"
                         src="{{ $ministry->cover_image_path ? asset('storage/'.$ministry->cover_image_path) : '' }}"
                         alt=""
                         class="w-full aspect-video rounded-lg object-cover border border-[rgb(var(--border))] mb-3 {{ $ministry->cover_image_path ? '' : 'hidden' }}">
                    @unless($ministry->cover_image_path)
                        <div id="ministry_cover_placeholder" class="w-full aspect-video rounded-lg bg-gradient-to-br from-brand-primary/20 to-brand-secondary/30 border border-[rgb(var(--border))] mb-3"></div>
                    @endunless
                    <input type="hidden" id="ministry_cover_path" name="cover_image_path" value="{{ old('cover_image_path', $ministry->cover_image_path) }}">
                    <div class="flex gap-2 items-start">
                        <input type="file" name="cover_image_file" accept="image/*" class="text-xs flex-1">
                        <button type="button"
                                onclick="openMediaPicker((url, alt) => { const img=document.getElementById('ministry_cover_preview'); img.src=url; img.classList.remove('hidden'); const ph=document.getElementById('ministry_cover_placeholder'); if(ph) ph.classList.add('hidden'); document.getElementById('ministry_cover_path').value=url.replace(/^.*\/storage\//,''); })"
                                class="btn-ghost text-xs whitespace-nowrap">Library</button>
                    </div>
                </div>
            </aside>
        </div>
    </form>
</x-admin.layout>
