<x-member.layout title="Dashboard">
    <div class="max-w-container mx-auto px-4 py-10 space-y-8">
        <div class="card p-6 md:p-8 bg-gradient-to-br from-brand-primary/10 to-brand-secondary/20">
            <h1 class="font-serif text-3xl md:text-4xl">Good afternoon, Sarah</h1>
            <p class="text-ink-muted mt-2">Welcome back. Here's what's happening this week.</p>
            <div class="mt-5 flex flex-wrap gap-2">
                <a href="{{ route('preview.member.prayer.create') }}" class="btn-primary text-sm">Submit a prayer request</a>
                <a href="{{ route('preview.member.care.create') }}" class="btn-ghost text-sm">Knock for help</a>
                <a href="{{ route('preview.member.profile') }}" class="btn-ghost text-sm">Update profile</a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="card p-5 lg:col-span-2">
                <h2 class="font-serif text-xl mb-1">From the feed</h2>
                <p class="text-sm text-ink-muted mb-4">Latest from the team.</p>
                <ul class="divide-y divide-[rgb(var(--border))]">
                    @foreach([
                        ['title' => 'Welcome to the new portal', 'time' => '2 hours ago', 'excerpt' => 'We have launched our new member portal — explore it and let us know what you think.'],
                        ['title' => 'Sunday gathering: a special evening', 'time' => 'Yesterday', 'excerpt' => 'Join us for an extended worship time at 6pm this Sunday.'],
                        ['title' => 'Volunteer call: kids ministry', 'time' => '3 days ago', 'excerpt' => 'We need 2 more volunteers for Sunday mornings — could that be you?'],
                    ] as $p)
                        <li class="py-3">
                            <div class="flex flex-wrap justify-between gap-3">
                                <span class="font-medium">{{ $p['title'] }}</span>
                                <span class="text-xs text-ink-muted shrink-0">{{ $p['time'] }}</span>
                            </div>
                            <p class="text-sm text-ink-muted mt-1">{{ $p['excerpt'] }}</p>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('preview.member.feed') }}" class="mt-3 inline-flex text-sm text-brand-primary hover:underline">See all &rarr;</a>
            </div>

            <div class="space-y-6">
                <div class="card p-5">
                    <h2 class="font-serif text-lg mb-3">Your prayer requests</h2>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center justify-between gap-2">
                            <span>Family wisdom</span>
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-amber-50 text-amber-700">Praying</span>
                        </li>
                        <li class="flex items-center justify-between gap-2">
                            <span>New job opportunity</span>
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-emerald-50 text-emerald-700">Answered</span>
                        </li>
                    </ul>
                    <a href="{{ route('preview.member.prayer') }}" class="mt-4 inline-flex text-sm text-brand-primary hover:underline">View all &rarr;</a>
                </div>

                <div class="card p-5">
                    <h2 class="font-serif text-lg mb-3">Upcoming</h2>
                    <ul class="space-y-2 text-sm">
                        <li><span class="font-medium">Sun</span> &middot; Sunday service &middot; 10am</li>
                        <li><span class="font-medium">Wed</span> &middot; Midweek prayer &middot; 7pm</li>
                        <li><span class="font-medium">Sat</span> &middot; Youth gathering &middot; 6pm</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-member.layout>
