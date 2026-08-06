<x-member.layout title="My questions">
    <div class="max-w-container mx-auto px-4 py-10 space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div class="max-w-2xl">
                <h1 class="font-serif text-3xl md:text-4xl">My questions</h1>
                <p class="text-ink-muted mt-2 text-sm">Questions you submitted from the website — including replies from our team.</p>
            </div>
            @if(\Illuminate\Support\Facades\Route::has('site.home'))
                <a href="{{ route('site.home') }}" class="btn-primary shine-btn glow-primary text-sm">Ask a new question</a>
            @endif
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-surface text-ink-muted">
                        <tr>
                            <th class="text-left font-medium px-4 py-3">Subject</th>
                            <th class="text-left font-medium px-4 py-3">Your question</th>
                            <th class="text-left font-medium px-4 py-3">Status</th>
                            <th class="text-left font-medium px-4 py-3">Submitted</th>
                            <th class="text-right font-medium px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[rgb(var(--border))]">
                        @forelse($items as $q)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $q->subject }}</td>
                                <td class="px-4 py-3 text-ink-muted max-w-md truncate">{{ \Illuminate\Support\Str::limit($q->message, 80) }}</td>
                                <td class="px-4 py-3">
                                    @if($q->replied_at)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-emerald-50 text-emerald-700">Replied</span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-amber-50 text-amber-700">Awaiting reply</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-ink-muted whitespace-nowrap">{{ $q->created_at?->diffForHumans() }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('member.questions.show', $q) }}" class="text-xs text-brand-primary hover:underline">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-sm text-ink-muted italic">
                                    No questions yet. Use <span class="font-medium text-ink">Ask a question</span> on the website header to get started.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($items->hasPages())
                <div class="px-4 py-3 border-t border-[rgb(var(--border))]">{{ $items->links() }}</div>
            @endif
        </div>
    </div>
</x-member.layout>
