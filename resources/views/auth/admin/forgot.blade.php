<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset password | Church CMS Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen bg-[#F7F5F1] font-sans text-ink">
    <div class="min-h-screen lg:grid lg:grid-cols-2">
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
                <p class="font-serif text-2xl leading-snug mb-4">"The Lord is near to all who call on him."</p>
                <p class="text-sm text-white/70">— Psalm 145:18</p>
            </div>
            <div class="relative text-xs text-white/50">&copy; {{ date('Y') }} Assemblies of God.</div>
        </aside>

        <main class="flex items-center justify-center p-6 lg:p-12">
            <div class="w-full max-w-md">
                <h1 class="text-2xl font-serif mb-1">Forgot your password?</h1>
                <p class="text-sm text-ink-muted mb-8">Enter your email and we'll send you a reset link.</p>

                @if ($errors->any())
                    <div class="mb-4 bg-red-50 text-red-700 border border-red-200 p-3 rounded text-sm">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('admin.password.email') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium mb-1.5">Email</label>
                        <input id="email" name="email" type="email" class="input" value="{{ old('email') }}" required autofocus>
                    </div>
                    <button type="submit" class="btn-primary w-full">Send reset link</button>
                </form>

                <p class="text-xs text-ink-muted text-center mt-8">
                    <a href="{{ route('admin.login') }}" class="text-brand-primary hover:underline">Back to sign in</a>
                </p>
            </div>
        </main>
    </div>
    <x-sweet-alert />
</body>
</html>
