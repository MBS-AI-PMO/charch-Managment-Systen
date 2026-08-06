@php
$sermons = [
    ['title'=>'Hope in the wilderness',  'series'=>'Walking by faith', 'speaker'=>'Pastor Greg',  'date'=>'May 26, 2026', 'status'=>'published'],
    ['title'=>'Manna for today',         'series'=>'Walking by faith', 'speaker'=>'Pastor Greg',  'date'=>'May 19, 2026', 'status'=>'published'],
    ['title'=>'When God seems silent',   'series'=>'Walking by faith', 'speaker'=>'Pastor James', 'date'=>'May 12, 2026', 'status'=>'published'],
    ['title'=>'Faith of Abraham',        'series'=>'Walking by faith', 'speaker'=>'Pastor Greg',  'date'=>'May 05, 2026', 'status'=>'published'],
    ['title'=>'The good shepherd',       'series'=>'Psalms',           'speaker'=>'Pastor James', 'date'=>'Apr 28, 2026', 'status'=>'published'],
    ['title'=>'Sermon (upcoming)',       'series'=>'Walking by faith', 'speaker'=>'Pastor Greg',  'date'=>'Jun 02, 2026', 'status'=>'draft'],
];
$series = [
    ['name'=>'Walking by faith', 'count'=>5, 'status'=>'active'],
    ['name'=>'Psalms',           'count'=>8, 'status'=>'complete'],
    ['name'=>'Advent 2025',      'count'=>4, 'status'=>'complete'],
];
$speakers = [
    ['name'=>'Pastor Greg',  'role'=>'Senior pastor',     'sermons'=>42],
    ['name'=>'Pastor James', 'role'=>'Associate pastor',  'sermons'=>17],
    ['name'=>'Mike Davis',   'role'=>'Worship leader',    'sermons'=>3],
];
@endphp

<x-admin.layout title="Sermons">
    <div x-data="{tab:'sermons'}" class="space-y-6">
        <div class="flex items-end justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-2xl font-serif">Sermons</h1>
                <p class="text-sm text-ink-muted mt-1">Manage your sermon library, series, and speakers.</p>
            </div>
            <div class="flex items-center gap-2">
                <button class="btn-ghost text-sm">Import RSS</button>
                <button class="btn-primary text-sm">
                    <span x-show="tab==='sermons'">+ New sermon</span>
                    <span x-show="tab==='series'" x-cloak>+ New series</span>
                    <span x-show="tab==='speakers'" x-cloak>+ New speaker</span>
                </button>
            </div>
        </div>

        <div class="border-b border-[rgb(var(--border))]">
            <nav class="flex gap-1">
                <button @click="tab='sermons'"  :class="tab==='sermons'  ? 'border-brand-primary text-ink' : 'border-transparent text-ink-muted hover:text-ink'" class="px-4 py-3 text-sm border-b-2 font-medium transition">Sermons <span class="ml-1 text-xs text-ink-muted">({{ count($sermons) }})</span></button>
                <button @click="tab='series'"   :class="tab==='series'   ? 'border-brand-primary text-ink' : 'border-transparent text-ink-muted hover:text-ink'" class="px-4 py-3 text-sm border-b-2 font-medium transition">Series <span class="ml-1 text-xs text-ink-muted">({{ count($series) }})</span></button>
                <button @click="tab='speakers'" :class="tab==='speakers' ? 'border-brand-primary text-ink' : 'border-transparent text-ink-muted hover:text-ink'" class="px-4 py-3 text-sm border-b-2 font-medium transition">Speakers <span class="ml-1 text-xs text-ink-muted">({{ count($speakers) }})</span></button>
            </nav>
        </div>

        {{-- Sermons --}}
        <div x-show="tab==='sermons'" class="card overflow-hidden">
            <div class="px-5 py-3 border-b border-[rgb(var(--border))] flex items-center gap-2 flex-wrap">
                <input type="text" class="input w-64" placeholder="Search sermons…">
                <select class="input w-40"><option>All series</option><option>Walking by faith</option><option>Psalms</option></select>
                <select class="input w-40"><option>All speakers</option><option>Pastor Greg</option><option>Pastor James</option></select>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-surface text-ink-muted text-xs uppercase tracking-wider">
                    <tr>
                        <th class="text-left font-medium px-5 py-3">Title</th>
                        <th class="text-left font-medium px-5 py-3">Series</th>
                        <th class="text-left font-medium px-5 py-3">Speaker</th>
                        <th class="text-left font-medium px-5 py-3">Preached on</th>
                        <th class="text-left font-medium px-5 py-3">Status</th>
                        <th class="text-right font-medium px-5 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[rgb(var(--border))]">
                    @foreach($sermons as $s)
                        <tr class="hover:bg-surface/60">
                            <td class="px-5 py-3 font-medium">{{ $s['title'] }}</td>
                            <td class="px-5 py-3 text-ink-muted">{{ $s['series'] }}</td>
                            <td class="px-5 py-3 text-ink-muted">{{ $s['speaker'] }}</td>
                            <td class="px-5 py-3 text-ink-muted">{{ $s['date'] }}</td>
                            <td class="px-5 py-3">
                                @if($s['status']==='published')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Published</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Draft</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="#" class="text-xs text-brand-primary hover:underline">Edit</a>
                                <a href="#" class="text-xs text-ink-muted hover:text-ink ml-3">Play</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Series --}}
        <div x-show="tab==='series'" x-cloak class="card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-surface text-ink-muted text-xs uppercase tracking-wider">
                    <tr>
                        <th class="text-left font-medium px-5 py-3">Series</th>
                        <th class="text-left font-medium px-5 py-3">Sermons</th>
                        <th class="text-left font-medium px-5 py-3">Status</th>
                        <th class="text-right font-medium px-5 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[rgb(var(--border))]">
                    @foreach($series as $row)
                        <tr class="hover:bg-surface/60">
                            <td class="px-5 py-3 font-medium flex items-center gap-3">
                                <span class="w-10 h-10 rounded-md bg-gradient-to-br from-brand-primary/20 to-brand-secondary/30"></span>
                                {{ $row['name'] }}
                            </td>
                            <td class="px-5 py-3 text-ink-muted">{{ $row['count'] }}</td>
                            <td class="px-5 py-3 text-ink-muted capitalize">{{ $row['status'] }}</td>
                            <td class="px-5 py-3 text-right"><a href="#" class="text-xs text-brand-primary hover:underline">Edit</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Speakers --}}
        <div x-show="tab==='speakers'" x-cloak class="card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-surface text-ink-muted text-xs uppercase tracking-wider">
                    <tr>
                        <th class="text-left font-medium px-5 py-3">Name</th>
                        <th class="text-left font-medium px-5 py-3">Role</th>
                        <th class="text-left font-medium px-5 py-3">Sermons</th>
                        <th class="text-right font-medium px-5 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[rgb(var(--border))]">
                    @foreach($speakers as $sp)
                        <tr class="hover:bg-surface/60">
                            <td class="px-5 py-3 font-medium flex items-center gap-3">
                                <span class="w-9 h-9 rounded-full bg-brand-secondary/20 text-[#8a6e2c] flex items-center justify-center text-xs font-semibold">{{ collect(explode(' ', $sp['name']))->map(fn($w) => $w[0])->take(2)->join('') }}</span>
                                {{ $sp['name'] }}
                            </td>
                            <td class="px-5 py-3 text-ink-muted">{{ $sp['role'] }}</td>
                            <td class="px-5 py-3 text-ink-muted">{{ $sp['sermons'] }}</td>
                            <td class="px-5 py-3 text-right"><a href="#" class="text-xs text-brand-primary hover:underline">Edit</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-admin.layout>
