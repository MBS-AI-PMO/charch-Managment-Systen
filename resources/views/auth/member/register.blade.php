@php($registrationEnabled = (string) settings('member.registration_enabled', '1') === '1')
<x-member.layout title="Create account">
    <div class="min-h-[calc(100vh-6rem)] grid grid-cols-1 lg:grid-cols-2">
        <div class="hidden lg:flex bg-brand-secondary/30 items-center justify-center p-12 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-brand-secondary/40 via-transparent to-brand-primary/20"></div>
            <div class="relative max-w-md">
                <h2 class="font-serif text-4xl mb-4 text-ink">Welcome home.</h2>
                <p class="text-ink-muted leading-relaxed">Create an account to join our prayer wall, share with the community, and stay close to what God is doing among us.</p>
                <ul class="mt-8 space-y-3 text-sm text-ink/80">
                    <li class="flex items-start gap-2"><span class="text-brand-primary">&#10003;</span> Submit prayer requests &mdash; private or public.</li>
                    <li class="flex items-start gap-2"><span class="text-brand-primary">&#10003;</span> Knock for help when you need pastoral care.</li>
                    <li class="flex items-start gap-2"><span class="text-brand-primary">&#10003;</span> Follow our community feed.</li>
                </ul>
            </div>
        </div>

        <div class="flex items-center justify-center p-6 md:p-12">
            <div class="w-full max-w-md">
                <h1 class="font-serif text-3xl md:text-4xl mb-2">Create your account</h1>
                <p class="text-sm text-ink-muted mb-6">Already a member? <a href="{{ route('login') }}" class="text-brand-primary hover:underline">Sign in</a>.</p>

                @unless($registrationEnabled)
                    <div class="mb-4 px-4 py-3 rounded-lg border border-amber-200 bg-amber-50 text-amber-800 text-sm">
                        Registration is currently disabled. Please <a href="/contact" class="underline">contact us</a> to request an account.
                    </div>
                @endunless

                @if ($errors->any())
                    <div class="mb-4 px-4 py-3 rounded-lg border border-rose-200 bg-rose-50 text-rose-800 text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="card p-6 md:p-7 space-y-4">
                    @csrf

                    {{-- Honeypot — bots fill all fields, humans never see this. --}}
                    <div class="hidden" aria-hidden="true">
                        <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off" /></label>
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium mb-1">Full name</label>
                        <input id="name" name="name" class="input w-full" type="text" required autocomplete="name"
                               value="{{ old('name') }}" {{ $registrationEnabled ? '' : 'disabled' }} />
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium mb-1">Email</label>
                        <input id="email" name="email" class="input w-full" type="email" required autocomplete="email"
                               value="{{ old('email') }}" {{ $registrationEnabled ? '' : 'disabled' }} />
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium mb-1">Password</label>
                        <input id="password" name="password" class="input w-full" type="password" required
                               autocomplete="new-password" {{ $registrationEnabled ? '' : 'disabled' }} />
                        <p class="text-xs text-ink-muted mt-1">Minimum 8 characters.</p>
                        @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium mb-1">Confirm password</label>
                        <input id="password_confirmation" name="password_confirmation" class="input w-full" type="password"
                               required autocomplete="new-password" {{ $registrationEnabled ? '' : 'disabled' }} />
                    </div>

                    <button type="submit" class="btn-primary w-full" {{ $registrationEnabled ? '' : 'disabled' }}>
                        Create account
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-member.layout>
