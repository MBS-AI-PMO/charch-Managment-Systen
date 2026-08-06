<x-admin.layout title="Edit page">
    <form method="POST" action="{{ route('admin.pages.update', $page) }}" enctype="multipart/form-data" x-data="{tab:'content'}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Header / action bar --}}
        <div class="flex items-end justify-between flex-wrap gap-3">
            <div>
                <div class="text-xs text-ink-muted mb-1">
                    <a href="{{ route('admin.pages.index') }}" class="hover:underline">Pages</a>
                    <span class="mx-1">/</span>
                    <span>{{ $page->title }}</span>
                </div>
                <h1 class="text-2xl font-serif">Edit page: {{ $page->title }}</h1>
                <p class="text-sm text-ink-muted mt-1">slug <code class="text-xs px-1.5 py-0.5 rounded bg-surface">/{{ $page->slug }}</code></p>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" name="is_published" value="0" class="btn-ghost text-sm">Save Draft</button>
                <button type="submit" name="is_published" value="1" class="btn-primary text-sm">Save &amp; Publish</button>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="border-b border-[rgb(var(--border))]">
            <nav class="flex gap-1">
                <button type="button" @click="tab='content'" :class="tab==='content' ? 'border-brand-primary text-ink' : 'border-transparent text-ink-muted hover:text-ink'" class="px-4 py-3 text-sm border-b-2 font-medium transition">Content</button>
                <button type="button" @click="tab='seo'" :class="tab==='seo' ? 'border-brand-primary text-ink' : 'border-transparent text-ink-muted hover:text-ink'" class="px-4 py-3 text-sm border-b-2 font-medium transition">SEO</button>
                <button type="button" @click="tab='settings'" :class="tab==='settings' ? 'border-brand-primary text-ink' : 'border-transparent text-ink-muted hover:text-ink'" class="px-4 py-3 text-sm border-b-2 font-medium transition">Settings</button>
            </nav>
        </div>

        {{-- Content tab --}}
        <div x-show="tab==='content'" x-cloak class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-5">
                <div class="card p-5 space-y-4">
                    <h2 class="text-base font-serif">Identity</h2>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Title</label>
                        <input type="text" name="title" class="input" value="{{ old('title', $page->title) }}" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Slug</label>
                        <input type="text" name="slug" class="input font-mono text-sm" value="{{ old('slug', $page->slug) }}" required>
                    </div>
                </div>

                <div class="card p-5 space-y-4">
                    <h2 class="text-base font-serif">Hero</h2>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Hero heading</label>
                        <input type="text" name="hero_heading" class="input" value="{{ old('hero_heading', $page->hero_heading) }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Hero subheading</label>
                        <input type="text" name="hero_subheading" class="input" value="{{ old('hero_subheading', $page->hero_subheading) }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Hero image</label>
                        @if($page->hero_image_path)
                            <img id="hero_image_preview" src="{{ asset('storage/'.$page->hero_image_path) }}" alt="" class="w-full max-w-md aspect-video rounded-lg object-cover border border-[rgb(var(--border))] mb-3">
                        @else
                            <img id="hero_image_preview" src="" alt="" class="hidden w-full max-w-md aspect-video rounded-lg object-cover border border-[rgb(var(--border))] mb-3">
                        @endif
                        <input type="hidden" id="hero_image_path" name="hero_image_path" value="{{ old('hero_image_path', $page->hero_image_path) }}">
                        <div class="flex gap-2 items-start">
                            <input type="file" name="hero_image_file" accept="image/*" class="text-xs flex-1">
                            <button type="button"
                                    onclick="openMediaPicker((url, alt) => { const img=document.getElementById('hero_image_preview'); img.src=url; img.classList.remove('hidden'); document.getElementById('hero_image_path').value=url.replace(/^.*\/storage\//,''); })"
                                    class="btn-ghost text-xs whitespace-nowrap">Library</button>
                        </div>
                        @if($page->hero_image_path)
                            <p class="text-xs text-ink-muted mt-1">Current: <code class="text-xs">{{ $page->hero_image_path }}</code></p>
                        @endif
                    </div>
                </div>

                <div class="card p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-serif">Body</h2>
                        <span class="text-xs text-ink-muted">HTML (sanitized on save)</span>
                    </div>
                    <textarea name="body" rows="14" class="input ck-editor font-mono text-sm">{{ old('body', $page->body) }}</textarea>
                    <p class="text-xs text-ink-muted">CKEditor is wired in M8 &mdash; for now, write raw HTML.</p>
                </div>
            </div>

            <aside class="space-y-5">
                <div class="card p-5">
                    <h3 class="text-sm font-medium mb-3">Page status</h3>
                    <div class="flex items-center gap-2">
                        @if($page->is_published)
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Published
                            </span>
                            @if($page->published_at)
                                <span class="text-xs text-ink-muted">{{ $page->published_at->format('M j, Y') }}</span>
                            @endif
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Draft
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card p-5 text-sm space-y-2">
                    <h3 class="font-medium mb-2">Author info</h3>
                    <div class="flex items-baseline justify-between"><span class="text-ink-muted">Created</span><span>{{ $page->created_at?->format('M j, Y') }}</span></div>
                    <div class="flex items-baseline justify-between"><span class="text-ink-muted">Last edit</span><span>{{ $page->updated_at?->diffForHumans() }}</span></div>
                    <div class="flex items-baseline justify-between"><span class="text-ink-muted">Revisions</span><span>{{ $page->revisions->count() }}</span></div>
                </div>
                @if($page->is_published)
                    <div class="card p-5 text-sm">
                        <h3 class="font-medium mb-2">Live URL</h3>
                        <a href="{{ url('/'.$page->slug) }}" target="_blank" class="text-brand-primary hover:underline break-all">{{ url('/'.$page->slug) }}</a>
                    </div>
                @endif
            </aside>
        </div>

        {{-- SEO tab --}}
        <div x-show="tab==='seo'" x-cloak class="card p-5 max-w-3xl space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1.5">Meta title</label>
                <input type="text" name="meta_title" class="input" value="{{ old('meta_title', $page->meta_title) }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Meta description</label>
                <textarea name="meta_description" rows="3" class="input">{{ old('meta_description', $page->meta_description) }}</textarea>
            </div>
        </div>

        {{-- Settings tab --}}
        <div x-show="tab==='settings'" x-cloak class="card p-5 max-w-3xl space-y-5">
            <label class="flex items-start gap-3">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published', $page->is_published) ? 'checked' : '' }} class="mt-0.5 rounded border-[rgb(var(--border))] text-brand-primary focus:ring-brand-primary">
                <span>
                    <span class="block text-sm font-medium">Published</span>
                    <span class="block text-xs text-ink-muted">When unchecked, the page returns a 404 to visitors.</span>
                </span>
            </label>
            <div class="border-t border-[rgb(var(--border))] pt-5">
                <h3 class="text-sm font-medium mb-2">Revision history</h3>
                <p class="text-xs text-ink-muted">{{ $page->revisions->count() }} revisions saved.</p>
            </div>
        </div>
    </form>
</x-admin.layout>
