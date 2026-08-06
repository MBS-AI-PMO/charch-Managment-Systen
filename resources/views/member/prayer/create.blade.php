<x-member.layout title="Submit a prayer request">
    <div class="max-w-2xl mx-auto px-4 py-10">
        <a href="{{ route('member.prayer.index') }}" class="text-sm text-ink-muted hover:text-brand-primary">&larr; Back to prayer wall</a>
        <h1 class="font-serif text-3xl md:text-4xl mt-3">Submit a prayer request</h1>
        <p class="text-ink-muted mt-2 text-sm">Share what's on your heart. You choose who sees it.</p>

        <form method="POST" action="{{ route('member.prayer.store') }}" class="card p-6 md:p-8 mt-6 space-y-5" x-data="{pub:{{ old('is_public') ? 'true' : 'false' }}, anon:{{ old('is_anonymous') ? 'true' : 'false' }}}">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Title</label>
                <input name="title" class="input w-full" type="text" maxlength="200" placeholder="A short heading for your request" value="{{ old('title') }}" required />
                @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">What would you like prayer for?</label>
                <textarea name="body" class="input w-full" rows="6" maxlength="5000" placeholder="Share as much or as little as you'd like..." required>{{ old('body') }}</textarea>
                @error('body')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="rounded-lg border border-[rgb(var(--border))] divide-y divide-[rgb(var(--border))]">
                <label class="flex items-start justify-between gap-4 p-4 cursor-pointer">
                    <div>
                        <div class="text-sm font-medium">Share with the community board</div>
                        <p class="text-xs text-ink-muted mt-0.5">Other members will see this on the public prayer wall.</p>
                    </div>
                    <input type="hidden" name="is_public" value="0">
                    <input type="checkbox" name="is_public" value="1" x-model="pub" @change="if(!pub) anon=false" class="mt-1 h-5 w-5 accent-brand-primary" />
                </label>
                <label class="flex items-start justify-between gap-4 p-4"
                       :class="pub ? 'cursor-pointer' : 'opacity-50 cursor-not-allowed'">
                    <div>
                        <div class="text-sm font-medium">Submit anonymously</div>
                        <p class="text-xs text-ink-muted mt-0.5">Hide your name on the public board. Pastors will still know it's you.</p>
                    </div>
                    <input type="hidden" name="is_anonymous" value="0">
                    <input type="checkbox" name="is_anonymous" value="1" x-model="anon" :disabled="!pub" class="mt-1 h-5 w-5 accent-brand-primary" />
                </label>
            </div>

            <div class="flex flex-wrap gap-2 pt-2">
                <button type="submit" class="btn-primary text-sm">Submit prayer request</button>
                <a href="{{ route('member.prayer.index') }}" class="btn-ghost text-sm">Cancel</a>
            </div>
        </form>
    </div>
</x-member.layout>
