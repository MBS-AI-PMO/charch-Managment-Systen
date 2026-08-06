@php
    $going = $goingCount ?? 0;
    $names = $goingPreview ?? [];
    $extra = max(0, $going - count($names));
@endphp

<section class="mt-8 md:mt-10 p-5 md:p-8 rounded-xl border border-[rgb(var(--border))] bg-white" id="rsvp">
    <h3 class="font-serif text-xl mb-4">RSVP for this event</h3>

    @guest('web')
        <p class="text-ink-muted text-sm mb-4">Sign in to let us know you're coming.</p>
        <a href="{{ route('login') }}?return={{ urlencode(request()->fullUrl()) }}" class="btn-primary">Sign in to RSVP</a>
    @else
        @if($myRsvp && $myRsvp->status === 'going')
            <p class="text-sm mb-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-brand-secondary/15 text-brand-secondary text-xs font-semibold">✓ You're coming</span>
            </p>
            @if($myRsvp->guest_count > 0)
                <p class="text-sm text-ink-muted mb-3">Bringing {{ $myRsvp->guest_count }} guest{{ $myRsvp->guest_count === 1 ? '' : 's' }}.</p>
            @endif
            <details class="text-sm mb-3">
                <summary class="cursor-pointer text-brand-primary">Edit RSVP</summary>
                <form method="POST" action="{{ route('member.events.rsvp', $event) }}" class="mt-3 flex flex-col gap-3">
                    @csrf
                    <label class="flex items-center gap-3">
                        <span class="text-sm">Guests (incl. you):</span>
                        <select name="guest_count" class="border rounded px-2 py-1">
                            @for($i = 0; $i <= 5; $i++)
                                <option value="{{ $i }}" @selected($myRsvp->guest_count === $i)>{{ $i }}</option>
                            @endfor
                        </select>
                    </label>
                    <textarea name="note" rows="2" class="border rounded p-2 text-sm" placeholder="Note (optional)">{{ $myRsvp->note }}</textarea>
                    <button class="btn-primary self-start">Save changes</button>
                </form>
            </details>
            <form method="POST" action="{{ route('member.events.cancel-rsvp', $event) }}">
                @csrf @method('DELETE')
                <button class="text-sm text-brand-primary underline">Cancel RSVP</button>
            </form>
        @else
            <form method="POST" action="{{ route('member.events.rsvp', $event) }}" class="flex flex-col gap-3">
                @csrf
                <label class="flex items-center gap-3">
                    <span class="text-sm">Bringing guests?</span>
                    <select name="guest_count" class="border rounded px-2 py-1">
                        @for($i = 0; $i <= 5; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </label>
                <details class="text-sm">
                    <summary class="cursor-pointer text-brand-primary">Add a note (optional)</summary>
                    <textarea name="note" rows="2" class="border rounded p-2 text-sm w-full mt-2" maxlength="500"></textarea>
                </details>
                <button class="btn-primary self-start">I'll be there</button>
            </form>
        @endif
    @endguest

    @if($going > 0)
        <div class="mt-6 pt-4 border-t border-[rgb(var(--border))] text-sm text-ink-muted">
            <strong class="text-ink">{{ $going }}</strong> {{ Str::plural('person', $going) }} {{ $going === 1 ? 'is' : 'are' }} coming
            @if(count($names))
                — {{ implode(', ', $names) }}@if($extra) and {{ $extra }} {{ Str::plural('other', $extra) }}@endif.
            @endif
        </div>
    @endif
</section>
