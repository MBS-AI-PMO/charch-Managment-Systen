<x-admin.layout title="Speakers">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <div class="text-xs text-ink-muted mb-1">
                <a href="{{ route('admin.sermons.index') }}" class="hover:underline">Sermons</a>
                <span class="mx-1">/</span>
                <span>Speakers</span>
            </div>
            <h1 class="text-2xl font-serif">Speakers</h1>
            <p class="text-sm text-ink-muted mt-1">Pastors, teachers, and guest preachers.</p>
        </div>
        @can('manage-sermons')
            <a href="{{ route('admin.sermons.speakers.create') }}" class="btn-primary text-sm">+ New speaker</a>
        @endcan
    </div>

    <div class="card overflow-hidden">
        <div class="px-5 py-3 border-b border-[rgb(var(--border))] text-xs text-ink-muted">{{ $speakers->total() }} speakers</div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="bg-surface text-ink-muted text-xs uppercase tracking-wider">
                <tr>
                    <th class="text-left font-medium px-5 py-3">Name</th>
                    <th class="text-left font-medium px-5 py-3">Role</th>
                    <th class="text-left font-medium px-5 py-3">Sermons</th>
                    <th class="text-right font-medium px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[rgb(var(--border))]">
                @forelse($speakers as $sp)
                    <tr class="hover:bg-surface/60">
                        <td class="px-5 py-3 font-medium flex items-center gap-3">
                            @if($sp->photo_path)
                                <img src="{{ asset('storage/'.$sp->photo_path) }}" class="w-9 h-9 rounded-full object-cover" alt="">
                            @else
                                <span class="w-9 h-9 rounded-full bg-brand-secondary/20 text-[#8a6e2c] flex items-center justify-center text-xs font-semibold">{{ collect(explode(' ', $sp->name))->map(fn($w) => $w[0] ?? '')->take(2)->join('') }}</span>
                            @endif
                            <span>{{ $sp->name }}</span>
                        </td>
                        <td class="px-5 py-3 text-ink-muted">{{ $sp->role ?? '—' }}</td>
                        <td class="px-5 py-3 text-ink-muted">{{ $sp->sermons_count ?? 0 }}</td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.sermons.speakers.edit', $sp) }}" class="text-xs text-brand-primary hover:underline">Edit</a>
                            @can('manage-sermons')
                                <form method="POST" action="{{ route('admin.sermons.speakers.destroy', $sp) }}" class="inline ml-3" onsubmit="return confirm('Delete this speaker?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-red-600 hover:underline">Delete</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-10 text-center text-sm text-ink-muted">No speakers yet.</td></tr>
                @endforelse
            </tbody>
        </table></div>
        @if($speakers->hasPages())
            <div class="px-5 py-3 border-t border-[rgb(var(--border))]">{{ $speakers->links() }}</div>
        @endif
    </div>
</x-admin.layout>
