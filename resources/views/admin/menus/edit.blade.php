<x-admin.layout title="Edit menu: {{ $menu->name }}">
    @php
        // Group items into a (root => children[]) tree for rendering. Items
        // already arrive sorted by sort_order via the Menu::items() relation.
        $byParent = $menu->items->groupBy(fn ($i) => $i->parent_id ?? 0);
        $roots = $byParent->get(0, collect());
        $rootChoices = $rootItems ?? $menu->items->whereNull('parent_id')->values();
    @endphp

    <div class="space-y-6">
        <div class="flex items-end justify-between flex-wrap gap-3">
            <div>
                <div class="text-xs text-ink-muted mb-1">
                    <a href="{{ route('admin.menus.index') }}" class="hover:underline">Menus</a>
                    <span class="mx-1">/</span>
                    <span>{{ $menu->name }}</span>
                </div>
                <h1 class="text-2xl font-serif">{{ $menu->name }}</h1>
                <p class="text-sm text-ink-muted mt-1">slug <code class="text-xs px-1.5 py-0.5 rounded bg-surface">{{ $menu->slug }}</code></p>
            </div>
            <form method="POST" action="{{ route('admin.menus.update', $menu) }}" class="flex items-center gap-2">
                @csrf @method('PUT')
                <input type="hidden" name="name" value="{{ $menu->name }}">
                <input type="hidden" name="slug" value="{{ $menu->slug }}">
                {{-- placeholder save for menu meta if needed --}}
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 card">
                <div class="px-5 py-3 border-b border-[rgb(var(--border))] text-xs text-ink-muted flex items-center justify-between">
                    <span>{{ $menu->items->count() }} items &middot; drag to reorder or nest</span>
                    <span id="menu-reorder-status" class="text-ink-muted/70" aria-live="polite"></span>
                </div>
                @if($menu->items->isEmpty())
                    <div class="p-6 text-center text-sm text-ink-muted">No items yet. Add one using the form on the right.</div>
                @else
                    <ul id="menu-tree" class="menu-tree p-3 space-y-1.5" data-menu-id="{{ $menu->id }}" data-reorder-url="{{ route('admin.menus.reorder', $menu) }}">
                        @foreach($roots as $item)
                            @include('admin.menus._tree_item', ['item' => $item, 'byParent' => $byParent])
                        @endforeach
                    </ul>
                @endif
            </div>

            <aside class="space-y-5">
                <form method="POST" action="{{ route('admin.menus.items.store', $menu) }}" class="card p-5 space-y-4">
                    @csrf
                    <h3 class="text-sm font-medium">Add item</h3>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Label</label>
                        <input type="text" name="label" class="input" value="{{ old('label') }}" required>
                        @error('label')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Parent</label>
                        <select name="parent_id" class="input">
                            <option value="">(top level)</option>
                            @foreach($rootChoices as $root)
                                <option value="{{ $root->id }}" @selected(old('parent_id') == $root->id)>{{ $root->label }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-ink-muted">Nest under a top-level item (one level deep).</p>
                        @error('parent_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div x-data="{type: '{{ old('link_type', 'page') }}'}">
                        <label class="block text-sm font-medium mb-1.5">Link type</label>
                        <select name="link_type" x-model="type" class="input">
                            <option value="page">Page</option>
                            <option value="url">Custom URL</option>
                            <option value="route">Named route</option>
                        </select>

                        <div class="mt-3">
                            <label class="block text-sm font-medium mb-1.5">Link value</label>
                            <template x-if="type === 'page'">
                                <select name="link_value" class="input">
                                    <option value="">— Choose a page —</option>
                                    @foreach($pages as $p)
                                        <option value="{{ $p->slug }}" @selected(old('link_value') === $p->slug)>{{ $p->title }} (/{{ $p->slug }})</option>
                                    @endforeach
                                </select>
                            </template>
                            <template x-if="type === 'url'">
                                <input type="text" name="link_value" class="input font-mono text-xs" value="{{ old('link_value') }}" placeholder="https://… or /some-path">
                            </template>
                            <template x-if="type === 'route'">
                                <select name="link_value" class="input">
                                    <option value="">— Choose a route —</option>
                                    @foreach($routes as $r)
                                        <option value="{{ $r }}" @selected(old('link_value') === $r)>{{ $r }}</option>
                                    @endforeach
                                </select>
                            </template>
                        </div>
                        @error('link_value')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Target</label>
                        <select name="target" class="input">
                            <option value="_self">Same tab</option>
                            <option value="_blank">New tab</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary text-sm w-full">Add item</button>
                </form>

                <div class="card p-5 text-sm space-y-2">
                    <h3 class="font-medium mb-2">Tips</h3>
                    <ul class="text-xs text-ink-muted space-y-1.5 list-disc pl-4">
                        <li>Keep the main nav to 6 items or fewer.</li>
                        <li>Use Contact as the last item.</li>
                        <li>Drag rows to reorder. Drop onto another row's child area to nest (one level max).</li>
                    </ul>
                </div>
            </aside>
        </div>
    </div>

    @push('head')
        <style>
            .menu-tree, .menu-tree ul { list-style: none; }
            .menu-tree .menu-children { padding-left: 1.5rem; min-height: 0.5rem; }
            .menu-tree .menu-children:empty { min-height: 1.25rem; }
            .menu-tree li.menu-node > .menu-row {
                display: flex; align-items: center; gap: 0.75rem;
                padding: 0.6rem 0.75rem;
                border: 1px solid rgb(var(--border));
                background: #fff;
                border-radius: 0.5rem;
            }
            .menu-tree li.menu-node + li.menu-node { margin-top: 0.375rem; }
            .menu-tree li.menu-node ul.menu-children > li.menu-node { margin-top: 0.375rem; }
            .menu-tree .menu-handle {
                cursor: grab; user-select: none;
                color: rgb(var(--ink-muted, 100 100 100));
                font-size: 1rem; line-height: 1; padding: 0 0.25rem;
            }
            .menu-tree .menu-handle:active { cursor: grabbing; }
            .menu-tree .sortable-ghost { opacity: 0.4; }
            .menu-tree .sortable-chosen > .menu-row { box-shadow: 0 0 0 2px rgb(var(--brand-primary, 16 120 200)); }
        </style>
    @endpush

    @push('scripts')
        <script>
            (function () {
                const root = document.getElementById('menu-tree');
                if (!root || typeof window.Sortable === 'undefined') return;

                const statusEl = document.getElementById('menu-reorder-status');
                const url = root.dataset.reorderUrl;
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content
                    || document.querySelector('input[name="_token"]')?.value;

                function initSortable(ul) {
                    new window.Sortable(ul, {
                        group: 'menu',
                        animation: 150,
                        handle: '.menu-handle',
                        fallbackOnBody: true,
                        swapThreshold: 0.65,
                        invertSwap: true,
                        onEnd: persist,
                    });
                }

                // Init root + every children container (lazy-init nested ULs).
                initSortable(root);
                root.querySelectorAll('ul.menu-children').forEach(initSortable);

                function collapseDeepNesting() {
                    // Move any 2+ deep nodes back up to the root list.
                    const deep = root.querySelectorAll(
                        ':scope > li.menu-node > ul.menu-children > li.menu-node > ul.menu-children > li.menu-node'
                    );
                    deep.forEach((node) => root.appendChild(node));
                }

                function buildPayload() {
                    const tree = [];
                    // Roots first.
                    root.querySelectorAll(':scope > li.menu-node').forEach((li, idx) => {
                        tree.push({ id: parseInt(li.dataset.id, 10), parent_id: null, sort_order: idx });
                        li.querySelectorAll(':scope > ul.menu-children > li.menu-node').forEach((child, cIdx) => {
                            tree.push({
                                id: parseInt(child.dataset.id, 10),
                                parent_id: parseInt(li.dataset.id, 10),
                                sort_order: cIdx,
                            });
                        });
                    });
                    return tree;
                }

                async function persist() {
                    collapseDeepNesting();
                    const payload = buildPayload();
                    if (statusEl) statusEl.textContent = 'Saving…';
                    try {
                        const res = await fetch(url, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrf || '',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({ tree: payload }),
                        });
                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        if (statusEl) {
                            statusEl.textContent = 'Saved';
                            setTimeout(() => { statusEl.textContent = ''; }, 1500);
                        }
                    } catch (e) {
                        if (statusEl) statusEl.textContent = 'Save failed — refresh to retry';
                        console.error('menu reorder failed', e);
                    }
                }
            })();
        </script>
    @endpush
</x-admin.layout>
