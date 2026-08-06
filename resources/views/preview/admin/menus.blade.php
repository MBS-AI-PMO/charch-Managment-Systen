@php
$items = [
    ['label'=>'About',     'url'=>'/about',     'depth'=>0, 'children'=>[
        ['label'=>'Our beliefs',   'url'=>'/beliefs',     'depth'=>1],
        ['label'=>'Our team',      'url'=>'/team',        'depth'=>1],
        ['label'=>'Plan a visit',  'url'=>'/plan-visit',  'depth'=>1],
    ]],
    ['label'=>'Sermons',   'url'=>'/sermons',   'depth'=>0, 'children'=>[]],
    ['label'=>'Events',    'url'=>'/events',    'depth'=>0, 'children'=>[]],
    ['label'=>'Ministries','url'=>'/ministries','depth'=>0, 'children'=>[
        ['label'=>"Children's", 'url'=>'/ministries/children',     'depth'=>1],
        ['label'=>'Youth',      'url'=>'/ministries/youth',        'depth'=>1],
        ['label'=>'Worship',    'url'=>'/ministries/worship',      'depth'=>1],
    ]],
    ['label'=>'Blog',      'url'=>'/blog',      'depth'=>0, 'children'=>[]],
    ['label'=>'Contact',   'url'=>'/contact',   'depth'=>0, 'children'=>[]],
];
@endphp

<x-admin.layout title="Menus">
    <div x-data="{add:false, menu:'main'}" class="space-y-6">
        <div class="flex items-end justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-2xl font-serif">Menus</h1>
                <p class="text-sm text-ink-muted mt-1">Configure the navigation that appears in your header and footer.</p>
            </div>
            <div class="flex items-center gap-2">
                <select x-model="menu" class="input text-sm w-44">
                    <option value="main">Main navigation</option>
                    <option value="footer">Footer navigation</option>
                </select>
                <button @click="add=true" class="btn-primary text-sm">+ Add item</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 card">
                <div class="px-5 py-3 border-b border-[rgb(var(--border))] text-xs text-ink-muted">
                    Drag items to reorder. Indent (or drop onto a parent) to create a sub-item.
                </div>
                <ul class="p-3 space-y-1.5">
                    @foreach($items as $item)
                        <li>
                            <div class="flex items-center gap-3 p-3 rounded-lg border border-[rgb(var(--border))] bg-white hover:border-brand-secondary">
                                <span class="text-ink-muted cursor-grab"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><circle cx="9" cy="6" r="1.4"/><circle cx="15" cy="6" r="1.4"/><circle cx="9" cy="12" r="1.4"/><circle cx="15" cy="12" r="1.4"/><circle cx="9" cy="18" r="1.4"/><circle cx="15" cy="18" r="1.4"/></svg></span>
                                <div class="flex-1">
                                    <div class="text-sm font-medium">{{ $item['label'] }}</div>
                                    <div class="text-xs text-ink-muted font-mono">{{ $item['url'] }}</div>
                                </div>
                                <span class="text-[11px] text-ink-muted">Page</span>
                                <a href="#" class="text-xs text-brand-primary hover:underline">Edit</a>
                                <a href="#" class="text-xs text-ink-muted hover:text-brand-primary">Remove</a>
                            </div>
                            @if(!empty($item['children']))
                                <ul class="ml-8 mt-1.5 space-y-1.5 border-l-2 border-[rgb(var(--border))] pl-4">
                                    @foreach($item['children'] as $child)
                                        <li class="flex items-center gap-3 p-3 rounded-lg border border-dashed border-[rgb(var(--border))] bg-surface/40">
                                            <span class="text-ink-muted cursor-grab"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><circle cx="9" cy="6" r="1.4"/><circle cx="15" cy="6" r="1.4"/><circle cx="9" cy="12" r="1.4"/><circle cx="15" cy="12" r="1.4"/><circle cx="9" cy="18" r="1.4"/><circle cx="15" cy="18" r="1.4"/></svg></span>
                                            <div class="flex-1">
                                                <div class="text-sm">{{ $child['label'] }}</div>
                                                <div class="text-xs text-ink-muted font-mono">{{ $child['url'] }}</div>
                                            </div>
                                            <a href="#" class="text-xs text-brand-primary hover:underline">Edit</a>
                                            <a href="#" class="text-xs text-ink-muted hover:text-brand-primary">Remove</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>

            <aside class="card p-5 text-sm space-y-3 h-fit">
                <h2 class="text-base font-serif">Tips</h2>
                <p class="text-ink-muted text-xs">Menus reflect immediately on your public site after saving.</p>
                <ul class="text-xs text-ink-muted space-y-2 list-disc pl-4">
                    <li>Keep the main nav to 6 items or fewer.</li>
                    <li>Use Contact as the last item.</li>
                    <li>Sub-menus appear on hover/tap.</li>
                </ul>
            </aside>
        </div>

        {{-- Add item modal placeholder --}}
        <div x-show="add" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
            <div @click.away="add=false" class="card max-w-md w-full p-6">
                <h2 class="text-lg font-serif mb-4">Add menu item</h2>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Label</label>
                        <input type="text" class="input" placeholder="e.g. About us">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Link to</label>
                        <select class="input">
                            <option>Page — Home</option>
                            <option>Page — About</option>
                            <option>Custom URL…</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-5">
                    <button @click="add=false" class="btn-ghost text-sm">Cancel</button>
                    <button @click="add=false" class="btn-primary text-sm">Add</button>
                </div>
            </div>
        </div>
    </div>
</x-admin.layout>
