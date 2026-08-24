<x-admin.layout title="Pages">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-serif">Pages</h1>
            <p class="text-sm text-ink-muted mt-1">Edit the singleton pages that make up your public site.</p>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="px-5 py-3 border-b border-[rgb(var(--border))] flex items-center justify-between gap-3 flex-wrap">
            <div class="text-xs text-ink-muted">{{ $pages->total() }} pages</div>
        </div>

        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="bg-surface text-ink-muted text-xs uppercase tracking-wider">
                <tr>
                    <th class="text-left font-medium px-5 py-3">Title</th>
                    <th class="text-left font-medium px-5 py-3">Slug</th>
                    <th class="text-left font-medium px-5 py-3">Status</th>
                    <th class="text-left font-medium px-5 py-3">Updated</th>
                    <th class="text-right font-medium px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[rgb(var(--border))]">
                @forelse($pages as $p)
                    <tr class="hover:bg-surface/60">
                        <td class="px-5 py-3 font-medium">{{ $p->title }}</td>
                        <td class="px-5 py-3 text-ink-muted font-mono text-xs">/{{ $p->slug }}</td>
                        <td class="px-5 py-3">
                            @if($p->is_published)
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Published
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-ink-muted">{{ $p->updated_at?->diffForHumans() }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="row-actions">
                                <x-row-action type="edit" href="{{ route('admin.pages.edit', $p) }}" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-sm text-ink-muted">No pages yet.</td></tr>
                @endforelse
            </tbody>
        </table></div>

        @if($pages->hasPages())
            <div class="px-5 py-3 border-t border-[rgb(var(--border))]">{{ $pages->links() }}</div>
        @endif
    </div>
</x-admin.layout>
