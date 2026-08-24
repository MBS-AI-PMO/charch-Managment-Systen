<x-admin.layout title="Users">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-serif">Users</h1>
            <p class="text-sm text-ink-muted mt-1">Admins and editors who can sign into this panel.</p>
        </div>
        @can('manage-users')
            <a href="{{ route('admin.users.create') }}" class="btn-primary text-sm">+ New user</a>
        @endcan
    </div>

    <div class="card overflow-hidden">
        <form method="GET" class="px-5 py-3 border-b border-[rgb(var(--border))] flex items-center gap-2 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}" class="input w-64" placeholder="Search name or email…">
            <select name="role" class="input w-44" onchange="this.form.submit()">
                <option value="">All roles</option>
                @foreach($roles as $r)
                    <option value="{{ $r->name }}" @selected(request('role') === $r->name)>{{ $r->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-ghost text-sm">Filter</button>
            <span class="ml-auto text-xs text-ink-muted">{{ $users->total() }} users</span>
        </form>

        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="bg-surface text-ink-muted text-xs uppercase tracking-wider">
                <tr>
                    <th class="text-left font-medium px-5 py-3">User</th>
                    <th class="text-left font-medium px-5 py-3">Email</th>
                    <th class="text-left font-medium px-5 py-3">Roles</th>
                    <th class="text-left font-medium px-5 py-3">Joined</th>
                    <th class="text-left font-medium px-5 py-3">Last login</th>
                    <th class="text-right font-medium px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[rgb(var(--border))]">
                @forelse($users as $u)
                    <tr class="hover:bg-surface/60">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-brand-secondary/20 text-[#8a6e2c] flex items-center justify-center text-xs font-semibold">{{ collect(explode(' ', $u->name))->map(fn($w) => $w[0] ?? '')->take(2)->join('') }}</span>
                                <span class="font-medium">{{ $u->name }}@if($u->id === 1)<span class="ml-2 text-[10px] uppercase tracking-wider text-ink-muted">bootstrap</span>@endif</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-ink-muted">{{ $u->email }}</td>
                        <td class="px-5 py-3">
                            @forelse($u->roles as $role)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] bg-blue-50 text-blue-700 border border-blue-200 mr-1">{{ $role->name }}</span>
                            @empty
                                <span class="text-xs text-ink-muted">—</span>
                            @endforelse
                        </td>
                        <td class="px-5 py-3 text-ink-muted">{{ $u->created_at?->format('M j, Y') }}</td>
                        <td class="px-5 py-3 text-ink-muted">{{ $u->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                        <td class="px-5 py-3 text-right">
                            @if($u->id === 1)
                                <span class="text-xs text-ink-muted">Protected</span>
                            @else
                                <div class="row-actions">
                                    <x-row-action type="edit" href="{{ route('admin.users.edit', $u) }}" />
                                    @can('manage-users')
                                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Delete this user?')">
                                            @csrf @method('DELETE')
                                            <x-row-action type="delete" />
                                        </form>
                                    @endcan
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-ink-muted">No users match.</td></tr>
                @endforelse
            </tbody>
        </table></div>

        @if($users->hasPages())
            <div class="px-5 py-3 border-t border-[rgb(var(--border))]">{{ $users->links() }}</div>
        @endif
    </div>
</x-admin.layout>
