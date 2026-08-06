@php
    $isNew = !$user->exists;
    $action = $isNew ? route('admin.users.store') : route('admin.users.update', $user);
    $userRoles = $user->exists ? $user->roles->pluck('name')->all() : [];
@endphp
<x-admin.layout title="{{ $isNew ? 'New user' : 'Edit user' }}">
    <form method="POST" action="{{ $action }}" class="space-y-6 max-w-3xl">
        @csrf
        @unless($isNew) @method('PUT') @endunless

        <div>
            <div class="text-xs text-ink-muted mb-1">
                <a href="{{ route('admin.users.index') }}" class="hover:underline">Users</a>
                <span class="mx-1">/</span>
                <span>{{ $isNew ? 'New' : $user->name }}</span>
            </div>
            <h1 class="text-2xl font-serif">{{ $isNew ? 'New user' : $user->name }}</h1>
            @if(!$isNew && $user->id === 1)
                <p class="text-xs text-amber-700 mt-1">This is the bootstrap Site Admin. Some fields are protected.</p>
            @endif
        </div>

        <div class="card p-5 space-y-4">
            <h2 class="text-base font-serif">Identity</h2>
            <div>
                <label class="block text-sm font-medium mb-1.5">Name</label>
                <input type="text" name="name" class="input" value="{{ old('name', $user->name) }}" required>
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Email</label>
                <input type="email" name="email" class="input" value="{{ old('email', $user->email) }}" required>
                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="card p-5 space-y-4">
            <h2 class="text-base font-serif">Password</h2>
            <p class="text-xs text-ink-muted">{{ $isNew ? 'Set an initial password.' : 'Leave blank to keep current password.' }}</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ $isNew ? 'Password' : 'New password' }}</label>
                    <input type="password" name="password" class="input" autocomplete="new-password">
                    @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Confirm password</label>
                    <input type="password" name="password_confirmation" class="input" autocomplete="new-password">
                </div>
            </div>
        </div>

        <div class="card p-5 space-y-4">
            <h2 class="text-base font-serif">Roles</h2>
            <p class="text-xs text-ink-muted">Pick one or more roles that determine what this user can manage.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach($roles as $role)
                    <label class="flex items-start gap-2 p-3 rounded-lg border border-[rgb(var(--border))] hover:bg-surface/60">
                        <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                            {{ in_array($role->name, old('roles', $userRoles), true) ? 'checked' : '' }}
                            @if(!$isNew && $user->id === 1 && $role->name === 'Site Admin') disabled checked @endif
                            class="mt-0.5 rounded border-[rgb(var(--border))] text-brand-primary focus:ring-brand-primary">
                        <span class="text-sm">
                            <span class="block font-medium">{{ $role->name }}</span>
                            <span class="block text-xs text-ink-muted">{{ $role->permissions->count() }} permission(s)</span>
                        </span>
                    </label>
                @endforeach
            </div>
            @if(!$isNew && $user->id === 1)
                <input type="hidden" name="roles[]" value="Site Admin">
            @endif
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.users.index') }}" class="btn-ghost text-sm">Cancel</a>
            <button type="submit" class="btn-primary text-sm">{{ $isNew ? 'Create user' : 'Save changes' }}</button>
        </div>
    </form>
</x-admin.layout>
