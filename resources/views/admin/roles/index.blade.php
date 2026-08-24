<x-admin.layout title="Roles & permissions">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-serif">Roles &amp; permissions</h1>
            <p class="text-sm text-ink-muted mt-1">Each user can hold one or more roles. Roles bundle a set of permissions.</p>
        </div>
        @can('manage-roles')
            <a href="{{ route('admin.roles.create') }}" class="btn-primary text-sm">+ New role</a>
        @endcan
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($roles as $role)
            <div class="card p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-base font-serif">{{ $role->name }}</h2>
                        <p class="text-xs text-ink-muted mt-1">{{ $role->users_count }} user{{ $role->users_count === 1 ? '' : 's' }} &middot; {{ $role->permissions->count() }} permission{{ $role->permissions->count() === 1 ? '' : 's' }}</p>
                    </div>
                    @if($role->name === 'Site Admin')
                        <span class="text-[10px] uppercase tracking-wider px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">protected</span>
                    @endif
                </div>
                <div class="mt-3 flex flex-wrap gap-1">
                    @foreach($role->permissions->take(8) as $perm)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] bg-surface text-ink-muted border border-[rgb(var(--border))]">{{ $perm->name }}</span>
                    @endforeach
                    @if($role->permissions->count() > 8)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] text-ink-muted">+{{ $role->permissions->count() - 8 }} more</span>
                    @endif
                </div>
                <div class="mt-4 flex items-center gap-2">
                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn-primary text-sm">Edit</a>
                    @if($role->name !== 'Site Admin')
                        @can('manage-roles')
                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Delete this role?')">
                                @csrf @method('DELETE')
                                <x-row-action type="delete" />
                            </form>
                        @endcan
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</x-admin.layout>
