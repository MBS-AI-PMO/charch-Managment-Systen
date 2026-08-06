@php
  $submitUrl = \Illuminate\Support\Facades\Route::has('site.contact.submit')
    ? route('site.contact.submit')
    : url('/contact');
  $openOnLoad = $errors->any() && old('source') === 'ask_question';
  $member = auth('web')->user();
  $historyUrl = ($member && \Illuminate\Support\Facades\Route::has('member.questions.index'))
    ? route('member.questions.index')
    : null;
  $loginUrl = \Illuminate\Support\Facades\Route::has('login') ? route('login') : null;
@endphp

<div
  x-data="{ open: {{ $openOnLoad ? 'true' : 'false' }} }"
  x-effect="document.body.classList.toggle('overflow-hidden', open)"
  @keydown.escape.window="if (open) open = false"
  @open-ask-question.window="open = true"
>
  <template x-teleport="body">
    <div
      x-show="open"
      x-cloak
      class="fixed inset-0 z-[100] flex items-center justify-center p-2 sm:p-4"
      role="dialog"
      aria-modal="true"
      aria-labelledby="ask-question-title"
    >
      <div
        class="absolute inset-0 bg-ink/40"
        x-show="open"
        x-transition.opacity.duration.200ms
        @click="open = false"
      ></div>

      <div
        class="relative w-full max-w-[26rem] bg-surface-elevated rounded-2xl shadow-2xl border-x border-b border-[rgb(var(--border))] overflow-hidden max-h-[calc(100dvh-1rem)] sm:max-h-[min(86vh,34rem)] flex flex-col my-auto"
        x-show="open"
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="opacity-0 translate-y-4 scale-[0.98]"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-3 scale-[0.98]"
        @click.stop
      >
        {{-- Accent bar flush to top edge (no top border above it) --}}
        <div class="h-1 w-full sm:h-1.5 bg-gradient-to-r from-brand-primary via-brand-primary to-brand-secondary shrink-0"></div>

        <div class="flex items-start justify-between gap-3 px-4 pt-2.5 pb-1.5 sm:px-5 sm:pt-4 sm:pb-3 shrink-0">
          <div class="flex items-start gap-3 min-w-0">
            <div class="hidden sm:flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-brand-primary/10 text-brand-primary">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8.25 6.75h7.5M8.25 12h4.5m-8.25 6.75 2.25-2.25H18a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 18 4.5H6A2.25 2.25 0 0 0 3.75 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25Z"/>
              </svg>
            </div>
            <div class="min-w-0">
              <p class="text-[10px] uppercase tracking-[0.14em] text-brand-primary font-semibold">We're listening</p>
              <h2 id="ask-question-title" class="font-serif text-lg sm:text-xl mt-0.5 text-ink leading-tight">Ask a question</h2>
              <p class="text-[11px] sm:text-xs text-ink-muted mt-0.5 sm:mt-1 leading-snug sm:leading-relaxed">We’ll usually reply within one business day.</p>
            </div>
          </div>
          <button
            type="button"
            @click="open = false"
            class="shrink-0 -mr-1 mt-0.5 p-1.5 rounded-lg text-ink-muted hover:text-ink hover:bg-surface transition-colors"
            aria-label="Close"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <div class="px-4 pb-3 sm:px-5 sm:pb-5 overflow-y-auto overflow-x-hidden overscroll-contain">
          <form method="post" action="{{ $submitUrl }}" class="space-y-2 sm:space-y-3.5 relative">
            @csrf
            <input type="hidden" name="source" value="ask_question">
            <input type="hidden" name="subject" value="Ask a question">

            <div class="hidden" aria-hidden="true">
              <label>Leave this empty<input type="text" name="website" tabindex="-1" autocomplete="off" value=""></label>
            </div>

            <div class="grid grid-cols-2 gap-2 sm:gap-3">
              <div class="min-w-0">
                <label for="ask-name" class="ask-label">Name</label>
                <input
                  id="ask-name"
                  type="text"
                  name="name"
                  value="{{ old('source') === 'ask_question' ? old('name', $member?->name) : ($member?->name ?? '') }}"
                  class="ask-field"
                  placeholder="Your full name"
                  required
                  autocomplete="name"
                />
                @if(old('source') === 'ask_question')
                  @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                @endif
              </div>
              <div class="min-w-0">
                <label for="ask-email" class="ask-label">Email</label>
                <input
                  id="ask-email"
                  type="email"
                  name="email"
                  value="{{ old('source') === 'ask_question' ? old('email', $member?->email) : ($member?->email ?? '') }}"
                  class="ask-field"
                  placeholder="you@example.com"
                  required
                  autocomplete="email"
                />
                @if(old('source') === 'ask_question')
                  @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                @endif
              </div>
            </div>

            <div class="min-w-0">
              <label for="ask-phone" class="ask-label">Phone <span class="font-normal text-ink-muted">(optional)</span></label>
              <input
                id="ask-phone"
                type="tel"
                name="phone"
                value="{{ old('source') === 'ask_question' ? old('phone') : '' }}"
                class="ask-field"
                placeholder="Mobile or home number"
                autocomplete="tel"
              />
              @if(old('source') === 'ask_question')
                @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              @endif
            </div>

            <div class="min-w-0">
              <label for="ask-message" class="ask-label">Your question</label>
              <textarea
                id="ask-message"
                name="message"
                rows="3"
                class="ask-field resize-none min-h-[64px] sm:min-h-[96px]"
                placeholder="Write your question clearly…"
                required
              >{{ old('source') === 'ask_question' ? old('message') : '' }}</textarea>
              @if(old('source') === 'ask_question')
                @error('message')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                @error('subject')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              @endif
            </div>

            <div class="rounded-lg bg-surface border border-[rgb(var(--border))] px-3 py-2 sm:py-2.5 text-[11px] text-ink-muted leading-snug sm:leading-relaxed">
              @if($historyUrl)
                Replies appear in
                <a href="{{ $historyUrl }}" class="font-medium text-brand-primary hover:underline">My questions</a>
                so you can revisit them anytime.
              @elseif($loginUrl)
                <a href="{{ $loginUrl }}" class="font-medium text-brand-primary hover:underline">Sign in</a>
                with the same email later to track replies in your history.
              @else
                We will email you when our team replies.
              @endif
            </div>

            <div class="flex flex-row items-center justify-end gap-2 sm:gap-2.5 pt-0.5 sm:pt-1">
              <button type="button" @click="open = false" class="btn-ghost flex-1 sm:flex-none sm:w-auto text-sm px-4 py-2 sm:py-2.5">
                Cancel
              </button>
              <button type="submit" class="ask-submit btn-primary shine-btn glow-primary flex-1 sm:flex-none sm:w-auto text-sm px-5 py-2 sm:py-2.5">
                <span>Submit question</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </template>
</div>
