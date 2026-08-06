<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Choose a new password | Church CMS Admin</title>
    <style>
        :root {
            --brand-primary: {{ $brandPrimaryRgb ?? '122 31 43' }};
            --brand-secondary: {{ $brandSecondaryRgb ?? '201 169 97' }};
        }
        [x-cloak]{display:none!important}
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
  $brand = $brandName ?? 'Assemblies of God';
  $tagline = $brandTagline ?? 'Rawalpindi';
@endphp
<body class="min-h-screen bg-[#F7F5F1] font-sans text-ink">
    <div class="min-h-screen lg:grid lg:grid-cols-2">
        <aside class="hidden lg:flex flex-col justify-between bg-brand-primary text-white p-12 relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-brand-secondary/10"></div>
            <div class="absolute -bottom-32 -left-16 w-80 h-80 rounded-full bg-white/[0.04]"></div>

            <div class="relative flex items-center gap-3">
                @if($brandLogoUrl ?? null)
                    <img src="{{ $brandLogoUrl }}" alt="{{ $brand }}" class="h-11 w-auto max-w-[56px] object-contain rounded-lg bg-white/95 p-0.5">
                @else
                    <div class="w-11 h-11 rounded-lg bg-brand-secondary text-ink flex items-center justify-center font-serif text-xl">{{ mb_strtoupper(mb_substr($brand, 0, 1)) }}</div>
                @endif
                <div class="leading-tight">
                    <div class="font-semibold tracking-wide">{{ $brand }}</div>
                    <div class="text-xs text-white/60 uppercase tracking-wider">{{ $tagline }} · Admin</div>
                </div>
            </div>

            <div class="relative max-w-md">
                <p class="font-serif text-2xl leading-snug mb-4">A fresh password for a safer church.</p>
                <p class="text-sm text-white/70">Choose something strong you have not used elsewhere.</p>
            </div>

            <div class="relative text-xs text-white/50">&copy; {{ date('Y') }} {{ $brand }}.</div>
        </aside>

        <main class="flex items-center justify-center p-6 lg:p-12">
            <div class="w-full max-w-md">
                <h1 class="text-2xl font-serif mb-1">Set a new password</h1>
                <p class="text-sm text-ink-muted mb-8">For your security, we need you to choose a new password before continuing. Minimum 12 characters with upper, lower, number and symbol.</p>

                @if ($errors->any())
                    <div class="mb-4 bg-red-50 text-red-700 border border-red-200 p-3 rounded text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.password.force.update') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="current_password" class="block text-sm font-medium mb-1.5">Current password</label>
                        <input id="current_password" name="current_password" type="password" class="input" autocomplete="current-password" required autofocus>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium mb-1.5">New password</label>
                        <input id="password" name="password" type="password" class="input" autocomplete="new-password" required>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium mb-1.5">Confirm new password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="input" autocomplete="new-password" required>
                    </div>

                    <button type="submit" class="btn-primary w-full">Update password</button>
                </form>

                <form method="POST" action="{{ route('admin.logout') }}" class="mt-6 text-center">
                    @csrf
                    <button type="submit" class="text-xs text-ink-muted hover:underline">Sign out</button>
                </form>
            </div>
        </main>
    </div>
    <x-sweet-alert />
</body>
</html>
