@php
    $isNew = !$branch->exists;
    $action = $isNew ? route('admin.churches.store') : route('admin.churches.update', $branch);
@endphp
<x-admin.layout title="{{ $isNew ? 'New church branch' : 'Edit church branch' }}">
    <form method="POST" action="{{ $action }}" class="space-y-6">
        @csrf
        @unless($isNew) @method('PUT') @endunless

        <div class="flex items-end justify-between flex-wrap gap-3">
            <div>
                <div class="text-xs text-ink-muted mb-1">
                    <a href="{{ route('admin.churches.index') }}" class="hover:underline">Church branches</a>
                    <span class="mx-1">/</span>
                    <span>{{ $isNew ? 'New' : $branch->name }}</span>
                </div>
                <h1 class="text-2xl font-serif">{{ $isNew ? 'New church branch' : 'Edit church branch' }}</h1>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.churches.index') }}" class="btn-ghost text-sm">Cancel</a>
                <button type="submit" name="is_published" value="0" class="btn-ghost text-sm">Save Draft</button>
                <button type="submit" name="is_published" value="1" class="btn-primary text-sm">Save &amp; Publish</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-5">
                <div class="card p-5 space-y-4">
                    <h2 class="text-base font-serif">Basics</h2>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Name</label>
                        <input type="text" name="name" class="input text-lg" value="{{ old('name', $branch->name) }}" required>
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Slug</label>
                            <input type="text" name="slug" class="input font-mono text-sm" value="{{ old('slug', $branch->slug) }}" placeholder="auto-generated">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Role label</label>
                            <input type="text" name="role" class="input" value="{{ old('role', $branch->role) }}" placeholder="Main campus">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5">City</label>
                            <input type="text" name="city" class="input" value="{{ old('city', $branch->city) }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Pastor</label>
                            <input type="text" name="pastor" class="input" value="{{ old('pastor', $branch->pastor) }}">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Card summary</label>
                        <textarea name="note" rows="2" class="input">{{ old('note', $branch->note) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Hero subtitle</label>
                        <input type="text" name="hero_sub" class="input" value="{{ old('hero_sub', $branch->hero_sub) }}">
                    </div>
                </div>

                <div class="card p-5 space-y-4">
                    <h2 class="text-base font-serif">About this church</h2>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">About paragraphs</label>
                        <textarea name="about" rows="8" class="input" placeholder="Separate paragraphs with a blank line">{{ old('about', $branch->about) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">What to expect on Sunday (one item per line)</label>
                        <textarea name="expect" rows="5" class="input">{{ old('expect', $branch->expect) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Ministries (one item per line)</label>
                        <textarea name="ministries" rows="5" class="input">{{ old('ministries', $branch->ministries) }}</textarea>
                    </div>
                </div>

                <div class="card p-5 space-y-4">
                    <h2 class="text-base font-serif">Visit information</h2>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Families</label>
                        <textarea name="families" rows="3" class="input">{{ old('families', $branch->families) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">First visit</label>
                        <textarea name="visit" rows="3" class="input">{{ old('visit', $branch->visit) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Getting here</label>
                        <textarea name="getting_here" rows="3" class="input">{{ old('getting_here', $branch->getting_here) }}</textarea>
                    </div>
                </div>
            </div>

            <aside class="space-y-5">
                <div class="card p-5 space-y-4">
                    <h3 class="text-sm font-medium">Contact &amp; services</h3>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Address</label>
                        <textarea name="address" rows="2" class="input">{{ old('address', $branch->address) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Service times</label>
                        <textarea name="services" rows="4" class="input">{{ old('services', $branch->services) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Language</label>
                        <input type="text" name="language" class="input" value="{{ old('language', $branch->language) }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Phone override</label>
                        <input type="text" name="phone" class="input" value="{{ old('phone', $branch->phone) }}" placeholder="Leave blank to use site contact phone">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Email override</label>
                        <input type="email" name="email" class="input" value="{{ old('email', $branch->email) }}" placeholder="Leave blank to use site contact email">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Sort order</label>
                        <input type="number" name="sort_order" min="0" class="input" value="{{ old('sort_order', $branch->sort_order ?? 0) }}">
                    </div>
                </div>
            </aside>
        </div>
    </form>
</x-admin.layout>
