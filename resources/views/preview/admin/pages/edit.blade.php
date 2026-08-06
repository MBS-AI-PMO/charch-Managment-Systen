<x-admin.layout title="Edit page">
    <div x-data="{tab:'content', addOpen:false}" class="space-y-6">
        {{-- Header / action bar --}}
        <div class="flex items-end justify-between flex-wrap gap-3">
            <div>
                <div class="text-xs text-ink-muted mb-1">
                    <a href="{{ route('admin.preview.pages') }}" class="hover:underline">Pages</a>
                    <span class="mx-1">/</span>
                    <span>Home</span>
                </div>
                <h1 class="text-2xl font-serif">Edit page: Home</h1>
                <p class="text-sm text-ink-muted mt-1">Singleton page • slug <code class="text-xs px-1.5 py-0.5 rounded bg-surface">/</code></p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-ink-muted hidden sm:inline">
                    <svg class="inline-block w-3.5 h-3.5 -mt-0.5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m9 12 2 2 4-4"/></svg>
                    Last saved 2 min ago
                </span>
                <button class="btn-ghost text-sm">Preview</button>
                <button class="btn-ghost text-sm">Save Draft</button>
                <button class="btn-primary text-sm">Save &amp; Publish</button>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="border-b border-[rgb(var(--border))]">
            <nav class="flex gap-1">
                <button @click="tab='content'" :class="tab==='content' ? 'border-brand-primary text-ink' : 'border-transparent text-ink-muted hover:text-ink'" class="px-4 py-3 text-sm border-b-2 font-medium transition">Content</button>
                <button @click="tab='seo'"     :class="tab==='seo'     ? 'border-brand-primary text-ink' : 'border-transparent text-ink-muted hover:text-ink'" class="px-4 py-3 text-sm border-b-2 font-medium transition">SEO</button>
                <button @click="tab='settings'" :class="tab==='settings' ? 'border-brand-primary text-ink' : 'border-transparent text-ink-muted hover:text-ink'" class="px-4 py-3 text-sm border-b-2 font-medium transition">Settings</button>
            </nav>
        </div>

        {{-- Content tab --}}
        <div x-show="tab==='content'" x-cloak class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-5">
                <div class="card p-5 space-y-4">
                    <h2 class="text-base font-serif">Hero</h2>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Hero heading</label>
                        <input type="text" class="input" value="A place to belong, become, and be loved.">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Hero subheading</label>
                        <input type="text" class="input" value="Grace Community is a church for the curious, the worn out, and everyone in between.">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Hero image</label>
                        <div class="flex items-center gap-3">
                            <div class="w-24 h-16 rounded-md bg-gradient-to-br from-brand-primary/30 to-brand-secondary/40 border border-[rgb(var(--border))]"></div>
                            <button class="btn-ghost text-xs">Choose from media library</button>
                            <button class="text-xs text-ink-muted hover:text-brand-primary">Remove</button>
                        </div>
                    </div>
                </div>

                <div class="card p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-serif">Body</h2>
                        <div class="flex items-center gap-1 text-xs text-ink-muted">
                            <span class="px-1.5 py-0.5 rounded border border-[rgb(var(--border))] bg-surface">B</span>
                            <span class="px-1.5 py-0.5 rounded border border-[rgb(var(--border))] bg-surface italic">I</span>
                            <span class="px-1.5 py-0.5 rounded border border-[rgb(var(--border))] bg-surface underline">U</span>
                            <span class="px-1.5 py-0.5 rounded border border-[rgb(var(--border))] bg-surface">H₂</span>
                            <span class="px-1.5 py-0.5 rounded border border-[rgb(var(--border))] bg-surface">¶</span>
                        </div>
                    </div>
                    <textarea id="body" rows="14" class="input ck-editor font-mono text-sm">Welcome to Grace Community Church. We are a family-oriented church that loves God and loves people…</textarea>
                    <p class="text-xs text-ink-muted">A rich text editor (CKEditor) will replace this textarea after wiring.</p>
                </div>

                <div class="card p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-base font-serif">Sections</h2>
                            <p class="text-xs text-ink-muted mt-0.5">Drag to reorder. Click a section to edit its fields.</p>
                        </div>
                        <div class="relative" x-data="{open:false}" @keydown.escape.window="open=false">
                            <button @click="open=!open" :aria-expanded="open" aria-haspopup="menu" class="btn-primary text-xs">+ Add section</button>
                            <div x-show="open" @click.away="open=false" x-cloak role="menu" class="absolute right-0 mt-2 w-56 card p-1.5 text-sm z-20 bg-white">
                                <a href="#" class="block px-3 py-2 hover:bg-surface rounded">Service times strip</a>
                                <a href="#" class="block px-3 py-2 hover:bg-surface rounded">Featured sermon</a>
                                <a href="#" class="block px-3 py-2 hover:bg-surface rounded">Ministries grid</a>
                                <a href="#" class="block px-3 py-2 hover:bg-surface rounded">Upcoming events</a>
                                <a href="#" class="block px-3 py-2 hover:bg-surface rounded">Call to action</a>
                            </div>
                        </div>
                    </div>
                    <ul class="space-y-2">
                        @foreach([['Service times', 'Sun 9 AM • Sun 11 AM • Wed 7 PM'], ['Featured sermon', 'Hope in the Wilderness — Pastor Greg'], ['Upcoming events', '3 events shown']] as $i => $s)
                            <li class="flex items-center gap-3 p-3 rounded-lg border border-[rgb(var(--border))] bg-white hover:border-brand-secondary">
                                <span class="text-ink-muted cursor-grab" title="Drag to reorder">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><circle cx="9" cy="6" r="1.4"/><circle cx="15" cy="6" r="1.4"/><circle cx="9" cy="12" r="1.4"/><circle cx="15" cy="12" r="1.4"/><circle cx="9" cy="18" r="1.4"/><circle cx="15" cy="18" r="1.4"/></svg>
                                </span>
                                <div class="flex-1">
                                    <div class="text-sm font-medium">{{ $s[0] }}</div>
                                    <div class="text-xs text-ink-muted">{{ $s[1] }}</div>
                                </div>
                                <button class="text-xs text-brand-primary hover:underline">Edit</button>
                                <button class="text-xs text-ink-muted hover:text-brand-primary">Remove</button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <aside class="space-y-5">
                <div class="card p-5">
                    <h3 class="text-sm font-medium mb-3">Page status</h3>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Published
                        </span>
                        <span class="text-xs text-ink-muted">since Mar 02, 2026</span>
                    </div>
                </div>
                <div class="card p-5 text-sm space-y-2">
                    <h3 class="font-medium mb-2">Author info</h3>
                    <div class="flex items-baseline justify-between"><span class="text-ink-muted">Created</span><span>Feb 14, 2026</span></div>
                    <div class="flex items-baseline justify-between"><span class="text-ink-muted">Last edit</span><span>2 min ago</span></div>
                    <div class="flex items-baseline justify-between"><span class="text-ink-muted">Revisions</span><span>14</span></div>
                </div>
                <div class="card p-5 text-sm">
                    <h3 class="font-medium mb-2">Live URL</h3>
                    <a href="#" class="text-brand-primary hover:underline break-all">https://grace.local/</a>
                </div>
            </aside>
        </div>

        {{-- SEO tab --}}
        <div x-show="tab==='seo'" x-cloak class="card p-5 max-w-3xl space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1.5">Meta title</label>
                <input type="text" class="input" value="Grace Community Church | A place to belong">
                <p class="text-xs text-ink-muted mt-1">60 / 60 characters</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Meta description</label>
                <textarea rows="3" class="input">A welcoming church in Springfield. Sunday services at 9 and 11 AM. Plan your visit, listen to sermons, or get involved.</textarea>
                <p class="text-xs text-ink-muted mt-1">140 / 160 characters</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Social share image (Open Graph)</label>
                <div class="flex items-center gap-3">
                    <div class="w-32 h-16 rounded-md bg-gradient-to-br from-brand-primary/30 to-brand-secondary/40 border border-[rgb(var(--border))]"></div>
                    <button class="btn-ghost text-xs">Choose image</button>
                </div>
                <p class="text-xs text-ink-muted mt-1">1200 × 630 recommended.</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Canonical URL</label>
                <input type="text" class="input" value="https://grace.local/">
            </div>
        </div>

        {{-- Settings tab --}}
        <div x-show="tab==='settings'" x-cloak class="card p-5 max-w-3xl space-y-5">
            <label class="flex items-start gap-3">
                <input type="checkbox" class="mt-0.5 rounded border-[rgb(var(--border))] text-brand-primary focus:ring-brand-primary" checked>
                <span>
                    <span class="block text-sm font-medium">Published</span>
                    <span class="block text-xs text-ink-muted">When unchecked, the page returns a 404 to visitors.</span>
                </span>
            </label>
            <div>
                <label class="block text-sm font-medium mb-1.5">Scheduled publish date</label>
                <input type="datetime-local" class="input max-w-xs" value="2026-03-02T08:00">
                <p class="text-xs text-ink-muted mt-1">Leave blank to publish immediately when toggled on.</p>
            </div>
            <div class="border-t border-[rgb(var(--border))] pt-5">
                <h3 class="text-sm font-medium mb-2">Revision history</h3>
                <p class="text-xs text-ink-muted mb-3">14 revisions saved.</p>
                <button class="btn-ghost text-xs">View revisions</button>
            </div>
        </div>
    </div>
</x-admin.layout>
