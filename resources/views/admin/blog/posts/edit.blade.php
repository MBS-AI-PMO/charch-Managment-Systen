@php
    $isNew = !$post->exists;
    $action = $isNew ? route('admin.blog.posts.store') : route('admin.blog.posts.update', $post);
@endphp
<x-admin.layout title="{{ $isNew ? 'New post' : 'Edit post' }}">
    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" x-data="{seoOpen:false}" class="space-y-6">
        @csrf
        @unless($isNew) @method('PUT') @endunless

        <div class="flex items-end justify-between flex-wrap gap-3">
            <div>
                <div class="text-xs text-ink-muted mb-1">
                    <a href="{{ route('admin.blog.posts.index') }}" class="hover:underline">News</a>
                    <span class="mx-1">/</span>
                    <span>{{ $isNew ? 'New post' : $post->title }}</span>
                </div>
                <h1 class="text-2xl font-serif">{{ $isNew ? 'Write a new post' : 'Edit post' }}</h1>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.blog.posts.index') }}" class="btn-ghost text-sm">Cancel</a>
                <button type="submit" name="is_published" value="0" class="btn-ghost text-sm">Save Draft</button>
                <button type="submit" name="is_published" value="1" class="btn-primary text-sm">Save &amp; Publish</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-5">
                <div class="card p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Title</label>
                        <input type="text" name="title" class="input text-lg" value="{{ old('title', $post->title) }}" required>
                        @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Slug</label>
                        <input type="text" name="slug" class="input font-mono text-sm" value="{{ old('slug', $post->slug) }}" placeholder="auto-generated from title if empty">
                        @error('slug')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Excerpt</label>
                        <textarea name="excerpt" rows="2" class="input">{{ old('excerpt', $post->excerpt) }}</textarea>
                        @error('excerpt')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="card p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-serif">Body</h2>
                        <span class="text-xs text-ink-muted">HTML (CKEditor wired in M8)</span>
                    </div>
                    <textarea name="body" rows="16" class="input ck-editor font-mono text-sm" required>{{ old('body', $post->body) }}</textarea>
                    @error('body')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="card">
                    <button type="button" @click="seoOpen=!seoOpen" class="w-full px-5 py-4 flex items-center justify-between text-left">
                        <div>
                            <h2 class="text-base font-serif">SEO</h2>
                            <p class="text-xs text-ink-muted">Override meta title and description.</p>
                        </div>
                        <svg :class="seoOpen ? 'rotate-180' : ''" class="transition-transform text-ink-muted" width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path d="M5.25 7.5 10 12.25 14.75 7.5"/></svg>
                    </button>
                    <div x-show="seoOpen" x-cloak class="px-5 pb-5 space-y-4 border-t border-[rgb(var(--border))] pt-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Meta title</label>
                            <input type="text" name="meta_title" class="input" value="{{ old('meta_title', $post->meta_title) }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Meta description</label>
                            <textarea name="meta_description" rows="2" class="input">{{ old('meta_description', $post->meta_description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="space-y-5">
                <div class="card p-5 space-y-4">
                    <h3 class="text-sm font-medium">Publish</h3>
                    <div class="flex items-center gap-2">
                        @if($post->is_published)
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Published</span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Draft</span>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Publish date</label>
                        <input type="datetime-local" name="published_at" class="input" value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}">
                        @error('published_at')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="card p-5 space-y-4">
                    <h3 class="text-sm font-medium">Organize</h3>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Category</label>
                        <select name="category_id" class="input">
                            <option value="">— None —</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" @selected((string) old('category_id', $post->category_id) === (string) $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="card p-5">
                    <h3 class="text-sm font-medium mb-3">Featured image</h3>
                    <img id="featured_image_preview"
                         src="{{ $post->featured_image_path ? asset('storage/'.$post->featured_image_path) : '' }}"
                         alt=""
                         class="w-full aspect-video rounded-lg object-cover border border-[rgb(var(--border))] mb-3 {{ $post->featured_image_path ? '' : 'hidden' }}">
                    @unless($post->featured_image_path)
                        <div id="featured_image_placeholder" class="w-full aspect-video rounded-lg bg-gradient-to-br from-brand-primary/20 to-brand-secondary/30 border border-[rgb(var(--border))] mb-3"></div>
                    @endunless
                    <input type="hidden" id="featured_image_path" name="featured_image_path" value="{{ old('featured_image_path', $post->featured_image_path) }}">
                    <div class="flex gap-2 items-start">
                        <input type="file" name="featured_image_file" accept="image/*" class="text-xs flex-1">
                        <button type="button"
                                onclick="openMediaPicker((url, alt) => {
                                    const img = document.getElementById('featured_image_preview');
                                    img.src = url; img.classList.remove('hidden');
                                    const ph = document.getElementById('featured_image_placeholder'); if (ph) ph.classList.add('hidden');
                                    document.getElementById('featured_image_path').value = url.replace(/^.*\/storage\//, '');
                                })"
                                class="btn-ghost text-xs whitespace-nowrap">Library</button>
                    </div>
                    <p class="text-xs text-ink-muted mt-2">PNG/JPG/WebP up to 10 MB. Or pick from the library.</p>
                </div>
            </aside>
        </div>
    </form>
</x-admin.layout>
