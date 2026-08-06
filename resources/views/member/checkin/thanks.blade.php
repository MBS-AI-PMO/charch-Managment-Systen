<x-member.layout title="Checked in">
    <div class="max-w-md mx-auto px-4 py-16 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-brand-secondary/15 text-brand-secondary flex items-center justify-center text-3xl">✓</div>
        <h1 class="font-serif text-2xl mb-2">{{ session('status', 'You\'re checked in') }}</h1>
        <p class="text-ink-muted mb-6">{{ $attendance->event->title }} · {{ $attendance->event->starts_at?->format('D, M j · g:i A') }}</p>

        <a href="{{ route('member.dashboard') }}" class="btn-primary">Back to dashboard</a>
    </div>
</x-member.layout>
