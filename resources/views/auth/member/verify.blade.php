<x-member.layout title="Verify your email">
    <div class="max-w-md mx-auto px-4 py-16">
        <div class="card p-8 text-center">
            <div class="w-14 h-14 mx-auto rounded-full bg-brand-secondary/20 text-brand-primary flex items-center justify-center mb-4">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
            </div>
            <h1 class="font-serif text-2xl md:text-3xl mb-2">Check your email</h1>
            <p class="text-sm text-ink-muted leading-relaxed">
                We just sent a verification link to
                <span class="font-medium text-ink">{{ auth()->user()?->email ?? 'your email' }}</span>.
                Click the link in the email to finish creating your account.
            </p>

            <div class="mt-6 flex flex-wrap gap-2 justify-center">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn-primary text-sm">Resend verification email</button>
                </form>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-ghost text-sm">Sign out</button>
                </form>
            </div>
            <p class="text-xs text-ink-muted mt-6">Can't find the email? Check your spam folder, or contact us if it still hasn't arrived.</p>
        </div>
    </div>
</x-member.layout>
