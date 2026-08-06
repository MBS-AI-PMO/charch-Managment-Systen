@php
$messages = [
    ['name'=>'Anna Wright',   'email'=>'anna@example.com',   'subject'=>'Question about baptism',     'snippet'=>'Hi pastors, I\'m curious about the baptism process for our family. Could we…', 'when'=>'18 min', 'unread'=>true],
    ['name'=>'James Park',    'email'=>'james@example.com',  'subject'=>'Joining the youth team',     'snippet'=>'My wife and I would love to volunteer with the youth this fall. We have…',     'when'=>'2 hr',   'unread'=>true],
    ['name'=>'Eleanor Cole',  'email'=>'eleanor@gmail.com',  'subject'=>'Hospitality team availability','snippet'=>'I can serve every other Sunday at the 11 AM service. Let me know what slot…',  'when'=>'Yesterday','unread'=>true],
    ['name'=>'Mark Bradley',  'email'=>'mark@example.com',   'subject'=>'Plan a visit',               'snippet'=>'We\'re visiting Springfield in July and would love to attend. Are there…',     'when'=>'Tue',    'unread'=>false],
    ['name'=>'Lily Tran',     'email'=>'lily@example.com',   'subject'=>'Prayer request',             'snippet'=>'Please pray for my mom who is going in for surgery this Friday morning…',     'when'=>'May 22', 'unread'=>false],
    ['name'=>'Diego Ramirez', 'email'=>'diego@example.com',  'subject'=>'Spanish-language service?',  'snippet'=>'Hello, I was wondering if you have or are planning a Spanish-language…',      'when'=>'May 20', 'unread'=>false],
    ['name'=>'Maya Chen',     'email'=>'maya@example.com',   'subject'=>'Worship night details',      'snippet'=>'Thank you for the worship night last Friday — could you share the song list?', 'when'=>'May 18', 'unread'=>false],
    ['name'=>'Tom Whitford',  'email'=>'tom@example.com',    'subject'=>'Donation receipt',           'snippet'=>'Could I get a copy of my tax receipt for last year\'s donations? Thanks…',     'when'=>'May 14', 'unread'=>false],
];
@endphp

<x-admin.layout title="Messages">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-serif">Messages</h1>
            <p class="text-sm text-ink-muted mt-1">Messages sent through your public Contact form.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="btn-ghost text-sm">Mark all read</button>
            <button class="btn-ghost text-sm">Export CSV</button>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-12 min-h-[640px]">
            {{-- List --}}
            <aside class="lg:col-span-5 xl:col-span-4 border-r border-[rgb(var(--border))]">
                <div class="px-4 py-3 border-b border-[rgb(var(--border))] flex items-center gap-2">
                    <input type="text" class="input flex-1" placeholder="Search messages…">
                    <select class="input w-28"><option>All</option><option>Unread</option></select>
                </div>
                <ul class="divide-y divide-[rgb(var(--border))] max-h-[640px] overflow-y-auto">
                    @foreach($messages as $i => $m)
                        <li class="px-4 py-3 flex gap-3 cursor-pointer {{ $i===0 ? 'bg-brand-primary/[0.04] border-l-2 border-brand-primary' : 'hover:bg-surface/60' }}">
                            <div class="w-9 h-9 rounded-full bg-brand-secondary/20 text-[#8a6e2c] flex items-center justify-center text-xs font-semibold shrink-0">{{ collect(explode(' ', $m['name']))->map(fn($w)=>$w[0])->take(2)->join('') }}</div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-baseline justify-between gap-2">
                                    <span class="text-sm {{ $m['unread'] ? 'font-semibold text-ink' : 'text-ink' }}">{{ $m['name'] }}</span>
                                    <span class="text-[11px] text-ink-muted shrink-0">{{ $m['when'] }}</span>
                                </div>
                                <div class="text-sm {{ $m['unread'] ? 'font-medium text-ink' : 'text-ink-muted' }} truncate">{{ $m['subject'] }}</div>
                                <div class="text-xs text-ink-muted truncate">{{ $m['snippet'] }}</div>
                            </div>
                            @if($m['unread'])<span class="w-2 h-2 rounded-full bg-brand-primary mt-2 shrink-0"></span>@endif
                        </li>
                    @endforeach
                </ul>
            </aside>

            {{-- Detail --}}
            <section class="lg:col-span-7 xl:col-span-8 flex flex-col">
                <div class="px-6 py-4 border-b border-[rgb(var(--border))] flex items-start justify-between gap-3 flex-wrap">
                    <div>
                        <h2 class="text-lg font-serif">Question about baptism</h2>
                        <div class="text-xs text-ink-muted mt-1">
                            <span class="font-medium text-ink">Anna Wright</span> &lt;anna@example.com&gt; — May 31, 2026 at 9:18 AM
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <button class="btn-ghost">Reply</button>
                        <button class="btn-ghost">Mark unread</button>
                        <button class="btn-ghost text-brand-primary">Archive</button>
                    </div>
                </div>
                <div class="p-6 flex-1 text-sm leading-relaxed space-y-4">
                    <p>Hi pastors,</p>
                    <p>I'm curious about the baptism process for our family. My husband and I started attending in March and would love to take this step with our two kids (ages 7 and 9). Is there a class we should attend first? Are baptisms scheduled on certain Sundays?</p>
                    <p>Thank you for your time and for the warm welcome we've felt from day one.</p>
                    <p>Blessings,<br>Anna</p>
                </div>
                <div class="border-t border-[rgb(var(--border))] p-4 bg-surface/40">
                    <label class="block text-xs text-ink-muted mb-1.5">Quick reply</label>
                    <textarea rows="3" class="input" placeholder="Type a quick response…"></textarea>
                    <div class="flex justify-end gap-2 mt-2">
                        <button class="btn-ghost text-xs">Save draft</button>
                        <button class="btn-primary text-xs">Send reply</button>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-admin.layout>
