<x-member.layout title="Prayer requests">
    <div class="max-w-container mx-auto px-4 py-10 space-y-6" x-data="{tab:'mine'}">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="font-serif text-3xl md:text-4xl">Prayer wall</h1>
                <p class="text-ink-muted mt-2 text-sm">Submit a request, or pray for your community.</p>
            </div>
            <a href="{{ route('member.prayer.create') }}" class="btn-primary text-sm">+ New prayer request</a>
        </div>

        <div class="flex flex-wrap gap-2 border-b border-[rgb(var(--border))]">
            <button @click="tab='mine'"
                    :class="tab==='mine' ? 'text-brand-primary border-brand-primary' : 'text-ink-muted border-transparent hover:text-ink'"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition-colors">My requests ({{ $mine->count() }})</button>
            <button @click="tab='community'"
                    :class="tab==='community' ? 'text-brand-primary border-brand-primary' : 'text-ink-muted border-transparent hover:text-ink'"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition-colors">Community board ({{ $community->total() }})</button>
        </div>

        {{-- My requests --}}
        <div x-show="tab==='mine'" x-cloak>
            <div class="card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-surface text-ink-muted">
                            <tr>
                                <th class="text-left font-medium px-4 py-3">Title</th>
                                <th class="text-left font-medium px-4 py-3">Status</th>
                                <th class="text-left font-medium px-4 py-3">Prays</th>
                                <th class="text-left font-medium px-4 py-3">Submitted</th>
                                <th class="text-right font-medium px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[rgb(var(--border))]">
                            @forelse($mine as $r)
                                @php
                                    $statusMap = [
                                        'pending'  => ['bg-slate-100','text-slate-700'],
                                        'praying'  => ['bg-amber-50','text-amber-700'],
                                        'answered' => ['bg-emerald-50','text-emerald-700'],
                                        'closed'   => ['bg-slate-100','text-slate-600'],
                                    ];
                                    [$bg,$tx] = $statusMap[$r->status ?? 'pending'] ?? $statusMap['pending'];
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 font-medium">
                                        <a href="{{ route('member.prayer.show', $r) }}" class="hover:text-brand-primary">{{ $r->title }}</a>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs {{ $bg }} {{ $tx }} capitalize">{{ $r->status ?? 'pending' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-ink-muted">{{ $r->pray_count }}</td>
                                    <td class="px-4 py-3 text-ink-muted">{{ $r->created_at?->diffForHumans() }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="inline-flex flex-wrap gap-3 justify-end">
                                            <a href="{{ route('member.prayer.show', $r) }}" class="text-xs text-brand-primary hover:underline">View</a>
                                            <form method="POST" action="{{ route('member.prayer.destroy', $r) }}" onsubmit="return confirm('Delete this prayer request?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs text-ink-muted hover:text-red-600">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-10 text-center text-sm text-ink-muted italic">You haven't submitted any prayer requests yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Community board --}}
        <div x-show="tab==='community'" x-cloak>
            @if($community->isEmpty())
                <div class="card p-10 text-center text-sm text-ink-muted italic">No community prayer requests yet.</div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($community as $p)
                        @php $author = $p->displayName(); @endphp
                        <article class="card p-5 space-y-3">
                            <div class="flex items-center gap-2 text-xs text-ink-muted">
                                <span class="w-7 h-7 rounded-full bg-brand-primary/10 text-brand-primary flex items-center justify-center font-medium">{{ mb_substr($author, 0, 1) }}</span>
                                <span>{{ $author }}</span>
                                <span class="ml-auto">{{ $p->created_at?->diffForHumans() }}</span>
                            </div>
                            <h3 class="font-serif text-lg leading-snug">
                                <a href="{{ route('member.prayer.show', $p) }}" class="hover:text-brand-primary">{{ $p->title }}</a>
                            </h3>
                            <p class="text-sm text-ink-muted leading-relaxed">{{ \Illuminate\Support\Str::limit(strip_tags($p->body), 160) }}</p>
                            <form method="POST" action="{{ route('member.prayer.pray', $p) }}">
                                @csrf
                                <button type="submit"
                                        class="w-full text-sm border border-[rgb(var(--border))] hover:border-brand-primary hover:text-brand-primary rounded-md py-2 transition-colors flex items-center justify-center gap-2">
                                    <span>&#128591;</span>
                                    <span>I'm praying</span>
                                    <span class="opacity-70">({{ $p->pray_count }})</span>
                                </button>
                            </form>
                        </article>
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $community->links() }}
                </div>
            @endif
        </div>
    </div>
</x-member.layout>
