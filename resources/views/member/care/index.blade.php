@php
    $catBadge = [
        'illness'   => 'bg-rose-50 text-rose-700',
        'grief'     => 'bg-indigo-50 text-indigo-700',
        'financial' => 'bg-amber-50 text-amber-700',
        'food'      => 'bg-emerald-50 text-emerald-700',
        'other'     => 'bg-slate-100 text-slate-700',
    ];
    $statusBadge = [
        'open'       => 'bg-slate-100 text-slate-700',
        'responding' => 'bg-amber-50 text-amber-700',
        'closed'     => 'bg-emerald-50 text-emerald-700',
    ];
@endphp
<x-member.layout title="Knock for help">
    <div class="max-w-container mx-auto px-4 py-10 space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div class="max-w-2xl">
                <h1 class="font-serif text-3xl md:text-4xl">Knock for help</h1>
                <p class="text-ink-muted mt-2 text-sm">Need pastoral care, practical support, or just someone to talk to? Reach out &mdash; a pastor will be in touch within 24 hours.</p>
            </div>
            <a href="{{ route('member.care.create') }}" class="btn-primary text-sm">+ New request</a>
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
                            <th class="text-right font-medium px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[rgb(var(--border))]">
                        @forelse($items as $r)
                            @php
                                $cb = $catBadge[$r->category] ?? $catBadge['other'];
                                $sb = $statusBadge[$r->status] ?? $statusBadge['open'];
                            @endphp
                            <tr>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs {{ $cb }} capitalize">{{ $r->category }}</span>
                                </td>
                                <td class="px-4 py-3 text-ink-muted max-w-md truncate">{{ \Illuminate\Support\Str::limit($r->message, 80) }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs {{ $sb }} capitalize">{{ $r->status }}</span>
                                </td>
                                <td class="px-4 py-3 text-ink-muted whitespace-nowrap">{{ $r->created_at?->diffForHumans() }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('member.care.show', $r) }}" class="text-xs text-brand-primary hover:underline">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-sm text-ink-muted italic">
                                    You haven't knocked yet &mdash; submit your first care request.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-member.layout>
