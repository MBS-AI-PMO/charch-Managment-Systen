@php
    $isNew = !$post->exists;
    $action = $isNew ? route('admin.feed.store') : route('admin.feed.update', $post);
    $publishedDefault = $post->published_at?->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i');
@endphp
<x-admin.layout title="{{ $isNew ? 'New feed post' : 'Edit feed post' }}">
    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @unless($isNew) @method('PUT') @endunless

        <div class="flex items-end justify-between flex-wrap gap-3">
            <div>
                <div class="text-xs text-ink-muted mb-1">
                    <a href="{{ route('admin.feed.index') }}" class="hover:underline">Community feed</a>
                    <span class="mx-1">/</span>
                    <span>{{ $isNew ? 'New post' : ($post->title ?: 'Edit post') }}</span>
                </div>
                <h1 class="text-2xl font-serif">{{ $isNew ? 'New feed post' : 'Edit feed post' }}</h1>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.feed.index') }}" class="btn-ghost text-sm">Cancel</a>
                <button type="submit" class="btn-primary text-sm">{{ $isNew ? 'Publish' : 'Save changes' }}</button>
            </div>
        </div>

        @if ($errors->any())
            <div class="px-4 py-3 rounded-lg border border-red-200 bg-red-50 text-red-800 text-sm">
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-5">
                <div class="card p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Title <span class="text-ink-muted font-normal">(optional)</span></label>
                        <input type="text" name="title" class="input text-lg" value="{{ old('title', $post->title) }}" maxlength="200" placeholder="A short headline">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Body</label>
                        <textarea name="body" rows="12" class="input ck-editor" required>{{ old('body', $post->body) }}</textarea>
                        <p class="mt-1 text-xs text-ink-muted">Rich text. HTML is sanitized on save.</p>
                    </div>
                </div>
            </div>

            <aside class="space-y-5">
                <div class="card p-5 space-y-3">
                    <h3 class="text-sm font-medium">Schedule &amp; visibility</h3>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Published at</label>
                        <input type="datetime-local" name="published_at" class="input"
                               value="{{ old('published_at', $publishedDefault) }}">
                        <p class="mt-1 text-xs text-ink-muted">Posts dated in the future stay hidden until then.</p>
                    </div>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="hidden" name="pinned" value="0">
                        <input type="checkbox" name="pinned" value="1"
                               {{ old('pinned', $post->pinned) ? 'checked' : '' }}
                               class="rounded border-[rgb(var(--border))] text-brand-primary focus:ring-brand-primary">
                        <span>Pin to top of feed</span>
                    </label>
                    @if($isNew)
                        <label class="flex items-start gap-2 text-sm pt-2 border-t border-[rgb(var(--border))]">
                            <input type="hidden" name="broadcast" value="0">
                            <input type="checkbox" name="broadcast" value="1"
                                   {{ old('broadcast') ? 'checked' : '' }}
                                   class="mt-1 rounded border-[rgb(var(--border))] text-brand-primary focus:ring-brand-primary">
                            <span>
                                <span class="block">Broadcast by email</span>
                                <span class="block text-xs text-ink-muted">Send to all verified members.</span>
                            </span>
                        </label>
                    @endif
                </div>

                <div class="card p-5">
                    <h3 class="text-sm font-medium mb-3">Image</h3>
                    @if($post->image_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($post->image_path) }}" alt=""
                             class="w-full aspect-video rounded-lg object-cover border border-[rgb(var(--border))] mb-3">
                    @endif
                    <input type="file" name="image" accept="image/*" class="text-xs w-full">
                    <p class="mt-2 text-xs text-ink-muted">Optional. Max 10 MB. Resized to 2400 px wide on upload.</p>
                    <input type="hidden" name="image_path" value="{{ old('image_path', $post->image_path) }}">
                </div>
            </aside>
        </div>
    </form>
</x-admin.layout>
