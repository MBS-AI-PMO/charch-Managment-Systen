@php
    $isNew = !$sermon->exists;
    $action = $isNew ? route('admin.sermons.store') : route('admin.sermons.update', $sermon);
@endphp
<x-admin.layout title="{{ $isNew ? 'New sermon' : 'Edit sermon' }}">
    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @unless($isNew) @method('PUT') @endunless

        <div class="flex items-end justify-between flex-wrap gap-3">
            <div>
                <div class="text-xs text-ink-muted mb-1">
                    <a href="{{ route('admin.sermons.index') }}" class="hover:underline">Sermons</a>
                    <span class="mx-1">/</span>
                    <span>{{ $isNew ? 'New' : $sermon->title }}</span>
                </div>
                <h1 class="text-2xl font-serif">{{ $isNew ? 'Add a sermon' : 'Edit sermon' }}</h1>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.sermons.index') }}" class="btn-ghost text-sm">Cancel</a>
                <button type="submit" name="is_published" value="0" class="btn-ghost text-sm">Save Draft</button>
                <button type="submit" name="is_published" value="1" class="btn-primary text-sm">Save &amp; Publish</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-5">
                <div class="card p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Title</label>
                        <input type="text" name="title" class="input text-lg" value="{{ old('title', $sermon->title) }}" required>
                        @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Slug</label>
                        <input type="text" name="slug" class="input font-mono text-sm" value="{{ old('slug', $sermon->slug) }}" placeholder="auto-generated">
                        @error('slug')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Scripture reference</label>
                        <input type="text" name="scripture_reference" class="input" value="{{ old('scripture_reference', $sermon->scripture_reference) }}" placeholder="e.g. John 3:16-21">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Summary</label>
                        <textarea name="summary" rows="2" class="input">{{ old('summary', $sermon->summary) }}</textarea>
                    </div>
                </div>

                <div class="card p-5 space-y-3">
                    <h2 class="text-base font-serif">Notes / body</h2>
                    <textarea name="body" rows="12" class="input ck-editor font-mono text-sm">{{ old('body', $sermon->body) }}</textarea>
                </div>

                <div class="card p-5 space-y-4">
                    <h2 class="text-base font-serif">Media</h2>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Audio URL</label>
                        <input type="url" name="audio_url" class="input" value="{{ old('audio_url', $sermon->audio_url) }}" placeholder="https://…">
                        @error('audio_url')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Video URL</label>
                        <input type="url" name="video_url" class="input" value="{{ old('video_url', $sermon->video_url) }}" placeholder="https://youtube.com/…">
                        @error('video_url')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="hidden" name="downloads_enabled" value="0">
                        <input type="checkbox" name="downloads_enabled" value="1" {{ old('downloads_enabled', $sermon->downloads_enabled) ? 'checked' : '' }} class="rounded border-[rgb(var(--border))] text-brand-primary focus:ring-brand-primary">
                        <span>Allow audio/video downloads</span>
                    </label>
                </div>
            </div>

            <aside class="space-y-5">
                <div class="card p-5 space-y-4">
                    <h3 class="text-sm font-medium">Organize</h3>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Series</label>
                        <select name="series_id" class="input">
                            <option value="">— None —</option>
                            @foreach($series as $sr)
                                <option value="{{ $sr->id }}" @selected((string) old('series_id', $sermon->series_id) === (string) $sr->id)>{{ $sr->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Speaker</label>
                        <select name="speaker_id" class="input">
                            <option value="">— None —</option>
                            @foreach($speakers as $sp)
                                <option value="{{ $sp->id }}" @selected((string) old('speaker_id', $sermon->speaker_id) === (string) $sp->id)>{{ $sp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Preached on</label>
                        <input type="date" name="preached_on" class="input" value="{{ old('preached_on', $sermon->preached_on?->format('Y-m-d')) }}">
                    </div>
                </div>

                <div class="card p-5">
                    <h3 class="text-sm font-medium mb-3">Thumbnail</h3>
                    <img id="thumbnail_preview"
                         src="{{ $sermon->thumbnail_path ? asset('storage/'.$sermon->thumbnail_path) : '' }}"
                         alt=""
                         class="w-full aspect-video rounded-lg object-cover border border-[rgb(var(--border))] mb-3 {{ $sermon->thumbnail_path ? '' : 'hidden' }}">
                    @unless($sermon->thumbnail_path)
                        <div id="thumbnail_placeholder" class="w-full aspect-video rounded-lg bg-gradient-to-br from-brand-primary/20 to-brand-secondary/30 border border-[rgb(var(--border))] mb-3"></div>
                    @endunless
                    <input type="hidden" id="thumbnail_path" name="thumbnail_path" value="{{ old('thumbnail_path', $sermon->thumbnail_path) }}">
                    <div class="flex gap-2 items-start">
                        <input type="file" name="thumbnail_file" accept="image/*" class="text-xs flex-1">
                        <button type="button"
                                onclick="openMediaPicker((url, alt) => { const img=document.getElementById('thumbnail_preview'); img.src=url; img.classList.remove('hidden'); const ph=document.getElementById('thumbnail_placeholder'); if(ph) ph.classList.add('hidden'); document.getElementById('thumbnail_path').value=url.replace(/^.*\/storage\//,''); })"
                                class="btn-ghost text-xs whitespace-nowrap">Library</button>
                    </div>
                </div>
            </aside>
        </div>
    </form>
</x-admin.layout>
