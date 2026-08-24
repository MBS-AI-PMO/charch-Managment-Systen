<x-admin.layout title="Church branches">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-serif">Church branches</h1>
            <p class="text-sm text-ink-muted mt-1">Manage the campuses shown on the Our Churches page.</p>
        </div>
        @can('manage-pages')
            <a href="{{ route('admin.churches.create') }}" class="btn-primary text-sm">+ New branch</a>
        @endcan
    </div>

    <div class="card overflow-hidden">
        <div class="px-5 py-3 border-b border-[rgb(var(--border))] text-xs text-ink-muted">{{ $branches->total() }} branches</div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="bg-surface text-ink-muted text-xs uppercase tracking-wider">
                <tr>
                    <th class="text-left font-medium px-5 py-3 w-16">Order</th>
                    <th class="text-left font-medium px-5 py-3">Church</th>
                    <th class="text-left font-medium px-5 py-3">Role</th>
                    <th class="text-left font-medium px-5 py-3">Status</th>
                    <th class="text-right font-medium px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[rgb(var(--border))]">
                @forelse($branches as $branch)
                    <tr class="hover:bg-surface/60">
                        <td class="px-5 py-3 text-ink-muted text-xs">{{ $branch->sort_order }}</td>
                        <td class="px-5 py-3">
                            <div class="font-medium">{{ $branch->name }}</div>
                            <div class="text-xs text-ink-muted">{{ $branch->city }} · /our-churches/{{ $branch->slug }}</div>
                        </td>
                        <td class="px-5 py-3 text-ink-muted">{{ $branch->role ?? '—' }}</td>
                        <td class="px-5 py-3">
                            @if($branch->is_published)
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Published</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Draft</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="row-actions">
                                <x-row-action type="view" href="{{ route('site.churches.show', $branch) }}" target="_blank" />
                                <x-row-action type="edit" href="{{ route('admin.churches.edit', $branch) }}" />
                                @can('manage-pages')
                                    <form method="POST" action="{{ route('admin.churches.destroy', $branch) }}" onsubmit="return confirm('Delete this branch?')">
                                        @csrf @method('DELETE')
                                        <x-row-action type="delete" />
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-sm text-ink-muted">No branches yet.</td></tr>
                @endforelse
            </tbody>
        </table></div>
        @if($branches->hasPages())
            <div class="px-5 py-3 border-t border-[rgb(var(--border))]">{{ $branches->links() }}</div>
        @endif
    </div>
</x-admin.layout>
