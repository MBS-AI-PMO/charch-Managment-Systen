<x-admin.layout title="Home hero slides">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-serif">Home hero slides</h1>
            <p class="text-sm text-ink-muted mt-1">Carousel slides on the homepage. Only active slides are shown.</p>
        </div>
        @can('manage-pages')
            <a href="{{ route('admin.hero-slides.create') }}" class="btn-primary text-sm">+ New slide</a>
        @endcan
    </div>

    <div class="card overflow-hidden">
        <div class="px-5 py-3 border-b border-[rgb(var(--border))] text-xs text-ink-muted">{{ $slides->total() }} slides</div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="bg-surface text-ink-muted text-xs uppercase tracking-wider">
                <tr>
                    <th class="text-left font-medium px-5 py-3 w-16">Pos</th>
                    <th class="text-left font-medium px-5 py-3">Slide</th>
                    <th class="text-left font-medium px-5 py-3">Status</th>
                    <th class="text-right font-medium px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[rgb(var(--border))]">
                @forelse($slides as $slide)
                    <tr class="hover:bg-surface/60">
                        <td class="px-5 py-3 text-ink-muted text-xs">{{ $slide->position }}</td>
                        <td class="px-5 py-3">
                            <div class="font-medium">{{ $slide->heading }}</div>
                            <div class="text-xs text-ink-muted">{{ Str::limit($slide->sub, 80) }}</div>
                        </td>
                        <td class="px-5 py-3">
                            @if($slide->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Hidden</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="row-actions">
                                <x-row-action type="edit" href="{{ route('admin.hero-slides.edit', $slide) }}" />
                                @can('manage-pages')
                                    <form method="POST" action="{{ route('admin.hero-slides.destroy', $slide) }}" onsubmit="return confirm('Delete this slide?')">
                                        @csrf @method('DELETE')
                                        <x-row-action type="delete" />
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-10 text-center text-sm text-ink-muted">No slides yet.</td></tr>
                @endforelse
            </tbody>
        </table></div>
        @if($slides->hasPages())
            <div class="px-5 py-3 border-t border-[rgb(var(--border))]">{{ $slides->links() }}</div>
        @endif
    </div>
</x-admin.layout>
