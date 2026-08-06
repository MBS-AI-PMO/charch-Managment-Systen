<x-admin.layout title="Sermon series">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <div class="text-xs text-ink-muted mb-1">
                <a href="{{ route('admin.sermons.index') }}" class="hover:underline">Sermons</a>
                <span class="mx-1">/</span>
                <span>Series</span>
            </div>
            <h1 class="text-2xl font-serif">Series</h1>
            <p class="text-sm text-ink-muted mt-1">Group sermons by teaching arc.</p>
        </div>
        @can('manage-sermons')
            <a href="{{ route('admin.sermons.series.create') }}" class="btn-primary text-sm">+ New series</a>
        @endcan
    </div>

    <div class="card overflow-hidden">
        <div class="px-5 py-3 border-b border-[rgb(var(--border))] text-xs text-ink-muted">{{ $series->total() }} series</div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="bg-surface text-ink-muted text-xs uppercase tracking-wider">
                <tr>
                    <th class="text-left font-medium px-5 py-3">Name</th>
                    <th class="text-left font-medium px-5 py-3">Sermons</th>
                    <th class="text-right font-medium px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[rgb(var(--border))]">
                @forelse($series as $row)
                    <tr class="hover:bg-surface/60">
                        <td class="px-5 py-3 font-medium flex items-center gap-3">
                            @if($row->cover_image_path)
                                <img src="{{ asset('storage/'.$row->cover_image_path) }}" class="w-10 h-10 rounded-md object-cover" alt="">
                            @else
                                <span class="w-10 h-10 rounded-md bg-gradient-to-br from-brand-primary/20 to-brand-secondary/30"></span>
                            @endif
                            <span>{{ $row->name }}</span>
                        </td>
                        <td class="px-5 py-3 text-ink-muted">{{ $row->sermons_count ?? 0 }}</td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.sermons.series.edit', $row) }}" class="text-xs text-brand-primary hover:underline">Edit</a>
                            @can('manage-sermons')
                                <form method="POST" action="{{ route('admin.sermons.series.destroy', $row) }}" class="inline ml-3" onsubmit="return confirm('Delete this series?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-red-600 hover:underline">Delete</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-5 py-10 text-center text-sm text-ink-muted">No series yet.</td></tr>
                @endforelse
            </tbody>
        </table></div>
        @if($series->hasPages())
            <div class="px-5 py-3 border-t border-[rgb(var(--border))]">{{ $series->links() }}</div>
        @endif
    </div>
</x-admin.layout>
