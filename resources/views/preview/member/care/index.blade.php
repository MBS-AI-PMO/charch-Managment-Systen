<x-member.layout title="Knock for help">
    <div class="max-w-container mx-auto px-4 py-10 space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div class="max-w-2xl">
                <h1 class="font-serif text-3xl md:text-4xl">Knock for help</h1>
                <p class="text-ink-muted mt-2 text-sm">Need pastoral care, practical support, or just someone to talk to? Reach out &mdash; we'll be in touch within 24 hours.</p>
            </div>
            <a href="{{ route('preview.member.care.create') }}" class="btn-primary text-sm">+ New request</a>
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-surface text-ink-muted">
                        <tr>
                            <th class="text-left font-medium px-4 py-3">Category</th>
                            <th class="text-left font-medium px-4 py-3">Message</th>
                            <th class="text-left font-medium px-4 py-3">Status</th>
                            <th class="text-left font-medium px-4 py-3">Submitted</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[rgb(var(--border))]">
                        @foreach([
                            ['Illness',   'rose',    'Recovery from surgery has been slow...',           'responding','open',   '3 days ago'],
                            ['Financial', 'amber',   'Unexpected bill came in this week...',             'open',      'gray',   '5 days ago'],
                            ['Grief',     'indigo',  'Lost my grandmother last month, struggling...',    'closed',    'emerald','3 weeks ago'],
                        ] as $r)
                            <tr>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-{{ $r[1] }}-50 text-{{ $r[1] }}-700">{{ $r[0] }}</span>
                                </td>
                                <td class="px-4 py-3 text-ink-muted max-w-md truncate">{{ $r[2] }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-{{ $r[4] }}-50 text-{{ $r[4] }}-700 capitalize">{{ $r[3] }}</span>
                                </td>
                                <td class="px-4 py-3 text-ink-muted whitespace-nowrap">{{ $r[5] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-member.layout>
