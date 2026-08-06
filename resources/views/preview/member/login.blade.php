<x-member.layout title="Sign in">
    <div class="min-h-[calc(100vh-6rem)] grid grid-cols-1 lg:grid-cols-2">
        <div class="hidden lg:flex bg-brand-secondary/30 items-center justify-center p-12 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-brand-secondary/40 via-transparent to-brand-primary/20"></div>
            <div class="relative max-w-md">
                <h2 class="font-serif text-4xl mb-4 text-ink">Welcome back.</h2>
                <p class="text-ink-muted leading-relaxed">Sign in to access your prayer requests, the community feed, and your member dashboard.</p>
            </div>
        </div>

        <div class="flex items-center justify-center p-6 md:p-12">
            <div class="w-full max-w-md">
                <h1 class="font-serif text-3xl md:text-4xl mb-2">Sign in to your account</h1>
                <p class="text-sm text-ink-muted mb-6">New here? <a href="{{ route('preview.member.register') }}" class="text-brand-primary hover:underline">Create an account</a>.</p>

                <form class="card p-6 md:p-7 space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input class="input w-full" type="email" required autocomplete="email" />
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-sm font-medium">Password</label>
                            <a href="#" class="text-xs text-brand-primary hover:underline">Forgot password?</a>
                        </div>
                        <input class="input w-full" type="password" required autocomplete="current-password" />
                    </div>
                    <label class="flex items-center gap-2 text-sm text-ink-muted">
                        <input type="checkbox" />
                        <span>Remember me on this device</span>
                    </label>

                    <button type="button" class="btn-primary w-full">Sign in</button>
                </form>

                <p class="text-xs text-ink-muted text-center mt-6">Looking for the admin area? <a href="#" class="text-brand-primary hover:underline">Admin sign in</a>.</p>
            </div>
        </div>
    </div>
</x-member.layout>
