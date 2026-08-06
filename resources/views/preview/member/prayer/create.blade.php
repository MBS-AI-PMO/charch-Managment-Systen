<x-member.layout title="Submit a prayer request">
    <div class="max-w-2xl mx-auto px-4 py-10">
        <a href="{{ route('preview.member.prayer') }}" class="text-sm text-ink-muted hover:text-brand-primary">&larr; Back to prayer wall</a>
        <h1 class="font-serif text-3xl md:text-4xl mt-3">Submit a prayer request</h1>
        <p class="text-ink-muted mt-2 text-sm">Share what's on your heart. You choose who sees it.</p>

        <form class="card p-6 md:p-8 mt-6 space-y-5" x-data="{public:false, anon:false}">
            <div>
                <label class="block text-sm font-medium mb-1">Title</label>
                <input class="input w-full" type="text" maxlength="200" placeholder="A short heading for your request" required />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">What would you like prayer for?</label>
                <textarea class="input w-full" rows="6" placeholder="Share as much or as little as you'd like..." required></textarea>
            </div>

            <div class="rounded-lg border border-[rgb(var(--border))] divide-y divide-[rgb(var(--border))]">
                <label class="flex items-start justify-between gap-4 p-4 cursor-pointer">
                    <div>
                        <div class="text-sm font-medium">Share with the community board</div>
                        <p class="text-xs text-ink-muted mt-0.5">Other members will see this on the public prayer wall.</p>
                    </div>
                    <input type="checkbox" x-model="public" class="mt-1 h-5 w-5 accent-brand-primary" />
                </label>
                <label class="flex items-start justify-between gap-4 p-4"
                       :class="public ? 'cursor-pointer' : 'opacity-50 cursor-not-allowed'">
                    <div>
                        <div class="text-sm font-medium">Submit anonymously</div>
                        <p class="text-xs text-ink-muted mt-0.5">Hide your name on the public board. Pastors will still know it's you.</p>
                    </div>
                    <input type="checkbox" x-model="anon" :disabled="!public" class="mt-1 h-5 w-5 accent-brand-primary" />
                </label>
            </div>

            <div class="flex flex-wrap gap-2 pt-2">
                <button type="button" class="btn-primary text-sm">Submit prayer request</button>
                <a href="{{ route('preview.member.prayer') }}" class="btn-ghost text-sm">Cancel</a>
            </div>
        </form>
    </div>
</x-member.layout>
