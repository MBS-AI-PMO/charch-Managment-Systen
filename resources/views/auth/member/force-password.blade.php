@extends('layouts.site')

@section('title', 'Choose a new password')

@section('content')
@php
  $brand = $brandName ?? (function_exists('settings') ? settings('brand.name', 'Assemblies of God') : 'Assemblies of God');
  $tagline = $brandTagline ?? (function_exists('settings') ? settings('brand.tagline', 'Rawalpindi') : 'Rawalpindi');
  $initial = mb_strtoupper(mb_substr($brand, 0, 1));
@endphp
<main class="min-h-screen flex items-center justify-center px-4 py-16 bg-surface">
    <div class="w-full max-w-md card p-8">
        <div class="text-center mb-8">
            <span class="inline-flex items-center gap-2.5 mb-6">
                @if($brandLogoUrl ?? null)
                    <img src="{{ $brandLogoUrl }}" alt="{{ $brand }}" class="h-10 w-auto max-w-[120px] object-contain">
                @else
                    <span class="w-10 h-10 rounded-lg bg-brand-primary text-white flex items-center justify-center font-serif text-lg shrink-0">{{ $initial }}</span>
                @endif
                <span class="text-left leading-tight">
                    <span class="block font-serif text-xl">{{ $brand }}</span>
                    @if($tagline)
                        <span class="block text-xs text-ink-muted">{{ $tagline }}</span>
                    @endif
                </span>
            </span>
            <h1 class="font-serif text-3xl">Set a new password</h1>
            <p class="text-ink-muted text-sm mt-2">For your security, please choose a new password before continuing. Minimum 12 characters with upper, lower, number and symbol.</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 bg-red-50 text-red-700 border border-red-200 p-3 rounded text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.force.update') }}" class="space-y-5">
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
    </div>
</main>
@endsection
