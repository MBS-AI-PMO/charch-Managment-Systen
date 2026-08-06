<x-member.layout title="Thank you">
    <div class="max-w-xl mx-auto px-4 py-20">
        <div class="card p-10 text-center">
            <div class="w-16 h-16 mx-auto rounded-full bg-emerald-50 flex items-center justify-center mb-5">
                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h1 class="font-serif text-3xl mb-3">Thank you</h1>
            <p class="text-ink-muted leading-relaxed">A pastor will reach out to you within 24 hours. If your situation is urgent, please call the church office directly at <a href="tel:{{ settings('contact.phone','') }}" class="text-brand-primary font-medium">{{ settings('contact.phone', 'the number on the contact page') }}</a>.</p>
            <div class="mt-8 flex flex-wrap gap-2 justify-center">
                <a href="{{ route('member.dashboard') }}" class="btn-primary text-sm">Back to dashboard</a>
                <a href="{{ route('member.care.index') }}" class="btn-ghost text-sm">View my requests</a>
            </div>
        </div>
    </div>
</x-member.layout>
