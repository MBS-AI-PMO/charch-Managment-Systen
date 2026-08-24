@php
    $isNew = !$slide->exists;
    $action = $isNew ? route('admin.hero-slides.store') : route('admin.hero-slides.update', $slide);
@endphp
<x-admin.layout title="{{ $isNew ? 'New hero slide' : 'Edit hero slide' }}">
    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @unless($isNew) @method('PUT') @endunless

        <div class="flex items-end justify-between flex-wrap gap-3">
            <div>
                <div class="text-xs text-ink-muted mb-1">
                    <a href="{{ route('admin.hero-slides.index') }}" class="hover:underline">Hero slides</a>
                    <span class="mx-1">/</span>
                    <span>{{ $isNew ? 'New' : $slide->heading }}</span>
                </div>
                <h1 class="text-2xl font-serif">{{ $isNew ? 'New hero slide' : 'Edit hero slide' }}</h1>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.hero-slides.index') }}" class="btn-ghost text-sm">Cancel</a>
                <button type="submit" name="is_active" value="0" class="btn-ghost text-sm">Save Hidden</button>
                <button type="submit" name="is_active" value="1" class="btn-primary text-sm">Save &amp; Activate</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-5">
                <div class="card p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Heading</label>
                        <input type="text" name="heading" class="input text-lg" value="{{ old('heading', $slide->heading) }}" required>
                        @error('heading')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Eyebrow</label>
                        <input type="text" name="eyebrow" class="input" value="{{ old('eyebrow', $slide->eyebrow) }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Subheading</label>
                        <textarea name="sub" rows="3" class="input">{{ old('sub', $slide->sub) }}</textarea>
                    </div>
                </div>

                <div class="card p-5 space-y-4">
                    <h2 class="text-base font-serif">Buttons</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Primary button label</label>
                            <input type="text" name="primary_cta_label" class="input" value="{{ old('primary_cta_label', $slide->primary_cta_label) }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Primary button URL</label>
                            <input type="text" name="primary_cta_url" class="input font-mono text-sm" value="{{ old('primary_cta_url', $slide->primary_cta_url) }}" placeholder="/contact">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Secondary button label</label>
                            <input type="text" name="secondary_cta_label" class="input" value="{{ old('secondary_cta_label', $slide->secondary_cta_label) }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Secondary button URL</label>
                            <input type="text" name="secondary_cta_url" class="input font-mono text-sm" value="{{ old('secondary_cta_url', $slide->secondary_cta_url) }}" placeholder="/about">
                        </div>
                    </div>
                </div>
            </div>

            <aside class="space-y-5">
                <div class="card p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Position</label>
                        <input type="number" name="position" min="0" class="input" value="{{ old('position', $slide->position ?? 0) }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Background image</label>
                        <img id="hero_slide_preview"
                             src="{{ $slide->image_path ? asset('storage/'.$slide->image_path) : '' }}"
                             alt=""
                             class="w-full aspect-video rounded-lg object-cover border border-[rgb(var(--border))] mb-3 {{ $slide->image_path ? '' : 'hidden' }}">
                        <input type="hidden" id="hero_slide_path" name="image_path" value="{{ old('image_path', $slide->image_path) }}">
                        <div class="flex gap-2 items-start">
                            <input type="file" name="image_file" accept="image/*" class="text-xs flex-1">
                            <button type="button"
                                    onclick="openMediaPicker((url) => { const img=document.getElementById('hero_slide_preview'); img.src=url; img.classList.remove('hidden'); document.getElementById('hero_slide_path').value=url.replace(/^.*\/storage\//,''); })"
                                    class="btn-ghost text-xs whitespace-nowrap">Library</button>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </form>
</x-admin.layout>
