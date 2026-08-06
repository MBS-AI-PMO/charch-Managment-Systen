<x-member.layout title="Wisdom for my marriage">
    <div class="max-w-3xl mx-auto px-4 py-10 space-y-6">
        <a href="{{ route('preview.member.prayer') }}" class="text-sm text-ink-muted hover:text-brand-primary">&larr; Back to prayer wall</a>

        <article class="card p-6 md:p-8 space-y-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-full bg-brand-primary/10 text-brand-primary flex items-center justify-center font-medium">A</span>
                    <div>
                        <div class="text-sm font-medium">Anna T.</div>
                        <div class="text-xs text-ink-muted">2 days ago</div>
                    </div>
                </div>
                <span class="inline-flex px-3 py-1 rounded-full text-xs bg-amber-50 text-amber-700">Praying</span>
            </div>

            <h1 class="font-serif text-3xl">Wisdom for my marriage</h1>

            <div class="prose prose-sm max-w-none text-ink/90 leading-relaxed space-y-3">
                <p>My husband and I have been navigating some really hard conversations over the past few weeks. We've been together for 14 years and we still love each other deeply, but we've hit a season where it's just hard.</p>
                <p>Asking the community to pray with us for wisdom, patience, and grace as we work through it. Pray that God would speak to both of us in a way we can really hear.</p>
                <p>Thank you so much &mdash; you all mean the world.</p>
            </div>

            <div class="border-t border-[rgb(var(--border))] pt-5" x-data="{prayed:false, count:12}">
                <button @click="prayed=!prayed; prayed ? count++ : count--"
                        :class="prayed ? 'bg-brand-primary text-white border-brand-primary' : 'border-[rgb(var(--border))] hover:border-brand-primary hover:text-brand-primary'"
                        class="w-full sm:w-auto text-sm border rounded-md px-6 py-2.5 transition-colors flex items-center justify-center gap-2">
                    <span>&#128591;</span>
                    <span x-text="prayed ? 'You\'re praying for Anna' : 'I\'m praying for Anna'"></span>
                    <span class="text-xs opacity-70" x-text="'('+count+')'"></span>
                </button>
            </div>

            <div class="flex flex-wrap gap-3 pt-2 text-xs">
                <button type="button" class="text-brand-primary hover:underline">Edit</button>
                <button type="button" class="text-ink-muted hover:text-red-600">Delete</button>
            </div>
        </article>

        <div class="card p-6">
            <h2 class="font-serif text-lg mb-3">More from Anna</h2>
            <ul class="space-y-2 text-sm">
                <li class="flex flex-wrap items-center justify-between gap-2">
                    <a href="#" class="hover:text-brand-primary">Travelling mercies this weekend</a>
                    <span class="text-xs text-ink-muted">3 weeks ago</span>
                </li>
                <li class="flex flex-wrap items-center justify-between gap-2">
                    <a href="#" class="hover:text-brand-primary">My dad's surgery went well &mdash; thank you!</a>
                    <span class="text-xs text-ink-muted">2 months ago</span>
                </li>
            </ul>
        </div>
    </div>
</x-member.layout>
