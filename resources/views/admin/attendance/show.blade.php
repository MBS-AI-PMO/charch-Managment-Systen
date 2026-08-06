<x-admin.layout :title="'Attendance · '.$event->title">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <a href="{{ route('admin.events.edit', $event) }}" class="text-sm text-brand-primary">← Back to event</a>
            <h1 class="font-serif text-2xl mt-1">{{ $event->title }} — Attendance</h1>
            <p class="text-sm text-ink-muted">{{ $event->starts_at?->format('D, M j · g:i A') }}</p>
        </div>
        <a href="{{ route('admin.events.attendance.export', $event) }}" class="btn-secondary text-sm">Export CSV</a>
    </div>

    <div class="grid md:grid-cols-3 gap-4 mb-6">
        <div class="card p-4">
            <p class="text-xs uppercase text-ink-muted">Check-in code</p>
            <p class="font-mono text-3xl font-semibold mt-1">{{ $event->checkin_code ?: '—' }}</p>
            <form method="POST" action="{{ route('admin.events.checkin-code.regenerate', $event) }}" class="mt-2">
                @csrf
                <button class="text-xs text-brand-primary underline">Regenerate</button>
            </form>
        </div>

        <div class="card p-4">
            <p class="text-xs uppercase text-ink-muted">Check-in window</p>
            <form method="POST" action="{{ route('admin.events.attendance.toggle', $event) }}" class="mt-2 flex items-center gap-3">
                @csrf @method('PUT')
                <input type="hidden" name="open" value="{{ $event->attendance_open ? 0 : 1 }}">
                <button class="btn-{{ $event->attendance_open ? 'secondary' : 'primary' }} text-sm">
                    {{ $event->attendance_open ? 'Close check-in' : 'Open check-in' }}
                </button>
                <span class="text-sm {{ $event->attendance_open ? 'text-green-700' : 'text-ink-muted' }}">
                    {{ $event->attendance_open ? '● Open' : '○ Closed' }}
                </span>
            </form>
        </div>

        <div class="card p-4">
            <p class="text-xs uppercase text-ink-muted">Counts</p>
            <p class="text-sm mt-2">
                <strong>{{ $rsvpCount }}</strong> RSVPs ·
                <strong>{{ $checkedInCount }}</strong> checked in ·
                <strong>{{ $walkInCount }}</strong> walk-ins
            </p>
        </div>
    </div>

    <div class="card p-4" x-data="{ openModal: false }">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-serif text-lg">Roster</h2>
            <button @click="openModal = true" class="btn-secondary text-sm">+ Add walk-in</button>
        </div>

        <div class="overflow-x-auto -mx-1 px-1">
        <table class="w-full text-sm min-w-[32rem]">
            <thead class="text-left text-ink-muted border-b border-[rgb(var(--border))]">
                <tr>
                    <th class="py-2 w-10">✓</th>
                    <th class="py-2">Name</th>
                    <th class="py-2">+Gst</th>
                    <th class="py-2">RSVP'd</th>
                    <th class="py-2">Method</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $attByUser = $attendances->whereNotNull('user_id')->keyBy('user_id');
                    $walkIns   = $attendances->whereNull('user_id');
                @endphp

                @foreach($rsvps as $r)
                    @php $att = $attByUser->get($r->user_id); @endphp
                    <tr class="border-b border-[rgb(var(--border))]">
                        <td class="py-2">
                            @if($att)
                                <form method="POST" action="{{ route('admin.events.attendance.destroy', [$event, $att]) }}">
                                    @csrf @method('DELETE')
                                    <button class="text-green-700">✓</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.events.attendance.store', $event) }}">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $r->user_id }}">
                                    <button class="text-ink-muted">☐</button>
                                </form>
                            @endif
                        </td>
                        <td class="py-2">{{ $r->user->name ?? '—' }}</td>
                        <td class="py-2">{{ $r->guest_count }}</td>
                        <td class="py-2">{{ $r->created_at->diffForHumans() }}</td>
                        <td class="py-2">{{ $att?->method ?? '—' }}</td>
                    </tr>
                @endforeach

                @foreach($walkIns as $w)
                    <tr class="border-b border-[rgb(var(--border))]">
                        <td class="py-2 text-green-700">✓</td>
                        <td class="py-2">{{ $w->guest_name ?: 'Guest' }} <span class="text-xs text-ink-muted">(walk-in)</span></td>
                        <td class="py-2">{{ $w->guest_count }}</td>
                        <td class="py-2">—</td>
                        <td class="py-2">
                            <form method="POST" action="{{ route('admin.events.attendance.destroy', [$event, $w]) }}" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-600 underline">remove</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>

        {{-- Walk-in modal --}}
        <div x-show="openModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" @click="openModal = false"></div>
            <div class="relative bg-white rounded-xl p-6 w-full max-w-md">
                <h3 class="font-serif text-lg mb-4">Add walk-in</h3>
                <form method="POST" action="{{ route('admin.events.attendance.store', $event) }}" class="flex flex-col gap-3">
                    @csrf
                    <label class="text-sm">
                        Name <span class="text-ink-muted">(optional)</span>
                        <input type="text" name="guest_name" class="border rounded p-2 w-full mt-1" placeholder="Guest">
                    </label>
                    <label class="text-sm">
                        Additional guests
                        <select name="guest_count" class="border rounded p-2 w-full mt-1">
                            @for($i = 0; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </label>
                    <div class="flex gap-2 justify-end mt-2">
                        <button type="button" @click="openModal = false" class="text-sm">Cancel</button>
                        <button class="btn-primary text-sm">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin.layout>
