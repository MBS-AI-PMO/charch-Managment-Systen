<x-admin.layout title="Message from {{ $message->name }}">
    <div class="space-y-6 w-full">
        {{-- Header --}}
        <div class="flex items-end justify-between flex-wrap gap-4">
            <div>
                <div class="text-xs text-ink-muted mb-1">
                    <a href="{{ route('admin.messages.index') }}" class="hover:underline">Inbox</a>
                    <span class="mx-1">/</span>
                    <span>Message #{{ $message->id }}</span>
                </div>
                <h1 class="text-2xl font-semibold tracking-tight text-ink">{{ $message->subject ?? '(no subject)' }}</h1>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                @if($message->replied_at)
                    <span class="inline-flex px-3 py-1 rounded-full text-xs bg-emerald-50 text-emerald-700 font-medium">Replied</span>
                    @if($message->is_public)
                        <span class="inline-flex px-3 py-1 rounded-full text-xs bg-blue-50 text-blue-700 font-medium">On Q&A page</span>
                    @endif
                @else
                    <span class="inline-flex px-3 py-1 rounded-full text-xs bg-amber-50 text-amber-700 font-medium">Awaiting reply</span>
                @endif
                <a href="mailto:{{ $message->email }}?subject=Re: {{ urlencode($message->subject ?? '') }}" class="btn-ghost text-sm">Email directly</a>
                @can('manage-messages')
                    @if($message->replied_at)
                        <form method="POST" action="{{ route('admin.messages.toggle-public', $message) }}">
                            @csrf
                            <button class="btn-ghost text-sm">
                                {{ $message->is_public ? 'Unpublish from Q&A' : 'Publish to Q&A' }}
                            </button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" onsubmit="return confirm('Delete this message?')">
                        @csrf @method('DELETE')
                        <x-row-action type="delete" />
                    </form>
                @endcan
            </div>
        </div>

        <div class="grid xl:grid-cols-5 gap-6 items-start">
            {{-- Left: question + conversation --}}
            <div class="xl:col-span-3 space-y-6 min-w-0">
                {{-- Sender meta --}}
                <div class="card p-5 md:p-6">
                    <div class="flex items-start gap-4 flex-wrap">
                        @php
                            $initials = collect(preg_split('/\s+/', trim($message->name) ?: 'A'))
                                ->filter()
                                ->take(2)
                                ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                                ->implode('');
                        @endphp
                        <div class="w-12 h-12 rounded-full bg-brand-primary/10 text-brand-primary border border-brand-primary/15 flex items-center justify-center text-sm font-semibold shrink-0">
                            {{ $initials ?: 'A' }}
                        </div>
                        <div class="min-w-0 flex-1 grid sm:grid-cols-2 gap-4">
                            <div>
                                <div class="text-[11px] uppercase tracking-wider text-ink-muted font-semibold">From</div>
                                <div class="font-medium text-ink mt-1">{{ $message->name }}</div>
                                <div class="text-ink-muted text-sm mt-0.5 break-all">{{ $message->email }}</div>
                                @if($message->phone)
                                    <div class="text-ink-muted text-sm">{{ $message->phone }}</div>
                                @endif
                                @if($message->user_id)
                                    <div class="text-xs text-brand-primary mt-1.5 font-medium">Linked member account</div>
                                @endif
                            </div>
                            <div>
                                <div class="text-[11px] uppercase tracking-wider text-ink-muted font-semibold">Received</div>
                                <div class="font-medium text-ink mt-1">{{ $message->created_at?->format('M j, Y g:i A') }}</div>
                                <div class="text-ink-muted text-sm mt-0.5">{{ $message->created_at?->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Question --}}
                <div class="card overflow-hidden border-brand-primary/15">
                    <div class="px-5 md:px-6 py-3 bg-brand-primary/5 border-b border-brand-primary/10 flex items-center gap-2">
                        <span class="inline-flex w-7 h-7 rounded-lg bg-brand-primary/10 text-brand-primary items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                        <div>
                            <div class="text-[11px] uppercase tracking-wider text-brand-primary font-semibold">Question</div>
                            <div class="text-xs text-ink-muted">Visitor’s message</div>
                        </div>
                    </div>
                    <div class="p-5 md:p-6">
                        <p class="whitespace-pre-wrap text-base leading-relaxed text-ink">{{ $message->message }}</p>
                    </div>
                </div>

                {{-- Existing replies --}}
                @if($message->replies->isNotEmpty())
                    <div class="card overflow-hidden">
                        <div class="px-5 md:px-6 py-3 bg-emerald-50/80 border-b border-emerald-100 flex items-center gap-2">
                            <span class="inline-flex w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </span>
                            <div>
                                <div class="text-[11px] uppercase tracking-wider text-emerald-700 font-semibold">Answers</div>
                                <div class="text-xs text-ink-muted">{{ $message->replies->count() }} {{ $message->replies->count() === 1 ? 'reply' : 'replies' }} in this conversation</div>
                            </div>
                        </div>
                        <ul class="divide-y divide-[rgb(var(--border))]">
                            @foreach($message->replies as $reply)
                                <li class="p-5 md:p-6">
                                    <div class="flex items-center justify-between gap-3 mb-3 flex-wrap">
                                        <div class="flex items-center gap-2.5">
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-medium {{ $reply->is_staff ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                                {{ $reply->is_staff ? 'Staff reply' : 'Visitor' }}
                                            </span>
                                            <span class="text-sm font-medium text-ink">
                                                {{ $reply->is_staff ? ($reply->author?->name ?? 'Staff') : $message->name }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-ink-muted whitespace-nowrap">{{ $reply->created_at?->format('M j, Y g:i A') }}</div>
                                    </div>
                                    <div class="rounded-xl border border-[rgb(var(--border))] bg-surface px-4 py-3.5">
                                        <p class="whitespace-pre-wrap text-sm leading-relaxed text-ink">{{ $reply->body }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            {{-- Right: reply form --}}
            <div class="xl:col-span-2 xl:sticky xl:top-24">
                @can('manage-messages')
                    <form method="POST" action="{{ route('admin.messages.reply', $message) }}" class="card p-5 md:p-6 space-y-4 border-brand-primary/10">
                        @csrf
                        <div>
                            <h2 class="text-base font-semibold text-ink">{{ $message->replied_at ? 'Send another reply' : 'Write your answer' }}</h2>
                            <p class="text-xs text-ink-muted mt-1 leading-relaxed">
                                Saved to their history and emailed to <span class="font-medium text-ink">{{ $message->email }}</span>.
                            </p>
                        </div>
                        <div>
                            <label for="reply-body" class="block text-[11px] uppercase tracking-wider text-ink-muted font-semibold mb-1.5">Your answer</label>
                            <textarea id="reply-body" name="body" rows="10" class="input min-h-[12rem]" placeholder="Write a clear, pastoral reply…" required>{{ old('body') }}</textarea>
                            @error('body')<p class="mt-1 text-sm text-red-600">{{ $errors->first('body') }}</p>@enderror
                        </div>
                        <label class="flex items-start gap-3 cursor-pointer rounded-xl border border-[rgb(var(--border))] bg-surface p-3.5">
                            <input type="hidden" name="is_public" value="0">
                            <input
                                type="checkbox"
                                name="is_public"
                                value="1"
                                class="mt-0.5"
                                @checked(old('is_public', $message->is_public || ! $message->replied_at))
                            >
                            <span>
                                <span class="block text-sm font-medium">Show on public Q&A page</span>
                                <span class="block text-xs text-ink-muted mt-0.5 leading-relaxed">Only name, question, and answer — no email or phone.</span>
                            </span>
                        </label>
                        <button type="submit" class="btn-primary shine-btn glow-primary text-sm w-full">
                            <span>Send reply</span>
                        </button>
                    </form>
                @else
                    <div class="card p-6 text-sm text-ink-muted">You do not have permission to reply.</div>
                @endcan
            </div>
        </div>

        <div>
            <a href="{{ route('admin.messages.index') }}" class="text-xs text-brand-primary hover:underline">&larr; Back to inbox</a>
        </div>
    </div>
</x-admin.layout>
