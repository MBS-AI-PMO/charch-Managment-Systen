<x-admin.layout title="Events">
    <div class="space-y-6">
        <div class="flex items-end justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-2xl font-serif">Events</h1>
                <p class="text-sm text-ink-muted mt-1">Plan and publish gatherings, services, and community moments.</p>
            </div>
            @can('manage-events')
                <a href="{{ route('admin.events.create') }}" class="btn-primary text-sm">+ New event</a>
            @endcan
        </div>

        <div class="border-b border-[rgb(var(--border))]">
            <nav class="flex gap-1">
                <a href="{{ route('admin.events.index', ['tab' => 'upcoming']) }}" class="px-4 py-3 text-sm border-b-2 font-medium transition {{ $tab === 'upcoming' ? 'border-brand-primary text-ink' : 'border-transparent text-ink-muted hover:text-ink' }}">
                    Upcoming <span class="ml-1 text-xs text-ink-muted">({{ $upcomingCount }})</span>
                </a>
                <a href="{{ route('admin.events.index', ['tab' => 'past']) }}" class="px-4 py-3 text-sm border-b-2 font-medium transition {{ $tab === 'past' ? 'border-brand-primary text-ink' : 'border-transparent text-ink-muted hover:text-ink' }}">
                    Past <span class="ml-1 text-xs text-ink-muted">({{ $pastCount }})</span>
                </a>
            </nav>
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto"><table class="w-full text-sm">
                <thead class="bg-surface text-ink-muted text-xs uppercase tracking-wider">
                    <tr>
                        <th class="text-left font-medium px-5 py-3 w-20">Date</th>
                        <th class="text-left font-medium px-5 py-3">Event</th>
                        <th class="text-left font-medium px-5 py-3">Location</th>
                        <th class="text-left font-medium px-5 py-3">Time</th>
                        <th class="text-left font-medium px-5 py-3">Status</th>
                        <th class="text-right font-medium px-5 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[rgb(var(--border))]">
                    @forelse($events as $event)
                        <tr class="hover:bg-surface/60">
                            <td class="px-5 py-3">
                                <div class="w-12 h-14 rounded-lg border border-[rgb(var(--border))] overflow-hidden text-center">
                                    <div class="bg-brand-primary text-white text-[10px] uppercase tracking-wider py-0.5">{{ $event->starts_at?->format('M') }}</div>
                                    <div class="text-lg font-serif text-ink leading-tight pt-1">{{ $event->starts_at?->format('d') }}</div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    @if($event->cover_image_path)
                                        <img src="{{ asset('storage/'.$event->cover_image_path) }}" class="w-10 h-10 rounded-md object-cover" alt="">
                                    @endif
                                    <span class="font-medium">{{ $event->title }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-ink-muted">{{ $event->location ?? '—' }}</td>
                            <td class="px-5 py-3 text-ink-muted">{{ $event->starts_at?->format('g:i A') }}</td>
                            <td class="px-5 py-3">
                                @if($tab === 'past')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 text-gray-600 border border-gray-200">Completed</span>
                                @elseif($event->is_published)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Published</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Draft</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('admin.events.edit', $event) }}" class="text-xs text-brand-primary hover:underline">Edit</a>
                                @can('manage-events')
                                    <form method="POST" action="{{ route('admin.events.destroy', $event) }}" class="inline ml-3" onsubmit="return confirm('Delete this event?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-600 hover:underline">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-ink-muted">No {{ $tab }} events.</td></tr>
                    @endforelse
                </tbody>
            </table></div>
            @if($events->hasPages())
                <div class="px-5 py-3 border-t border-[rgb(var(--border))]">{{ $events->links() }}</div>
            @endif
        </div>
    </div>
</x-admin.layout>
