<x-member.layout title="Knock for help">
    <div class="max-w-2xl mx-auto px-4 py-10">
        <a href="{{ route('preview.member.care') }}" class="text-sm text-ink-muted hover:text-brand-primary">&larr; Back</a>

        <div class="card p-6 md:p-8 mt-3 bg-gradient-to-br from-brand-secondary/20 to-transparent">
            <h1 class="font-serif text-2xl md:text-3xl">We're glad you're here.</h1>
            <p class="text-ink-muted mt-2 text-sm leading-relaxed">Whatever you're carrying right now, you don't have to carry it alone. Share what you can &mdash; a pastor will reach out within 24 hours. Everything you share here is private.</p>
        </div>

        <form class="card p-6 md:p-8 mt-6 space-y-6" x-data="{cat:'illness'}">
            <div>
                <label class="block text-sm font-medium mb-3">What kind of help do you need?</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach([
                        ['illness',   'Illness',   'Recovery, hospital visits, ongoing care.'],
                        ['grief',     'Grief',     'Loss, bereavement, hard memories.'],
                        ['financial', 'Financial', 'Bills, debt, unexpected expenses.'],
                        ['food',      'Food',      'A meal train, grocery support.'],
                        ['other',     'Other',     'Something else &mdash; just tell us.'],
                    ] as $c)
                        <label class="cursor-pointer">
                            <input type="radio" name="category" value="{{ $c[0] }}" x-model="cat" class="sr-only peer" @if($loop->first) checked @endif />
                            <div :class="cat==='{{ $c[0] }}' ? 'border-brand-primary bg-brand-primary/5 ring-1 ring-brand-primary/30' : 'border-[rgb(var(--border))] bg-white hover:border-brand-secondary'"
                                 class="border rounded-lg p-4 transition">
                                <div class="text-sm font-medium">{{ $c[1] }}</div>
                                <p class="text-xs text-ink-muted mt-1 leading-snug">{!! $c[2] !!}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Tell us more</label>
                <textarea class="input w-full" rows="6" placeholder="Share what you're comfortable sharing..." required></textarea>
                <p class="text-xs text-ink-muted mt-1">Only the pastoral team will read this unless you opt in below.</p>
            </div>

            <label class="flex items-start justify-between gap-4 rounded-lg border border-[rgb(var(--border))] p-4 cursor-pointer">
                <div>
                    <div class="text-sm font-medium">Share with the prayer team</div>
                    <p class="text-xs text-ink-muted mt-0.5">Let our prayer team pray for you, in addition to the pastor.</p>
                </div>
                <input type="checkbox" class="mt-1 h-5 w-5 accent-brand-primary" />
            </label>

            <div class="flex flex-wrap gap-2 pt-2">
                <button type="button" class="btn-primary text-sm">Send privately</button>
                <a href="{{ route('preview.member.care') }}" class="btn-ghost text-sm">Cancel</a>
            </div>
        </form>
    </div>
</x-member.layout>
