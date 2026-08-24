<x-admin.layout title="Sermons">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-serif">Sermons</h1>
            <p class="text-sm text-ink-muted mt-1">Manage your sermon library. <a href="{{ route('admin.sermons.series.index') }}" class="text-brand-primary hover:underline">Series</a> &middot; <a href="{{ route('admin.sermons.speakers.index') }}" class="text-brand-primary hover:underline">Speakers</a></p>
        </div>
        @can('manage-sermons')
            <a href="{{ route('admin.sermons.create') }}" class="btn-primary text-sm">+ New sermon</a>
        @endcan
    </div>

    <div class="card overflow-hidden">
        <form method="GET" class="px-5 py-3 border-b border-[rgb(var(--border))] flex items-center gap-2 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}" class="input w-64" placeholder="Search sermons…">
            <select name="series_id" class="input w-44" onchange="this.form.submit()">
                <option value="">All series</option>
                @foreach($series as $sr)
                    <option value="{{ $sr->id }}" @selected((string) request('series_id') === (string) $sr->id)>{{ $sr->name }}</option>
                @endforeach
            </select>
            <select name="speaker_id" class="input w-44" onchange="this.form.submit()">
                <option value="">All speakers</option>
                @foreach($speakers as $sp)
                    <option value="{{ $sp->id }}" @selected((string) request('speaker_id') === (string) $sp->id)>{{ $sp->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-ghost text-sm">Filter</button>
            <span class="ml-auto text-xs text-ink-muted">{{ $sermons->total() }} sermons</span>
        </form>

        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="bg-surface text-ink-muted text-xs uppercase tracking-wider">
                <tr>
                    <th class="text-left font-medium px-5 py-3">Title</th>
                    <th class="text-left font-medium px-5 py-3">Series</th>
                    <th class="text-left font-medium px-5 py-3">Speaker</th>
                    <th class="text-left font-medium px-5 py-3">Preached</th>
                    <th class="text-left font-medium px-5 py-3">Status</th>
                    <th class="text-right font-medium px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[rgb(var(--border))]">
                @forelse($sermons as $sermon)
                    <tr class="hover:bg-surface/60">
                        <td class="px-5 py-3 font-medium">{{ $sermon->title }}</td>
                        <td class="px-5 py-3 text-ink-muted">{{ $sermon->series?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-ink-muted">{{ $sermon->speaker?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-ink-muted">{{ $sermon->preached_on?->format('M j, Y') ?? '—' }}</td>
                        <td class="px-5 py-3">
                            @if($sermon->is_published)
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Published</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Draft</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="row-actions">
                                <x-row-action type="edit" href="{{ route('admin.sermons.edit', $sermon) }}" />
                                @can('manage-sermons')
                                    <form method="POST" action="{{ route('admin.sermons.destroy', $sermon) }}" onsubmit="return confirm('Delete this sermon?')">
                                        @csrf @method('DELETE')
                                        <x-row-action type="delete" />
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-ink-muted">No sermons yet.</td></tr>
                @endforelse
            </tbody>
        </table></div>
        @if($sermons->hasPages())
            <div class="px-5 py-3 border-t border-[rgb(var(--border))]">{{ $sermons->links() }}</div>
        @endif
    </div>
</x-admin.layout>
