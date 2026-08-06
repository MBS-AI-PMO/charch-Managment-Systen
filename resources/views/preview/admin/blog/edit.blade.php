<x-admin.layout title="Edit post">
    <div x-data="{seoOpen:false}" class="space-y-6">
        <div class="flex items-end justify-between flex-wrap gap-3">
            <div>
                <div class="text-xs text-ink-muted mb-1">
                    <a href="{{ route('admin.preview.blog') }}" class="hover:underline">Blog</a>
                    <span class="mx-1">/</span>
                    <span>New post</span>
                </div>
                <h1 class="text-2xl font-serif">Write a new post</h1>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-ink-muted hidden sm:inline">Draft saved 18 sec ago</span>
                <button class="btn-ghost text-sm">Preview</button>
                <button class="btn-ghost text-sm">Save Draft</button>
                <button class="btn-primary text-sm">Save &amp; Publish</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-5">
                <div class="card p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Title</label>
                        <input type="text" class="input text-lg" placeholder="An engaging title…" value="A season of small beginnings">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Slug</label>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-ink-muted">grace.local/blog/</span>
                            <input type="text" class="input flex-1 font-mono text-sm" value="a-season-of-small-beginnings">
                            <button class="text-xs text-brand-primary hover:underline">Regenerate</button>
                        </div>
                        <p class="text-xs text-ink-muted mt-1">Auto-generated from title. Edit only if you have a reason.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Excerpt</label>
                        <textarea rows="2" class="input">A short reflection on faithfulness in the ordinary, hidden seasons before a harvest.</textarea>
                    </div>
                </div>

                <div class="card p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-serif">Body</h2>
                        <div class="flex items-center gap-1 text-xs text-ink-muted">
                            <span class="px-1.5 py-0.5 rounded border border-[rgb(var(--border))] bg-surface">B</span>
                            <span class="px-1.5 py-0.5 rounded border border-[rgb(var(--border))] bg-surface italic">I</span>
                            <span class="px-1.5 py-0.5 rounded border border-[rgb(var(--border))] bg-surface">H₂</span>
                            <span class="px-1.5 py-0.5 rounded border border-[rgb(var(--border))] bg-surface">Link</span>
                            <span class="px-1.5 py-0.5 rounded border border-[rgb(var(--border))] bg-surface">Image</span>
                            <span class="px-1.5 py-0.5 rounded border border-[rgb(var(--border))] bg-surface">Quote</span>
                        </div>
                    </div>
                    <textarea id="body" rows="16" class="input ck-editor font-mono text-sm">In Zechariah 4:10, the prophet asks "who despises the day of small things?"…</textarea>
                </div>

                {{-- SEO collapsible --}}
                <div class="card">
                    <button @click="seoOpen=!seoOpen" class="w-full px-5 py-4 flex items-center justify-between text-left">
                        <div>
                            <h2 class="text-base font-serif">SEO</h2>
                            <p class="text-xs text-ink-muted">Override meta title, description, and Open Graph image.</p>
                        </div>
                        <svg :class="seoOpen ? 'rotate-180' : ''" class="transition-transform text-ink-muted" width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path d="M5.25 7.5 10 12.25 14.75 7.5"/></svg>
                    </button>
                    <div x-show="seoOpen" x-cloak class="px-5 pb-5 space-y-4 border-t border-[rgb(var(--border))] pt-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Meta title</label>
                            <input type="text" class="input" value="A season of small beginnings — Grace Community">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Meta description</label>
                            <textarea rows="2" class="input">A reflection on faithfulness in the hidden, ordinary moments that prepare us for what's next.</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="space-y-5">
                <div class="card p-5 space-y-4">
                    <h3 class="text-sm font-medium">Publish</h3>
                    <label class="flex items-center justify-between gap-3">
                        <span class="text-sm">Published</span>
                        <span class="relative inline-flex items-center cursor-pointer" x-data="{on:true}" @click="on=!on">
                            <input type="checkbox" class="sr-only" x-model="on">
                            <span class="w-10 h-5 rounded-full transition" :class="on ? 'bg-brand-primary' : 'bg-gray-300'"></span>
                            <span class="absolute left-0.5 top-0.5 w-4 h-4 rounded-full bg-white shadow transition" :class="on ? 'translate-x-5' : ''"></span>
                        </span>
                    </label>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Scheduled date</label>
                        <input type="datetime-local" class="input" value="2026-05-28T07:00">
                    </div>
                </div>

                <div class="card p-5 space-y-4">
                    <h3 class="text-sm font-medium">Organize</h3>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Category</label>
                        <select class="input">
                            <option>Devotional</option>
                            <option>News</option>
                            <option>Outreach</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Tags</label>
                        <input type="text" class="input" value="faith, patience, growth">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Author</label>
                        <select class="input">
                            <option>Pastor Greg</option>
                            <option>Sarah Admin</option>
                            <option>Mike Davis</option>
                        </select>
                    </div>
                </div>

                <div class="card p-5">
                    <h3 class="text-sm font-medium mb-3">Featured image</h3>
                    <div class="w-full aspect-video rounded-lg bg-gradient-to-br from-brand-primary/20 to-brand-secondary/30 border border-[rgb(var(--border))] mb-3"></div>
                    <button class="btn-ghost text-xs w-full">Choose from media library</button>
                </div>
            </aside>
        </div>
    </div>
</x-admin.layout>
