<x-member.layout title="Check in">
    <div class="max-w-md mx-auto px-4 py-12">
        <h1 class="font-serif text-2xl mb-2">Check in to an event</h1>
        <p class="text-ink-muted text-sm mb-8">Enter the 4-digit code shown at the door.</p>

        @if($errors->any())
            <div class="mb-4 p-3 rounded bg-red-50 border border-red-200 text-red-800 text-sm">
                {{ $errors->first('code') }}
            </div>
        @endif

        <form method="POST" action="{{ route('member.checkin.submit') }}"
              x-data="{ d:['','','',''], code(){ return this.d.join('') } }"
              class="flex flex-col items-center gap-6">
            @csrf
            <input type="hidden" name="code" :value="code()">

            <div class="flex gap-3" @keydown.backspace="
                const idx = Array.from($event.target.parentNode.children).indexOf($event.target);
                if (!$event.target.value && idx > 0) $event.target.parentNode.children[idx-1].focus();
            ">
                <template x-for="(_, i) in 4" :key="i">
                    <input type="text" inputmode="numeric" maxlength="1"
                           x-model="d[i]"
                           class="w-14 h-16 text-center text-2xl font-semibold border border-[rgb(var(--border))] rounded-lg focus:border-brand-primary focus:outline-none"
                           @input="
                               d[i] = $event.target.value.replace(/[^0-9]/g,'');
                               if (d[i] && i < 3) $event.target.parentNode.children[i+1].focus();
                           "
                           x-init="if(i===0) $nextTick(() => $el.focus())">
                </template>
            </div>

            <button type="submit" class="btn-primary w-full" :disabled="code().length !== 4" :class="code().length !== 4 && 'opacity-50'">Check in</button>
        </form>

        <p class="text-sm text-ink-muted text-center mt-8">
            If the code doesn't work, ask an organizer to add you manually.
        </p>
    </div>
</x-member.layout>
