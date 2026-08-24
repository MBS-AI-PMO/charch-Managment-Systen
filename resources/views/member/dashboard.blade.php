@php
    $user = auth()->user();
    $hour = (int) now()->format('H');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
    $firstName = $user ? trim(explode(' ', $user->name)[0]) : '';
@endphp
<x-member.layout title="Dashboard">
    <div class="member-shell space-y-8">
        <div class="card p-6 md:p-8 bg-gradient-to-br from-brand-primary/10 to-brand-secondary/20">
            <h1 class="font-serif text-3xl md:text-4xl">{{ $greeting }}{{ $firstName ? ', '.$firstName : '' }}</h1>
            <p class="text-ink-muted mt-2">Welcome back. Here's what's happening this week.</p>
            <div class="mt-5 flex flex-wrap gap-2">
                <a href="{{ route('member.prayer.create') }}" class="btn-primary text-sm">Submit a prayer request</a>
                <a href="{{ route('member.certificates.index') }}" class="btn-ghost text-sm">My certificates</a>
                <a href="{{ route('member.care.create') }}" class="btn-ghost text-sm">Knock for help</a>
                <a href="{{ route('member.questions.index') }}" class="btn-ghost text-sm">My questions</a>
                <a href="{{ route('member.profile.edit') }}" class="btn-ghost text-sm">Update profile</a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="card p-5 lg:col-span-2">
                <h2 class="font-serif text-xl mb-1">From the feed</h2>
                <p class="text-sm text-ink-muted mb-4">Latest from the team.</p>
                @if($latestPosts->isEmpty())
                    <p class="text-sm text-ink-muted italic py-4">No posts yet.</p>
                @else
                    <ul class="divide-y divide-[rgb(var(--border))]">
                        @foreach($latestPosts as $p)
                            <li class="py-3">
                                <div class="flex flex-wrap justify-between gap-3">
                                    <span class="font-medium">{{ $p->title }}</span>
                                    <span class="text-xs text-ink-muted shrink-0">{{ $p->published_at?->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-ink-muted mt-1">{{ \Illuminate\Support\Str::limit(strip_tags($p->body ?? $p->excerpt ?? ''), 140) }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="space-y-6">
                <div class="card p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-serif text-lg">Your RSVPs</h3>
                        @if($hasOpenCheckin)
                            <a href="{{ route('member.checkin.show') }}" class="text-sm font-semibold text-brand-primary">Check in →</a>
                        @endif
                    </div>

                    @if($upcomingRsvps->isEmpty())
                        <p class="text-ink-muted text-sm">You haven't RSVP'd to anything yet. <a href="{{ route('site.events.index') }}" class="text-brand-primary">Browse events</a>.</p>
                    @else
                        <ul class="space-y-3">
                            @foreach($upcomingRsvps as $r)
                                <li class="flex items-start justify-between">
                                    <div>
                                        <a href="{{ route('site.events.show', $r->event) }}" class="font-medium hover:text-brand-primary">{{ $r->event->title }}</a>
                                        <p class="text-xs text-ink-muted">{{ $r->event->starts_at?->format('D, M j · g:i A') }}</p>
                                    </div>
                                    @if($r->guest_count > 0)
                                        <span class="text-xs text-ink-muted">+{{ $r->guest_count }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="card p-5">
                    <h2 class="font-serif text-lg mb-3">Your prayer requests</h2>
                    @if($myPrayer->isEmpty())
                        <p class="text-sm text-ink-muted italic">You haven't submitted any prayer requests yet.</p>
                    @else
                        <ul class="space-y-2 text-sm">
                            @foreach($myPrayer as $p)
                                <li class="flex items-center justify-between gap-2">
                                    <span class="truncate">{{ $p->title }}</span>
                                    @php
                                        $statusClass = match($p->status ?? 'pending') {
                                            'praying'  => 'bg-amber-50 text-amber-700',
                                            'answered' => 'bg-emerald-50 text-emerald-700',
                                            'closed'   => 'bg-slate-100 text-slate-600',
                                            default    => 'bg-slate-100 text-slate-700',
                                        };
                                    @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs {{ $statusClass }}">{{ ucfirst($p->status ?? 'pending') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="card p-5">
                    <h2 class="font-serif text-lg mb-3">Upcoming</h2>
                    @if($upcomingEvents->isEmpty())
                        <p class="text-sm text-ink-muted italic">No upcoming events.</p>
                    @else
                        <ul class="space-y-2 text-sm">
                            @foreach($upcomingEvents as $event)
                                <li>
                                    <span class="font-medium">{{ $event->starts_at?->format('D') }}</span>
                                    &middot; {{ $event->title }}
                                    &middot; {{ $event->starts_at?->format('g:ia') }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-member.layout>
