@php
    use Illuminate\Support\Facades\Storage;
@endphp
<x-admin.layout title="{{ $picker ? 'Media picker' : 'Media library' }}">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-serif">{{ $picker ? 'Choose a file' : 'Media library' }}</h1>
            <p class="text-sm text-ink-muted mt-1">
                @if($picker)
                    Click <strong>Select</strong> on any file to send it back to the editor.
                @else
                    Uploads used across the public site. Drop files into the zone below to add them.
                @endif
            </p>
        </div>
        @if($picker)
            <button type="button" onclick="window.close()" class="btn-ghost text-sm">Close</button>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Folder tree --}}
        <aside class="space-y-5">
            <div class="card p-5">
                <h3 class="text-sm font-medium mb-3">Folders</h3>

                <form method="POST" action="{{ route('admin.media.folders.store') }}" class="mb-3 flex gap-1.5">
                    @csrf
                    @if($currentFolder)
                        <input type="hidden" name="parent_id" value="{{ $currentFolder->id }}">
                    @endif
                    <input type="text" name="name" class="input text-xs py-1.5" placeholder="New folder…" required>
                    <button type="submit" class="btn-ghost text-xs px-2.5">+</button>
                </form>
                @error('name')<p class="-mt-2 mb-2 text-xs text-red-600">{{ $message }}</p>@enderror

                <ul class="space-y-1 text-sm">
                    <li>
                        <a href="{{ route('admin.media.index', $picker ? ['picker' => 1] : []) }}"
                           class="block px-2 py-1 rounded {{ !$currentFolder ? 'bg-brand-primary/10 text-brand-primary' : 'text-ink-muted hover:bg-surface' }}">
                            All files
                        </a>
                    </li>
                    @forelse($folders->whereNull('parent_id') as $folder)
                        @include('admin.media._folder-node', ['folder' => $folder, 'depth' => 0])
                    @empty
                        <li class="text-xs text-ink-muted px-2 py-1">No folders yet.</li>
                    @endforelse
                </ul>
            </div>

            @if(!$picker && $currentFolder)
                <div class="card p-5 text-xs space-y-2">
                    <h3 class="text-sm font-medium mb-1">Current folder</h3>
                    <div class="text-ink-muted">Path: <code class="text-[11px]">{{ $currentFolder->path }}</code></div>
                    <form method="POST" action="{{ route('admin.media.folders.update', $currentFolder) }}" class="space-y-2">
                        @csrf @method('PATCH')
                        <input type="text" name="name" class="input text-xs" value="{{ $currentFolder->name }}" required>
                        <button type="submit" class="btn-ghost text-xs w-full">Rename</button>
                    </form>
                    <form method="POST" action="{{ route('admin.media.folders.destroy', $currentFolder) }}" onsubmit="return confirm('Delete this folder? It must be empty.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline text-xs">Delete folder</button>
                    </form>
                </div>
            @endif
        </aside>

        {{-- Right: upload + grid --}}
        <div class="lg:col-span-3 space-y-5">
            {{-- Upload zone --}}
            <form method="POST"
                  action="{{ route('admin.media.store') }}"
                  enctype="multipart/form-data"
                  x-data="{dragging:false, files:0}"
                  @dragenter.prevent="dragging=true"
                  @dragover.prevent="dragging=true"
                  @dragleave.prevent="dragging=false"
                  @drop.prevent="dragging=false; $refs.fileInput.files = $event.dataTransfer.files; files = $refs.fileInput.files.length"
                  class="card p-6">
                @csrf
                @if($currentFolder)
                    <input type="hidden" name="folder_id" value="{{ $currentFolder->id }}">
                @endif
                <div class="border-2 border-dashed rounded-lg py-8 px-4 text-center transition"
                     :class="dragging ? 'border-brand-primary bg-brand-primary/5' : 'border-[rgb(var(--border))]'">
                    <svg class="mx-auto mb-2 text-ink-muted" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 16V4M12 4l-4 4M12 4l4 4"/><rect x="3" y="16" width="18" height="5" rx="1"/></svg>
                    <p class="text-sm">
                        <span x-show="files === 0">Drag files here, or
                            <label class="text-brand-primary cursor-pointer underline">
                                <input type="file" name="files[]" multiple x-ref="fileInput" @change="files = $refs.fileInput.files.length" class="hidden" accept="image/jpeg,image/png,image/webp,image/gif,application/pdf">
                                browse
                            </label>
                        </span>
                        <span x-show="files > 0">
                            <span x-text="files"></span> file(s) ready
                            <button type="submit" class="ml-2 btn-primary text-xs">Upload</button>
                            <button type="button" @click="$refs.fileInput.value=''; files=0" class="ml-1 btn-ghost text-xs">Clear</button>
                        </span>
                    </p>
                    <p class="text-xs text-ink-muted mt-1.5">JPG, PNG, WebP, GIF or PDF &middot; up to 10 MB each</p>
                </div>
                @error('file')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
                @error('files')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
            </form>

            {{-- Grid --}}
            <div class="card p-5">
                @if($items->isEmpty())
                    <div class="py-12 text-center text-sm text-ink-muted">
                        @if($currentFolder)
                            No files in <strong>{{ $currentFolder->name }}</strong> yet.
                        @else
                            No files uploaded yet. Drag one onto the zone above.
                        @endif
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @foreach($items as $file)
                            @php
                                $isImg = $file->mime_type && str_starts_with($file->mime_type, 'image/');
                                $url = $file->path ? Storage::disk($file->disk ?: 'public')->url($file->path) : '';
                            @endphp
                            <div class="group relative border border-[rgb(var(--border))] rounded-lg overflow-hidden bg-surface flex flex-col">
                                <div class="aspect-square flex items-center justify-center bg-white">
                                    @if($isImg && $file->path)
                                        <img src="{{ $url }}" alt="{{ $file->alt_text ?? $file->filename }}" loading="lazy" class="object-cover w-full h-full">
                                    @else
                                        <div class="flex flex-col items-center text-ink-muted">
                                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/></svg>
                                            <span class="text-[10px] uppercase mt-1">{{ pathinfo($file->filename ?? '', PATHINFO_EXTENSION) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="px-2 py-2 text-[11px] space-y-1 text-ink-muted flex-1 flex flex-col">
                                    <div class="truncate text-ink" title="{{ $file->filename }}">{{ $file->filename }}</div>
                                    <div class="flex items-center justify-between">
                                        <span>{{ $file->width ? $file->width.'x'.$file->height : '—' }}</span>
                                        <span>{{ number_format(($file->size ?? 0) / 1024, 1) }} KB</span>
                                    </div>
                                    <form method="POST" action="{{ route('admin.media.update', $file) }}" class="mt-1">
                                        @csrf @method('PATCH')
                                        <input type="text" name="alt_text" value="{{ $file->alt_text }}" placeholder="alt text" class="input text-[10px] py-1 px-1.5" onchange="this.form.submit()">
                                    </form>
                                </div>
                                <div class="flex border-t border-[rgb(var(--border))] bg-white">
                                    @if($picker)
                                        <button type="button"
                                                onclick="window.opener && window.opener.postMessage({kind:'media-picked', url: @js($url), alt: @js($file->alt_text ?? '')}, '*'); window.close();"
                                                class="flex-1 py-1.5 text-xs text-brand-primary hover:bg-brand-primary/5 font-medium">
                                            Select
                                        </button>
                                    @else
                                        <a href="{{ $url }}" target="_blank" class="flex-1 py-1.5 text-xs text-ink-muted hover:bg-surface text-center">Open</a>
                                    @endif
                                    @can('manage-media')
                                        <form method="POST" action="{{ route('admin.media.destroy', $file) }}" class="border-l border-[rgb(var(--border))]" onsubmit="return confirm('Delete this file?')">
                                            @csrf @method('DELETE')
                                            <button class="px-2.5 py-1.5 text-xs text-red-600 hover:bg-red-50">×</button>
                                        </form>
                                    @endcan
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
    </div>
</x-admin.layout>
