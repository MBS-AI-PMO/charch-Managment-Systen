<div class="grid lg:grid-cols-12 gap-4 lg:gap-6">
    <div class="lg:col-span-7 card p-5 md:p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium mb-1.5">Template</label>
            <div class="grid sm:grid-cols-3 gap-2.5">
                @foreach($templates as $key => $meta)
                    <label class="relative cursor-pointer">
                        <input
                            type="radio"
                            name="template"
                            value="{{ $key }}"
                            class="peer sr-only"
                            @checked(old('template', $certificate->template) === $key)
                            required
                        >
                        <span class="block h-full rounded-xl border border-[rgb(var(--border))] bg-surface/70 px-3 py-3 transition peer-checked:border-brand-primary peer-checked:bg-brand-primary/5 peer-checked:ring-1 peer-checked:ring-brand-primary/25 hover:border-brand-primary/30">
                            <span class="block text-sm font-medium text-ink leading-snug">{{ $meta['label'] }}</span>
                            @if(!empty($meta['description']))
                                <span class="block text-xs text-ink-muted mt-1.5 leading-relaxed">{{ $meta['description'] }}</span>
                            @endif
                        </span>
                    </label>
                @endforeach
            </div>
            @error('template')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium mb-1.5">Recipient name</label>
                <input type="text" name="recipient_name" class="input" value="{{ old('recipient_name', $certificate->recipient_name) }}" placeholder="Full name on the certificate" required>
                @error('recipient_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5">Assigned by</label>
                <input type="text" name="assigned_by" class="input" value="{{ old('assigned_by', $certificate->assigned_by) }}" placeholder="Pastor / church name" required>
                @error('assigned_by')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5">Date</label>
                <input type="date" name="issued_on" class="input" value="{{ old('issued_on', optional($certificate->issued_on)->format('Y-m-d') ?? $certificate->issued_on) }}" required>
                @error('issued_on')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium mb-1.5">Title / purpose <span class="text-ink-muted font-normal">(optional)</span></label>
                <input type="text" name="title" class="input" value="{{ old('title', $certificate->title) }}" placeholder="e.g. Youth Leadership Training">
                @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                <p class="text-xs text-ink-muted mt-1">This appears in the certificate body (about 3 lines of text).</p>
            </div>
        </div>

        <div class="rounded-xl border border-[rgb(var(--border))] bg-surface/60 p-4 space-y-3">
            <div>
                <p class="text-xs uppercase tracking-wide text-brand-primary font-semibold">Assign to member</p>
                <p class="text-sm text-ink-muted mt-1">
                    @if($certificate->exists)
                        Pick a member to send this certificate to their dashboard. Leave unchanged to keep the current assignment.
                    @else
                        Optionally assign this certificate when you create it — they’ll see it on their member dashboard.
                    @endif
                </p>
            </div>

            @if($certificate->exists && $certificate->isSent())
                <div class="flex items-start gap-3 rounded-lg border border-emerald-200 bg-emerald-50/70 px-3 py-2.5 text-sm">
                    <span class="mt-0.5 inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold">✓</span>
                    <div class="min-w-0">
                        <div class="font-medium text-ink">Currently with {{ $certificate->user?->name }}</div>
                        <div class="text-xs text-ink-muted truncate">{{ $certificate->user?->email }} · sent {{ $certificate->sent_at?->format('M j, Y g:i A') }}</div>
                    </div>
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium mb-1.5">Registered member</label>
                @php
                    $selectedMemberId = old('user_id', $certificate->exists ? '' : $certificate->user_id);
                @endphp
                <select name="user_id" class="input">
                    @if($certificate->exists)
                        <option value="" @selected($selectedMemberId === '' || $selectedMemberId === null)>Keep current assignment</option>
                    @else
                        <option value="" @selected($selectedMemberId === '' || $selectedMemberId === null)>Don’t assign yet</option>
                    @endif
                    @foreach(($members ?? collect()) as $member)
                        <option value="{{ $member->id }}" @selected((string) $selectedMemberId === (string) $member->id)>
                            {{ $member->name }} — {{ $member->email }}
                        </option>
                    @endforeach
                </select>
                @error('user_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                @if(($members ?? collect())->isEmpty())
                    <p class="mt-1 text-xs text-ink-muted">No registered members found yet.</p>
                @elseif($certificate->exists)
                    <p class="mt-1 text-xs text-ink-muted">Choosing a different member moves the certificate to their dashboard and adds a history entry.</p>
                @endif
            </div>
        </div>
    </div>

    <aside class="lg:col-span-5 card p-5 md:p-6 bg-surface/40">
        <p class="text-xs uppercase tracking-[0.16em] text-brand-primary font-semibold">Tips</p>
        <h2 class="font-serif text-xl mt-1.5 text-ink">Make it look complete</h2>
        <ul class="mt-4 space-y-3 text-sm text-ink-muted leading-relaxed">
            <li>Use the recipient’s full name exactly as it should appear on print.</li>
            <li>Add a clear title/purpose so the certificate body reads naturally in 2–3 lines.</li>
            <li>Assign to a registered member on create, or change the member when editing.</li>
            <li>After saving, use <strong class="text-ink font-medium">Preview</strong> then <strong class="text-ink font-medium">Print</strong> (A4 landscape).</li>
        </ul>
    </aside>
</div>
