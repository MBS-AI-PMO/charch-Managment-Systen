@extends('layouts.site')

@section('title', 'Sign in | Members')

@section('content')
@php
  $brand = $brandName ?? (function_exists('settings') ? settings('brand.name', 'Assemblies of God') : 'Assemblies of God');
  $tagline = $brandTagline ?? (function_exists('settings') ? settings('brand.tagline', 'Rawalpindi') : 'Rawalpindi');
  $bgUrl = asset('images/auth-church-bg.jpg');
@endphp
<main class="relative min-h-screen flex items-center justify-center px-4 py-16 overflow-hidden">
    {{-- Soft faded church background --}}
    <div class="absolute inset-0 -z-10" aria-hidden="true">
        <img
            src="{{ $bgUrl }}"
            alt=""
            class="auth-bg-image w-full h-full object-cover"
        >
        <div class="auth-bg-shade absolute inset-0"></div>
    </div>

    <div class="w-full max-w-md card p-8 shadow-lg relative z-10 bg-surface-elevated/95 backdrop-blur-sm">
        <div class="text-center mb-8">
            <a href="/" class="inline-flex flex-col items-center mb-6 group">
                @if($brandLogoUrl ?? null)
                    <img src="{{ $brandLogoUrl }}" alt="{{ $brand }}" class="h-14 w-auto max-w-[120px] object-contain mb-3">
                @endif
                <span class="font-serif text-2xl sm:text-[1.75rem] font-semibold tracking-tight text-ink group-hover:text-brand-primary transition-colors">
                    {{ $brand }}
                </span>
                @if($tagline)
                    <span class="mt-1 text-sm sm:text-base tracking-[0.12em] uppercase text-ink-muted">{{ $tagline }}</span>
                @endif
            </a>
            <h1 class="font-serif text-2xl">Member sign in</h1>
            <p class="text-ink-muted text-xs mt-1.5">Access your member portal.</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 bg-red-50 text-red-700 border border-red-200 p-3 rounded text-sm">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium mb-1.5">Email</label>
                <input id="email" name="email" type="email" class="input" value="{{ old('email') }}" autocomplete="username" required autofocus>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-sm font-medium">Password</label>
                    <a href="{{ route('password.request') }}" class="text-xs text-brand-primary hover:underline">Forgot password?</a>
                </div>
                <input id="password" name="password" type="password" class="input" autocomplete="current-password" required>
            </div>

            <label class="flex items-center gap-2 text-sm text-ink-muted">
                <input type="checkbox" name="remember" value="1" class="rounded border-[rgb(var(--border))] text-brand-primary focus:ring-brand-primary">
                Remember me
            </label>

            <button type="submit" class="btn-primary shine-btn glow-primary w-full"><span>Sign in</span></button>
        </form>

        <p class="text-xs text-ink-muted text-center mt-8">
            New to {{ $brand }}?
            <a href="{{ route('register') }}" class="text-brand-primary hover:underline font-medium">Create an account</a>.
        </p>
    </div>
</main>
@endsection
