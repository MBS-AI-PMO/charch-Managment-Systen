@php
$users = [
    ['name'=>'Sarah Admin',  'email'=>'sarah@grace.local',  'role'=>'Site Admin',       'last'=>'Just now',          'status'=>'active'],
    ['name'=>'Pastor Greg',  'email'=>'greg@grace.local',   'role'=>'Site Admin',       'last'=>'2 hours ago',       'status'=>'active'],
    ['name'=>'Mike Davis',   'email'=>'mike@grace.local',   'role'=>'Event Organizer',  'last'=>'Yesterday',         'status'=>'active'],
    ['name'=>'Anna Wright',  'email'=>'anna@grace.local',   'role'=>'Prayer Organizer', 'last'=>'3 days ago',        'status'=>'active'],
    ['name'=>'James Park',   'email'=>'james@grace.local',  'role'=>'Event Organizer',  'last'=>'2 weeks ago',       'status'=>'active'],
    ['name'=>'Eleanor Cole', 'email'=>'eleanor@grace.local','role'=>'Prayer Organizer', 'last'=>'1 month ago',       'status'=>'inactive'],
    ['name'=>'Pastor James', 'email'=>'pjames@grace.local', 'role'=>'Site Admin',       'last'=>'Today',             'status'=>'active'],
];

$roleStyles = [
    'Site Admin'        => 'bg-brand-primary/10 text-brand-primary border-brand-primary/20',
    'Event Organizer'   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'Prayer Organizer'  => 'bg-violet-50 text-violet-700 border-violet-200',
];
@endphp

<x-admin.layout title="Users">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-serif">Users</h1>
            <p class="text-sm text-ink-muted mt-1">Manage admin and staff accounts. Roles determine what each user can edit.</p>
        </div>
        <button class="btn-primary text-sm">+ Invite user</button>
    </div>

    <div class="card overflow-hidden">
        <div class="px-5 py-3 border-b border-[rgb(var(--border))] flex items-center gap-2 flex-wrap">
            <input type="text" class="input w-64" placeholder="Search users…">
            <select class="input w-44"><option>All roles</option><option>Site Admin</option><option>Event Organizer</option><option>Prayer Organizer</option></select>
            <select class="input w-36"><option>All statuses</option><option>Active</option><option>Inactive</option></select>
            <span class="ml-auto text-xs text-ink-muted">{{ count($users) }} users</span>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-surface text-ink-muted text-xs uppercase tracking-wider">
                <tr>
                    <th class="text-left font-medium px-5 py-3">Name</th>
                    <th class="text-left font-medium px-5 py-3">Email</th>
                    <th class="text-left font-medium px-5 py-3">Role</th>
                    <th class="text-left font-medium px-5 py-3">Last login</th>
                    <th class="text-left font-medium px-5 py-3">Status</th>
                    <th class="text-right font-medium px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[rgb(var(--border))]">
                @foreach($users as $u)
                    <tr class="hover:bg-surface/60">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-brand-secondary/20 text-[#8a6e2c] flex items-center justify-center text-[11px] font-semibold">{{ collect(explode(' ', $u['name']))->map(fn($w)=>$w[0])->take(2)->join('') }}</div>
                                <span class="font-medium">{{ $u['name'] }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-ink-muted">{{ $u['email'] }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium border {{ $roleStyles[$u['role']] ?? 'bg-gray-50 text-gray-700 border-gray-200' }}">{{ $u['role'] }}</span>
                        </td>
                        <td class="px-5 py-3 text-ink-muted">{{ $u['last'] }}</td>
                        <td class="px-5 py-3">
                            @if($u['status']==='active')
                                <span class="inline-flex items-center gap-1.5 text-xs"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs text-ink-muted"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Inactive</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="#" class="text-xs text-brand-primary hover:underline">Edit</a>
                            <a href="#" class="text-xs text-ink-muted hover:text-ink ml-3">Send reset</a>
                            <a href="#" class="text-xs text-ink-muted hover:text-brand-primary ml-3">Deactivate</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin.layout>
