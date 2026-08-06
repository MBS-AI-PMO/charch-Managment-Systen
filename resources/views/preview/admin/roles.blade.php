@php
$groups = ['Pages','Blog','Sermons','Events','Ministries','Media','Settings','Users'];
$actions = ['view','create','edit','delete','publish'];

$roles = [
    [
        'name'  => 'Site Admin',
        'desc'  => 'Full access to every part of the CMS.',
        'users' => 3,
        'pill'  => 'bg-brand-primary/10 text-brand-primary border-brand-primary/20',
        'perms' => array_fill_keys($groups, $actions),
    ],
    [
        'name'  => 'Event Organizer',
        'desc'  => 'Can create, edit and publish events. Read-only elsewhere.',
        'users' => 4,
        'pill'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'perms' => [
            'Pages'=>['view'], 'Blog'=>['view','create','edit'], 'Sermons'=>['view'],
            'Events'=>['view','create','edit','delete','publish'],
            'Ministries'=>['view'], 'Media'=>['view','create','edit'],
            'Settings'=>[], 'Users'=>[],
        ],
    ],
    [
        'name'  => 'Prayer Organizer',
        'desc'  => 'Manages ministry pages, prayer requests, and messages.',
        'users' => 2,
        'pill'  => 'bg-violet-50 text-violet-700 border-violet-200',
        'perms' => [
            'Pages'=>['view'], 'Blog'=>['view'], 'Sermons'=>['view'],
            'Events'=>['view'],
            'Ministries'=>['view','create','edit','publish'],
            'Media'=>['view','create'], 'Settings'=>[], 'Users'=>[],
        ],
    ],
];
@endphp

<x-admin.layout title="Roles & permissions">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-serif">Roles &amp; permissions</h1>
            <p class="text-sm text-ink-muted mt-1">Define what each role can see and do across the CMS.</p>
        </div>
        <button class="btn-primary text-sm">+ New role</button>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        @foreach($roles as $role)
            <div class="card overflow-hidden">
                <div class="px-5 py-4 border-b border-[rgb(var(--border))] flex items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-serif">{{ $role['name'] }}</h2>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium border {{ $role['pill'] }}">{{ $role['users'] }} {{ Str::plural('user', $role['users']) }}</span>
                        </div>
                        <p class="text-xs text-ink-muted mt-1">{{ $role['desc'] }}</p>
                    </div>
                    <button class="text-xs text-ink-muted hover:text-brand-primary">⋯</button>
                </div>

                <div class="p-5 space-y-3 max-h-[480px] overflow-y-auto">
                    @foreach($groups as $g)
                        <div class="border border-[rgb(var(--border))] rounded-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium">{{ $g }}</span>
                                @if(count($role['perms'][$g] ?? []) === count($actions))
                                    <span class="text-[11px] text-emerald-700">Full access</span>
                                @elseif(empty($role['perms'][$g]))
                                    <span class="text-[11px] text-ink-muted">No access</span>
                                @else
                                    <span class="text-[11px] text-ink-muted">{{ count($role['perms'][$g]) }} of {{ count($actions) }}</span>
                                @endif
                            </div>
                            <div class="grid grid-cols-2 gap-1.5">
                                @foreach($actions as $a)
                                    @php $checked = in_array($a, $role['perms'][$g] ?? []); @endphp
                                    <label class="flex items-center gap-2 text-xs text-ink-muted">
                                        <input type="checkbox" {{ $checked ? 'checked' : '' }} class="rounded border-[rgb(var(--border))] text-brand-primary focus:ring-brand-primary">
                                        <span class="capitalize">{{ $a }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="px-5 py-3 border-t border-[rgb(var(--border))] flex items-center justify-end gap-2">
                    <button class="btn-ghost text-xs">Duplicate</button>
                    <button class="btn-primary text-xs">Save changes</button>
                </div>
            </div>
        @endforeach
    </div>
</x-admin.layout>
