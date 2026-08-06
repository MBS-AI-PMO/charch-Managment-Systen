<x-admin.layout title="Attendance">
    <h1 class="font-serif text-2xl mb-6">Attendance</h1>

    <div class="card p-4 overflow-x-auto">
        <table class="w-full text-sm min-w-[36rem]">
            <thead class="text-left text-ink-muted border-b border-[rgb(var(--border))]">
                <tr>
                    <th class="py-2">Event</th>
                    <th class="py-2">Date</th>
                    <th class="py-2">Status</th>
                    <th class="py-2">Code</th>
                    <th class="py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $e)
                    <tr class="border-b border-[rgb(var(--border))]">
                        <td class="py-2">{{ $e->title }}</td>
                        <td class="py-2">{{ $e->starts_at?->format('D, M j · g:i A') }}</td>
                        <td class="py-2">
                            <span class="{{ $e->attendance_open ? 'text-green-700' : 'text-ink-muted' }}">
                                {{ $e->attendance_open ? '● Open' : '○ Closed' }}
                            </span>
                        </td>
                        <td class="py-2 font-mono">{{ $e->checkin_code ?: '—' }}</td>
                        <td class="py-2 text-right">
                            <a href="{{ route('admin.events.attendance', $e) }}" class="text-brand-primary text-sm">Manage →</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-4 text-ink-muted">No events found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $events->links() }}</div>
</x-admin.layout>
