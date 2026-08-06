<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in | Church CMS Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen bg-[#F7F5F1] font-sans text-ink">
    <div class="min-h-screen lg:grid lg:grid-cols-2">
        {{-- Left brand panel --}}
        <aside class="hidden lg:flex flex-col justify-between bg-brand-primary text-white p-12 relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-brand-secondary/10"></div>
            <div class="absolute -bottom-32 -left-16 w-80 h-80 rounded-full bg-white/[0.04]"></div>

            <div class="relative flex items-center gap-3">
                <div class="w-11 h-11 rounded-lg bg-brand-secondary text-ink flex items-center justify-center font-serif text-xl">A</div>
                <div class="leading-tight">
                    <div class="font-semibold tracking-wide">Assemblies of God</div>
                    <div class="text-xs text-white/60 uppercase tracking-wider">Rawalpindi · Admin</div>
                </div>
            </div>

            <div class="relative max-w-md">
                <svg class="w-10 h-10 text-brand-secondary mb-6" viewBox="0 0 24 24" fill="currentColor"><path d="M9.6 6.7c-.3 0-2.1.2-3.6 1.7C3.7 10.7 3 14 3 16.8c0 .3 0 .5.5.5h3.7c.4 0 .5-.2.5-.5v-3.2c0-1 .2-1.7.7-2.2.5-.5 1.1-.7 1.8-.7.4 0 .5-.2.5-.5V7.2c0-.3-.1-.5-.5-.5Zm10 0c-.3 0-2.1.2-3.6 1.7-2.3 2.3-3 5.6-3 8.4 0 .3 0 .5.5.5h3.7c.4 0 .5-.2.5-.5v-3.2c0-1 .2-1.7.7-2.2.5-.5 1.1-.7 1.8-.7.4 0 .5-.2.5-.5V7.2c0-.3-.1-.5-.5-.5Z"/></svg>
                <p class="font-serif text-2xl leading-snug mb-4">"For where two or three gather in my name, there am I with them."</p>
                <p class="text-sm text-white/70">— Matthew 18:20</p>
            </div>

            <div class="relative text-xs text-white/50">
                &copy; {{ date('Y') }} Assemblies of God. Built with care.
            </div>
        </aside>

        {{-- Right form panel --}}
        <main class="flex items-center justify-center p-6 lg:p-12">
            <div class="w-full max-w-md">
                <div class="lg:hidden mb-8 flex items-center gap-3">
                    <div class="w-11 h-11 rounded-lg bg-brand-primary text-white flex items-center justify-center font-serif text-xl">A</div>
                    <div class="leading-tight">
                        <div class="font-semibold">Assemblies of God</div>
                        <div class="text-xs text-ink-muted">Rawalpindi · Admin</div>
                    </div>
                </div>

                <h1 class="text-2xl font-serif mb-1">Welcome back</h1>
                <p class="text-sm text-ink-muted mb-8">Sign in to manage your church website.</p>

                @if ($errors->any())
                    <div class="mb-4 bg-red-50 text-red-700 border border-red-200 p-3 rounded text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form class="space-y-5" method="POST" action="{{ route('admin.login') }}">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium mb-1.5">Email</label>
                        <input id="email" name="email" type="email" class="input" placeholder="you@church.local" value="{{ old('email') }}" autocomplete="username" required autofocus>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-sm font-medium">Password</label>
                            <a href="{{ route('admin.password.request') }}" class="text-xs text-brand-primary hover:underline">Forgot password?</a>
                        </div>
                        <input id="password" name="password" type="password" class="input" placeholder="••••••••" autocomplete="current-password" required>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-ink-muted">
                        <input type="checkbox" name="remember" value="1" class="rounded border-[rgb(var(--border))] text-brand-primary focus:ring-brand-primary">
                        Keep me signed in for 30 days
                    </label>

                    <button type="submit" class="btn-primary w-full">Sign in</button>
                </form>

                <p class="text-xs text-ink-muted text-center mt-8">
                    Trouble signing in? Contact your administrator at
                    <a href="mailto:support@grace.local" class="text-brand-primary hover:underline">support@grace.local</a>.
                </p>
            </div>
        </main>
    </div>
    <x-sweet-alert />
</body>
</html>
