@php
    use Illuminate\Support\Facades\Storage;

    $allActive = ! $currentFolder && request('folder') !== 'root';
    $rootActive = request('folder') === 'root';
@endphp
<x-admin.layout title="Gallery">
    <div class="mb-5 flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-serif">Gallery</h1>
            <p class="text-sm text-ink-muted mt-1">
                Photos on the public <a href="{{ route('site.gallery.index') }}" target="_blank" class="text-brand-primary hover:underline">Gallery</a> page.
                Albums group photos — upload, replace, or delete anytime.
            </p>
        </div>
        <a href="{{ route('site.gallery.index') }}" target="_blank" class="btn-ghost text-sm">View public gallery →</a>
    </div>

    <div class="space-y-4">
        <div class="card p-3 sm:p-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="admin-filter-scroll flex items-center gap-1.5 overflow-x-auto pb-0.5 -mx-0.5 px-0.5">
                    <a href="{{ route('admin.gallery.index') }}"
                       @class(['admin-filter-chip', 'is-active' => $allActive])>
                        All photos
                    </a>
                    <a href="{{ route('admin.gallery.index', ['folder' => 'root']) }}"
                       @class(['admin-filter-chip', 'is-active' => $rootActive])>
                        Uncategorized
                    </a>
                    @foreach($folders->whereNull('parent_id') as $folder)
                        @include('admin.gallery._folder-chip', ['folder' => $folder, 'depth' => 0])
                    @endforeach
                </div>

                <form method="POST" action="{{ route('admin.media.folders.store') }}" class="flex gap-1.5 shrink-0 w-full sm:w-auto">
                    @csrf
                    @if($currentFolder)
                        <input type="hidden" name="parent_id" value="{{ $currentFolder->id }}">
                    @endif
                    <input type="text" name="name" class="input text-xs py-1.5 min-w-0 flex-1 sm:w-40" placeholder="New album…" required>
                    <button type="submit" class="btn-ghost text-xs px-3 whitespace-nowrap">+ Album</button>
                </form>
            </div>
            @error('name')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror

            @if($currentFolder)
                <div class="mt-3 pt-3 border-t border-[rgb(var(--border))] flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                    <div class="text-xs text-ink-muted shrink-0">
                        Album: <strong class="text-ink">{{ $currentFolder->name }}</strong>
                    </div>
                    <form method="POST" action="{{ route('admin.media.folders.update', $currentFolder) }}" class="flex gap-1.5 flex-1 min-w-0">
                        @csrf @method('PATCH')
                        <input type="text" name="name" class="input text-xs py-1.5 min-w-0 flex-1" value="{{ $currentFolder->name }}" required>
                        <button type="submit" class="btn-ghost text-xs px-2.5 whitespace-nowrap">Rename</button>
                    </form>
                    <div class="flex items-center gap-3 text-xs">
                        <a href="{{ route('site.gallery.show', $currentFolder) }}" target="_blank" class="text-brand-primary hover:underline whitespace-nowrap">Open on site</a>
                        <form method="POST" action="{{ route('admin.media.folders.destroy', $currentFolder) }}" onsubmit="return confirm('Delete this album? It must be empty.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline whitespace-nowrap">Delete</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <form method="POST"
              action="{{ route('admin.gallery.store') }}"
              enctype="multipart/form-data"
              x-data="{dragging:false, files:0}"
              @dragenter.prevent="dragging=true"
              @dragover.prevent="dragging=true"
              @dragleave.prevent="dragging=false"
              @drop.prevent="dragging=false; $refs.fileInput.files = $event.dataTransfer.files; files = $refs.fileInput.files.length"
              class="card p-4 sm:p-5">
            @csrf
            @if($currentFolder)
                <input type="hidden" name="folder_id" value="{{ $currentFolder->id }}">
            @endif
            <div class="border-2 border-dashed rounded-lg py-6 sm:py-7 px-4 text-center transition"
                 :class="dragging ? 'border-brand-primary bg-brand-primary/5' : 'border-[rgb(var(--border))]'">
                <svg class="mx-auto mb-2 text-ink-muted" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 16V4M12 4l-4 4M12 4l4 4"/><rect x="3" y="16" width="18" height="5" rx="1"/></svg>
                <p class="text-sm">
                    <span x-show="files === 0">Drop photos here, or
                        <label class="text-brand-primary cursor-pointer underline">
                            <input type="file" name="files[]" multiple accept="image/jpeg,image/png,image/webp,image/gif" x-ref="fileInput" @change="files = $refs.fileInput.files.length" class="hidden">
                            browse
                        </label>
                    </span>
                    <span x-show="files > 0">
                        <span x-text="files"></span> photo(s) ready
                        <button type="submit" class="ml-2 btn-primary text-xs">Upload</button>
                        <button type="button" @click="$refs.fileInput.value=''; files=0" class="ml-1 btn-ghost text-xs">Clear</button>
                    </span>
                </p>
                <p class="text-xs text-ink-muted mt-1.5">
                    JPG, PNG, WebP or GIF · up to 10 MB
                    @if($currentFolder)
                        · into <strong>{{ $currentFolder->name }}</strong>
                    @endif
                </p>
            </div>
            @error('files')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
            @error('file')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
        </form>

        <div class="card p-4 sm:p-5">
            @if($items->isEmpty())
                <div class="py-10 text-center text-sm text-ink-muted">
                    @if($currentFolder)
                        No photos in <strong>{{ $currentFolder->name }}</strong> yet.
                    @else
                        No gallery photos yet. Upload above to get started.
                    @endif
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                    @foreach($items as $file)
                        @php $url = $file->path ? Storage::disk($file->disk ?: 'public')->url($file->path) : ''; @endphp
                        <div class="group relative border border-[rgb(var(--border))] rounded-lg overflow-hidden bg-surface flex flex-col">
                            <div class="aspect-square bg-white">
                                @if($url)
                                    <img src="{{ $url }}?v={{ optional($file->updated_at)->timestamp }}" alt="{{ $file->alt_text ?? $file->filename }}" loading="lazy" class="object-cover w-full h-full">
                                @endif
                            </div>
                            <div class="px-2 py-2 text-[11px] space-y-1.5 text-ink-muted flex-1 flex flex-col">
                                <div class="truncate text-ink" title="{{ $file->filename }}">{{ $file->filename }}</div>
                                <div class="flex items-center justify-between">
                                    <span>{{ $file->width ? $file->width.'×'.$file->height : '—' }}</span>
                                    <span>{{ number_format(($file->size ?? 0) / 1024, 1) }} KB</span>
                                </div>
                                <form method="POST" action="{{ route('admin.gallery.update', $file) }}">
                                    @csrf @method('PATCH')
                                    <input type="text" name="alt_text" value="{{ $file->alt_text }}" placeholder="Caption / alt text" class="input text-[10px] py-1 px-1.5" onchange="this.form.submit()">
                                </form>
                            </div>
                            <div class="flex border-t border-[rgb(var(--border))] bg-white text-xs">
                                <form method="POST" action="{{ route('admin.gallery.replace', $file) }}" enctype="multipart/form-data" class="flex-1">
                                    @csrf
                                    <label class="block w-full py-1.5 text-center text-brand-primary hover:bg-brand-primary/5 font-medium cursor-pointer">
                                        Replace
                                        <input type="file" name="file" accept="image/jpeg,image/png,image/webp,image/gif" class="hidden" onchange="if (this.files.length) this.form.submit()">
                                    </label>
                                </form>
                                <a href="{{ $url }}" target="_blank" class="flex-1 py-1.5 text-ink-muted hover:bg-surface text-center border-l border-[rgb(var(--border))]">Open</a>
                                <form method="POST" action="{{ route('admin.gallery.destroy', $file) }}" class="border-l border-[rgb(var(--border))]" onsubmit="return confirm('Remove this photo from the gallery?')">
                                    @csrf @method('DELETE')
                                    <button class="px-2.5 py-1.5 text-red-600 hover:bg-red-50">Delete</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($items->hasPages())
                    <div class="mt-5">{{ $items->links() }}</div>
                @endif
            @endif
        </div>
    </div>
</x-admin.layout>
