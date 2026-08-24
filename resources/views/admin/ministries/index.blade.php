<x-admin.layout title="Ministries">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-serif">Ministries</h1>
            <p class="text-sm text-ink-muted mt-1">Children's, youth, worship, outreach &mdash; group activities your church offers.</p>
        </div>
        @can('manage-ministries')
            <a href="{{ route('admin.ministries.create') }}" class="btn-primary text-sm">+ New ministry</a>
        @endcan
    </div>

    <div class="card overflow-hidden">
        <div class="px-5 py-3 border-b border-[rgb(var(--border))] text-xs text-ink-muted">{{ $ministries->total() }} ministries &middot; ordered by sort_order</div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="bg-surface text-ink-muted text-xs uppercase tracking-wider">
                <tr>
                    <th class="text-left font-medium px-5 py-3 w-16">Order</th>
                    <th class="text-left font-medium px-5 py-3">Ministry</th>
                    <th class="text-left font-medium px-5 py-3">Leader</th>
                    <th class="text-left font-medium px-5 py-3">Status</th>
                    <th class="text-right font-medium px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[rgb(var(--border))]">
                @forelse($ministries as $m)
                    <tr class="hover:bg-surface/60">
                        <td class="px-5 py-3 text-ink-muted text-xs">{{ $m->sort_order ?? 0 }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                @if($m->cover_image_path)
                                    <img src="{{ asset('storage/'.$m->cover_image_path) }}" class="w-10 h-10 rounded-md object-cover" alt="">
                                @else
                                    <span class="w-10 h-10 rounded-md bg-gradient-to-br from-brand-primary/20 to-brand-secondary/30"></span>
                                @endif
                                <div>
                                    <div class="font-medium">{{ $m->name }}</div>
                                    <div class="text-xs text-ink-muted">{{ Str::limit($m->summary, 60) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-ink-muted">{{ $m->leader_name ?? '—' }}</td>
                        <td class="px-5 py-3">
                            @if($m->is_published)
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Published</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Draft</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="row-actions">
                                <x-row-action type="edit" href="{{ route('admin.ministries.edit', $m) }}" />
                                @can('manage-ministries')
                                    <form method="POST" action="{{ route('admin.ministries.destroy', $m) }}" onsubmit="return confirm('Delete this ministry?')">
                                        @csrf @method('DELETE')
                                        <x-row-action type="delete" />
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-sm text-ink-muted">No ministries yet.</td></tr>
                @endforelse
            </tbody>
        </table></div>
        @if($ministries->hasPages())
            <div class="px-5 py-3 border-t border-[rgb(var(--border))]">{{ $ministries->links() }}</div>
        @endif
    </div>
</x-admin.layout>
