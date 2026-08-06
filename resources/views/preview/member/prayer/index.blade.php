<x-member.layout title="Prayer requests">
    <div class="max-w-container mx-auto px-4 py-10 space-y-6" x-data="{tab:'mine'}">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="font-serif text-3xl md:text-4xl">Prayer wall</h1>
                <p class="text-ink-muted mt-2 text-sm">Submit a request, or pray for your community.</p>
            </div>
            <a href="{{ route('preview.member.prayer.create') }}" class="btn-primary text-sm">+ New prayer request</a>
        </div>

        <div class="flex flex-wrap gap-2 border-b border-[rgb(var(--border))]">
            <button @click="tab='mine'"
                    :class="tab==='mine' ? 'text-brand-primary border-brand-primary' : 'text-ink-muted border-transparent hover:text-ink'"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition-colors">My requests</button>
            <button @click="tab='community'"
                    :class="tab==='community' ? 'text-brand-primary border-brand-primary' : 'text-ink-muted border-transparent hover:text-ink'"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition-colors">Community board</button>
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
                            @foreach([
                                ['Family wisdom','praying','amber',12,'2 days ago'],
                                ['New job opportunity','answered','emerald',8,'1 week ago'],
                                ['Healing for my mum','pending','gray',0,'3 hours ago'],
                                ['Direction on a big decision','praying','amber',5,'5 days ago'],
                            ] as $r)
                                <tr>
                                    <td class="px-4 py-3 font-medium"><a href="{{ route('preview.member.prayer.show') }}" class="hover:text-brand-primary">{{ $r[0] }}</a></td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-{{ $r[2] }}-50 text-{{ $r[2] }}-700 capitalize">{{ $r[1] }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-ink-muted">{{ $r[3] }}</td>
                                    <td class="px-4 py-3 text-ink-muted">{{ $r[4] }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="inline-flex flex-wrap gap-3 justify-end">
                                            <a href="#" class="text-xs text-brand-primary hover:underline">Edit</a>
                                            <a href="#" class="text-xs text-ink-muted hover:text-red-600">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Community board --}}
        <div x-show="tab==='community'" x-cloak>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach([
                    ['author' => 'Anna T.', 'title' => 'Wisdom for my marriage', 'excerpt' => 'Asking for prayer as we work through some hard conversations together.', 'count' => 12],
                    ['author' => 'Anonymous', 'title' => 'Health concerns', 'excerpt' => 'Test results next week. Praying for peace and a clear answer.', 'count' => 8],
                    ['author' => 'David K.', 'title' => 'New ministry season', 'excerpt' => 'Starting to lead the youth group from January. Pray for energy and wisdom.', 'count' => 17],
                    ['author' => 'Grace L.', 'title' => 'Family reconciliation', 'excerpt' => 'My brother and I have been distant for years. Trusting God to heal it.', 'count' => 21],
                    ['author' => 'Anonymous', 'title' => 'Job search', 'excerpt' => 'Three months in and still nothing. Asking for hope and direction.', 'count' => 6],
                    ['author' => 'Michael P.', 'title' => 'Surgery on Friday', 'excerpt' => 'Routine but still anxious. Thank you for praying with me.', 'count' => 33],
                ] as $i => $p)
                    <article class="card p-5 space-y-3" x-data="{prayed:false, count:{{ $p['count'] }}}">
                        <div class="flex items-center gap-2 text-xs text-ink-muted">
                            <span class="w-7 h-7 rounded-full bg-brand-primary/10 text-brand-primary flex items-center justify-center font-medium">{{ mb_substr($p['author'], 0, 1) }}</span>
                            <span>{{ $p['author'] }}</span>
                        </div>
                        <h3 class="font-serif text-lg leading-snug"><a href="{{ route('preview.member.prayer.show') }}" class="hover:text-brand-primary">{{ $p['title'] }}</a></h3>
                        <p class="text-sm text-ink-muted leading-relaxed">{{ $p['excerpt'] }}</p>
                        <button @click="prayed=!prayed; prayed ? count++ : count--"
                                :class="prayed ? 'bg-brand-primary text-white border-brand-primary' : 'border-[rgb(var(--border))] hover:border-brand-primary hover:text-brand-primary'"
                                class="w-full text-sm border rounded-md py-2 transition-colors flex items-center justify-center gap-2">
                            <span>&#128591;</span>
                            <span x-text="prayed ? 'You\'re praying' : 'I\'m praying'"></span>
                            <span x-text="'('+count+')'"></span>
                        </button>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</x-member.layout>
