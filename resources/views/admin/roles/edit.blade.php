@php
    $isNew = !$role->exists;
    $action = $isNew ? route('admin.roles.store') : route('admin.roles.update', $role);
    $rolePerms = $role->exists ? $role->permissions->pluck('name')->all() : [];
    $grouped = $permissions->groupBy(function ($p) {
        $parts = explode('-', $p->name, 2);
        return $parts[1] ?? 'general';
    });
    $isSiteAdmin = !$isNew && $role->name === 'Site Admin';
@endphp
<x-admin.layout title="{{ $isNew ? 'New role' : 'Edit role: '.$role->name }}">
    <form method="POST" action="{{ $action }}" class="space-y-6">
        @csrf
        @unless($isNew) @method('PUT') @endunless

        <div class="flex items-end justify-between flex-wrap gap-3">
            <div>
                <div class="text-xs text-ink-muted mb-1">
                    <a href="{{ route('admin.roles.index') }}" class="hover:underline">Roles</a>
                    <span class="mx-1">/</span>
                    <span>{{ $isNew ? 'New' : $role->name }}</span>
                </div>
                <h1 class="text-2xl font-serif">{{ $isNew ? 'New role' : 'Edit role' }}</h1>
                @if($isSiteAdmin)
                    <p class="text-xs text-amber-700 mt-1">Site Admin always holds every permission. Edits here are no-ops for that.</p>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.roles.index') }}" class="btn-ghost text-sm">Cancel</a>
                <button type="submit" class="btn-primary text-sm">{{ $isNew ? 'Create role' : 'Save changes' }}</button>
            </div>
        </div>

        <div class="card p-5 space-y-4 max-w-2xl">
            <div>
                <label class="block text-sm font-medium mb-1.5">Role name</label>
                <input type="text" name="name" class="input" value="{{ old('name', $role->name) }}" @if($isSiteAdmin) readonly @endif required>
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="card p-5 space-y-4">
            <h2 class="text-base font-serif">Permissions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach($grouped as $group => $perms)
                    <div class="border border-[rgb(var(--border))] rounded-lg p-4">
                        <h3 class="text-sm font-medium capitalize mb-3">{{ str_replace('-', ' ', $group) }}</h3>
                        <div class="space-y-2">
                            @foreach($perms as $perm)
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                        {{ in_array($perm->name, old('permissions', $rolePerms), true) ? 'checked' : '' }}
                                        @if($isSiteAdmin) disabled checked @endif
                                        class="rounded border-[rgb(var(--border))] text-brand-primary focus:ring-brand-primary">
                                    <span class="font-mono text-xs">{{ $perm->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </form>
</x-admin.layout>
