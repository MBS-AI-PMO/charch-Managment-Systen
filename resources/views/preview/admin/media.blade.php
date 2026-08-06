@php
$thumbs = [
    ['name'=>'hero-sunday.jpg',    'size'=>'1.2 MB', 'tint'=>'from-amber-200 to-rose-300'],
    ['name'=>'youth-camp-04.jpg',  'size'=>'2.4 MB', 'tint'=>'from-emerald-200 to-emerald-400'],
    ['name'=>'pastor-greg.jpg',    'size'=>'820 KB', 'tint'=>'from-stone-200 to-stone-400'],
    ['name'=>'easter-stage.jpg',   'size'=>'1.7 MB', 'tint'=>'from-rose-200 to-pink-400'],
    ['name'=>'worship-night.jpg',  'size'=>'2.1 MB', 'tint'=>'from-violet-200 to-violet-500'],
    ['name'=>'kids-craft.jpg',     'size'=>'940 KB', 'tint'=>'from-sky-200 to-sky-400'],
    ['name'=>'sermon-bg.jpg',      'size'=>'1.3 MB', 'tint'=>'from-brand-primary/30 to-brand-secondary/40'],
    ['name'=>'community-day.jpg',  'size'=>'2.8 MB', 'tint'=>'from-emerald-200 to-teal-400'],
    ['name'=>'logo-mark.svg',      'size'=>'12 KB',  'tint'=>'from-stone-100 to-stone-300'],
    ['name'=>'baptism-river.jpg',  'size'=>'3.1 MB', 'tint'=>'from-blue-200 to-blue-400'],
    ['name'=>'staff-photo.jpg',    'size'=>'1.1 MB', 'tint'=>'from-amber-200 to-amber-400'],
    ['name'=>'meeting-room.jpg',   'size'=>'1.5 MB', 'tint'=>'from-stone-200 to-amber-200'],
];
@endphp

<x-admin.layout title="Media library">
    <div x-data="{view:'grid', sermonsOpen:true}" class="space-y-6">
        <div class="flex items-end justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-2xl font-serif">Media library</h1>
                <p class="text-sm text-ink-muted mt-1">All images, audio, video, and documents your site uses.</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-1 p-1 rounded-md border border-[rgb(var(--border))] bg-white">
                    <button @click="view='grid'" :class="view==='grid' ? 'bg-brand-primary/10 text-brand-primary' : 'text-ink-muted'" class="px-2 py-1 rounded text-xs flex items-center gap-1">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                        Grid
                    </button>
                    <button @click="view='list'" :class="view==='list' ? 'bg-brand-primary/10 text-brand-primary' : 'text-ink-muted'" class="px-2 py-1 rounded text-xs flex items-center gap-1">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13M8 12h13M8 18h13"/><circle cx="4" cy="6" r="1"/><circle cx="4" cy="12" r="1"/><circle cx="4" cy="18" r="1"/></svg>
                        List
                    </button>
                </div>
                <button class="btn-primary text-sm flex items-center gap-1.5">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M17 8l-5-5-5 5"/><path d="M12 3v12"/></svg>
                    Upload
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            {{-- Folder tree --}}
            <aside class="lg:col-span-3">
                <div class="card p-4">
                    <input type="text" class="input mb-3" placeholder="Search files…">
                    <ul class="text-sm space-y-0.5">
                        <li>
                            <a href="#" class="flex items-center gap-2 px-2 py-1.5 rounded bg-brand-primary/10 text-brand-primary font-medium">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"/></svg>
                                All media <span class="ml-auto text-xs text-ink-muted">128</span>
                            </a>
                        </li>
                        <li>
                            <button @click="sermonsOpen=!sermonsOpen" class="w-full flex items-center gap-2 px-2 py-1.5 rounded hover:bg-surface text-left">
                                <svg :class="sermonsOpen ? 'rotate-90' : ''" class="transition-transform text-ink-muted" width="10" height="10" viewBox="0 0 20 20" fill="currentColor"><path d="M7.5 5 12.25 10 7.5 15z"/></svg>
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="text-ink-muted"><path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"/></svg>
                                Sermons <span class="ml-auto text-xs text-ink-muted">42</span>
                            </button>
                            <ul x-show="sermonsOpen" x-cloak class="ml-6 mt-1 space-y-0.5 border-l border-[rgb(var(--border))] pl-2">
                                <li><a href="#" class="block px-2 py-1 rounded hover:bg-surface text-ink-muted text-xs">Walking by faith</a></li>
                                <li><a href="#" class="block px-2 py-1 rounded hover:bg-surface text-ink-muted text-xs">Psalms</a></li>
                                <li><a href="#" class="block px-2 py-1 rounded hover:bg-surface text-ink-muted text-xs">Advent 2025</a></li>
                            </ul>
                        </li>
                        <li><a href="#" class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-surface"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="text-ink-muted"><path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"/></svg>Events <span class="ml-auto text-xs text-ink-muted">31</span></a></li>
                        <li><a href="#" class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-surface"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="text-ink-muted"><path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"/></svg>Blog <span class="ml-auto text-xs text-ink-muted">24</span></a></li>
                        <li><a href="#" class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-surface"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="text-ink-muted"><path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"/></svg>Brand assets <span class="ml-auto text-xs text-ink-muted">7</span></a></li>
                    </ul>

                    <div class="mt-4 pt-4 border-t border-[rgb(var(--border))]">
                        <div class="text-xs text-ink-muted mb-1">Storage used</div>
                        <div class="h-2 rounded-full bg-surface overflow-hidden">
                            <div class="h-full bg-brand-primary" style="width:34%"></div>
                        </div>
                        <div class="text-xs text-ink-muted mt-1">3.4 GB of 10 GB</div>
                    </div>
                </div>
            </aside>

            {{-- Thumbs --}}
            <section class="lg:col-span-9">
                <div class="card p-4">
                    <div class="flex items-center justify-between text-xs text-ink-muted mb-4">
                        <span>Showing 12 of 128 files in <span class="text-ink font-medium">All media</span></span>
                        <select class="input w-40 text-xs"><option>Newest first</option><option>Oldest first</option><option>Name (A→Z)</option><option>Largest first</option></select>
                    </div>

                    <div x-show="view==='grid'" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach($thumbs as $t)
                            <figure class="group relative aspect-square rounded-lg overflow-hidden border border-[rgb(var(--border))] bg-gradient-to-br {{ $t['tint'] }} cursor-pointer">
                                <figcaption class="absolute inset-x-0 bottom-0 p-2 bg-gradient-to-t from-black/70 to-transparent text-white opacity-0 group-hover:opacity-100 transition">
                                    <div class="text-[11px] font-medium truncate">{{ $t['name'] }}</div>
                                    <div class="text-[10px] text-white/70">{{ $t['size'] }}</div>
                                </figcaption>
                                <div class="absolute top-1.5 right-1.5 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                                    <button class="w-6 h-6 rounded bg-white/90 text-ink flex items-center justify-center" title="Info">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8h.01M11 12h1v5h1"/></svg>
                                    </button>
                                    <button class="w-6 h-6 rounded bg-white/90 text-brand-primary flex items-center justify-center" title="Delete">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M9 7V5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/><path d="M6 7v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7"/></svg>
                                    </button>
                                </div>
                            </figure>
                        @endforeach
                    </div>

                    <div x-show="view==='list'" x-cloak class="text-sm">
                        <table class="w-full">
                            <thead class="text-xs text-ink-muted uppercase tracking-wider">
                                <tr>
                                    <th class="text-left px-3 py-2 font-medium">File</th>
                                    <th class="text-left px-3 py-2 font-medium">Size</th>
                                    <th class="text-left px-3 py-2 font-medium">Uploaded</th>
                                    <th class="text-right px-3 py-2 font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[rgb(var(--border))]">
                                @foreach($thumbs as $t)
                                    <tr class="hover:bg-surface/60">
                                        <td class="px-3 py-2 flex items-center gap-3">
                                            <span class="w-10 h-10 rounded bg-gradient-to-br {{ $t['tint'] }}"></span>
                                            <span class="font-mono text-xs">{{ $t['name'] }}</span>
                                        </td>
                                        <td class="px-3 py-2 text-ink-muted">{{ $t['size'] }}</td>
                                        <td class="px-3 py-2 text-ink-muted">May 28, 2026</td>
                                        <td class="px-3 py-2 text-right">
                                            <a href="#" class="text-xs text-brand-primary hover:underline">Rename</a>
                                            <a href="#" class="text-xs text-ink-muted hover:text-brand-primary ml-3">Delete</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-admin.layout>
