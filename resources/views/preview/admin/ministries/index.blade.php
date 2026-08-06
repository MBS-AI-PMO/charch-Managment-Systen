@php
$ministries = [
    ['name'=>"Children's ministry", 'leader'=>'Anna Wright',  'members'=>34, 'status'=>'active', 'tint'=>'from-amber-200 to-amber-400'],
    ['name'=>'Youth (grades 6–12)', 'leader'=>'James Park',   'members'=>52, 'status'=>'active', 'tint'=>'from-emerald-200 to-emerald-400'],
    ['name'=>'Young adults',        'leader'=>'Sarah Admin',  'members'=>28, 'status'=>'active', 'tint'=>'from-rose-200 to-rose-400'],
    ['name'=>'Worship team',        'leader'=>'Mike Davis',   'members'=>18, 'status'=>'active', 'tint'=>'from-violet-200 to-violet-400'],
    ['name'=>'Hospitality',         'leader'=>'Eleanor Cole', 'members'=>22, 'status'=>'active', 'tint'=>'from-sky-200 to-sky-400'],
    ['name'=>'Prayer team',         'leader'=>'Pastor James', 'members'=>14, 'status'=>'active', 'tint'=>'from-stone-200 to-stone-400'],
    ['name'=>'Outreach (paused)',   'leader'=>'—',            'members'=>0,  'status'=>'paused', 'tint'=>'from-gray-200 to-gray-300'],
];
@endphp

<x-admin.layout title="Ministries">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-serif">Ministries</h1>
            <p class="text-sm text-ink-muted mt-1">Drag to reorder how ministries appear on your public site.</p>
        </div>
        <button class="btn-primary text-sm">+ New ministry</button>
    </div>

    <ul class="card divide-y divide-[rgb(var(--border))]">
        @foreach($ministries as $m)
            <li class="flex items-center gap-4 px-5 py-4 hover:bg-surface/50">
                <span class="text-ink-muted cursor-grab shrink-0" title="Drag to reorder">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><circle cx="9" cy="6" r="1.4"/><circle cx="15" cy="6" r="1.4"/><circle cx="9" cy="12" r="1.4"/><circle cx="15" cy="12" r="1.4"/><circle cx="9" cy="18" r="1.4"/><circle cx="15" cy="18" r="1.4"/></svg>
                </span>
                <div class="w-14 h-14 rounded-lg bg-gradient-to-br {{ $m['tint'] }} shrink-0"></div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-medium">{{ $m['name'] }}</span>
                        @if($m['status']==='active')
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 text-gray-600 border border-gray-200">Paused</span>
                        @endif
                    </div>
                    <div class="text-xs text-ink-muted mt-0.5">Led by {{ $m['leader'] }} • {{ $m['members'] }} members</div>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <a href="#" class="text-brand-primary hover:underline">Edit</a>
                    <a href="#" class="text-ink-muted hover:text-ink">Members</a>
                    <a href="#" class="text-ink-muted hover:text-brand-primary">Archive</a>
                </div>
            </li>
        @endforeach
    </ul>
</x-admin.layout>
