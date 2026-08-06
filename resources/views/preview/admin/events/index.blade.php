@php
$upcoming = [
    ['day'=>'14','mon'=>'Jun','title'=>'Family picnic in the park',  'location'=>'Riverside Park',     'time'=>'12:00 PM', 'status'=>'published'],
    ['day'=>'21','mon'=>'Jun','title'=>'Father\'s Day breakfast',     'location'=>'Fellowship Hall',    'time'=>'9:30 AM',  'status'=>'published'],
    ['day'=>'28','mon'=>'Jun','title'=>'Worship night',                'location'=>'Main Sanctuary',     'time'=>'7:00 PM',  'status'=>'published'],
    ['day'=>'05','mon'=>'Jul','title'=>'Youth summer camp launch',    'location'=>'Camp Tahoma',        'time'=>'8:00 AM',  'status'=>'draft'],
    ['day'=>'19','mon'=>'Jul','title'=>'Community service day',      'location'=>'Downtown',           'time'=>'10:00 AM', 'status'=>'published'],
];
$past = [
    ['day'=>'26','mon'=>'May','title'=>'Sunday service',              'location'=>'Main Sanctuary',     'time'=>'9 & 11 AM','status'=>'completed'],
    ['day'=>'21','mon'=>'May','title'=>'Mid-week prayer',             'location'=>'Chapel',             'time'=>'7:00 PM',  'status'=>'completed'],
    ['day'=>'12','mon'=>'May','title'=>'Mother\'s Day brunch',        'location'=>'Fellowship Hall',    'time'=>'10:00 AM', 'status'=>'completed'],
];
@endphp

<x-admin.layout title="Events">
    <div x-data="{tab:'upcoming'}" class="space-y-6">
        <div class="flex items-end justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-2xl font-serif">Events</h1>
                <p class="text-sm text-ink-muted mt-1">Plan and publish gatherings, services, and community moments.</p>
            </div>
            <div class="flex items-center gap-2">
                <button class="btn-ghost text-sm">Calendar view</button>
                <button class="btn-primary text-sm">+ New event</button>
            </div>
        </div>

        <div class="border-b border-[rgb(var(--border))]">
            <nav class="flex gap-1">
                <button @click="tab='upcoming'" :class="tab==='upcoming' ? 'border-brand-primary text-ink' : 'border-transparent text-ink-muted hover:text-ink'" class="px-4 py-3 text-sm border-b-2 font-medium transition">Upcoming <span class="ml-1 text-xs text-ink-muted">({{ count($upcoming) }})</span></button>
                <button @click="tab='past'"     :class="tab==='past'     ? 'border-brand-primary text-ink' : 'border-transparent text-ink-muted hover:text-ink'" class="px-4 py-3 text-sm border-b-2 font-medium transition">Past <span class="ml-1 text-xs text-ink-muted">({{ count($past) }})</span></button>
            </nav>
        </div>

        @foreach([['upcoming', $upcoming], ['past', $past]] as $set)
            <div x-show="tab==='{{ $set[0] }}'" x-cloak class="card overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-surface text-ink-muted text-xs uppercase tracking-wider">
                        <tr>
                            <th class="text-left font-medium px-5 py-3 w-20">Date</th>
                            <th class="text-left font-medium px-5 py-3">Event</th>
                            <th class="text-left font-medium px-5 py-3">Location</th>
                            <th class="text-left font-medium px-5 py-3">Time</th>
                            <th class="text-left font-medium px-5 py-3">Status</th>
                            <th class="text-right font-medium px-5 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[rgb(var(--border))]">
                        @foreach($set[1] as $e)
                            <tr class="hover:bg-surface/60">
                                <td class="px-5 py-3">
                                    <div class="w-12 h-14 rounded-lg border border-[rgb(var(--border))] overflow-hidden text-center">
                                        <div class="bg-brand-primary text-white text-[10px] uppercase tracking-wider py-0.5">{{ $e['mon'] }}</div>
                                        <div class="text-lg font-serif text-ink leading-tight pt-1">{{ $e['day'] }}</div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 font-medium">{{ $e['title'] }}</td>
                                <td class="px-5 py-3 text-ink-muted">{{ $e['location'] }}</td>
                                <td class="px-5 py-3 text-ink-muted">{{ $e['time'] }}</td>
                                <td class="px-5 py-3">
                                    @php $st = $e['status']; @endphp
                                    @if($st==='published')
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Published</span>
                                    @elseif($st==='draft')
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Draft</span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 text-gray-600 border border-gray-200">Completed</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <a href="#" class="text-xs text-brand-primary hover:underline">Edit</a>
                                    <a href="#" class="text-xs text-ink-muted hover:text-ink ml-3">Duplicate</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
</x-admin.layout>
