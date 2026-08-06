<x-member.layout title="Knock for Help">
    <div class="max-w-2xl mx-auto px-4 py-10">
        <a href="{{ route('member.care.index') }}" class="text-sm text-ink-muted hover:text-brand-primary">&larr; Back</a>

        <div class="card p-6 md:p-8 mt-3 bg-gradient-to-br from-brand-secondary/20 to-transparent">
            <h1 class="font-serif text-2xl md:text-3xl">We're glad you're here.</h1>
            <p class="text-ink-muted mt-2 text-sm leading-relaxed">Tell us what's going on &mdash; a pastor will reach out within 24 hours. Everything you share here is private.</p>
        </div>

        <form method="POST" action="{{ route('member.care.store') }}" class="card p-6 md:p-8 mt-6 space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-3">What kind of help do you need?</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @php
                        $cats = [
                            ['illness',   'Illness',   'Recovery, hospital visits, ongoing care.'],
                            ['grief',     'Grief',     'Loss, bereavement, hard memories.'],
                            ['financial', 'Financial', 'Bills, debt, unexpected expenses.'],
                            ['food',      'Food',      'A meal train, grocery support.'],
                            ['other',     'Other',     'Something else &mdash; just tell us.'],
                        ];
                        $selectedCat = old('category', 'illness');
                    @endphp
                    @foreach($cats as $c)
                        <label class="cursor-pointer block">
                            <input type="radio" name="category" value="{{ $c[0] }}" class="sr-only peer" @checked($selectedCat === $c[0]) />
                            <div class="border rounded-lg p-4 transition border-[rgb(var(--border))] bg-white hover:border-brand-secondary peer-checked:border-brand-primary peer-checked:bg-brand-primary/5 peer-checked:ring-1 peer-checked:ring-brand-primary/30">
                                <div class="text-sm font-medium">{{ $c[1] }}</div>
                                <p class="text-xs text-ink-muted mt-1 leading-snug">{!! $c[2] !!}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('category')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="message">Tell us more</label>
                <textarea id="message" name="message" class="input w-full" rows="6" maxlength="5000" placeholder="Share what you're comfortable sharing..." required>{{ old('message') }}</textarea>
                <p class="text-xs text-ink-muted mt-1">Only the pastoral team will read this unless you opt in below.</p>
                @error('message')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <label class="flex items-start justify-between gap-4 rounded-lg border border-[rgb(var(--border))] p-4 cursor-pointer">
                <div>
                    <div class="text-sm font-medium">Share with the prayer team</div>
                    <p class="text-xs text-ink-muted mt-0.5">Let our prayer team pray for you, in addition to the pastor.</p>
                </div>
                <input type="hidden" name="share_with_team" value="0">
                <input type="checkbox" name="share_with_team" value="1" class="mt-1 h-5 w-5 accent-brand-primary" @checked(old('share_with_team')) />
            </label>
            @error('share_with_team')<p class="-mt-3 text-xs text-red-600">{{ $message }}</p>@enderror

            <div class="flex flex-wrap gap-2 pt-2">
                <button type="submit" class="btn-primary text-sm">Send privately</button>
                <a href="{{ route('member.care.index') }}" class="btn-ghost text-sm">Cancel</a>
            </div>
        </form>
    </div>
</x-member.layout>
