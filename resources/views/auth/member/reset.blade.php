@extends('layouts.site')

@section('title', 'Set new password | Members')

@section('content')
@php
  $brand = $brandName ?? (function_exists('settings') ? settings('brand.name', 'Assemblies of God') : 'Assemblies of God');
  $tagline = $brandTagline ?? (function_exists('settings') ? settings('brand.tagline', 'Rawalpindi') : 'Rawalpindi');
@endphp

<x-site.header :nav="$siteNav" />

<section class="auth-page relative overflow-hidden">
  <div class="auth-page-bg" aria-hidden="true"></div>

  <div class="relative max-w-container mx-auto px-4 sm:px-6 py-10 md:py-16">
    <div class="max-w-md mx-auto">
      <div class="contact-panel reveal">
        <div class="flex items-center gap-3 mb-6">
          @if($brandLogoUrl ?? null)
            <img src="{{ $brandLogoUrl }}" alt="{{ $brand }}" class="h-11 w-auto max-w-[64px] object-contain shrink-0">
          @endif
          <div class="leading-tight min-w-0">
            <div class="font-serif text-base font-semibold text-ink truncate">{{ $brand }}</div>
            @if($tagline)
              <div class="text-xs uppercase tracking-[0.12em] text-ink-muted mt-0.5">{{ $tagline }}</div>
            @endif
          </div>
        </div>

        <h1 class="font-serif text-2xl md:text-3xl text-ink">Set a new password</h1>
        <p class="text-sm text-ink-muted mt-1.5">Minimum 8 characters.</p>

        @if ($errors->any())
          <div class="mt-5 px-4 py-3 rounded-lg border border-rose-200 bg-rose-50 text-rose-800 text-sm">
            {{ $errors->first() }}
          </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="mt-7 space-y-5">
          @csrf
          <input type="hidden" name="token" value="{{ $token }}">

          <div>
            <label for="email" class="block text-sm font-medium mb-1.5">Email</label>
            <input id="email" name="email" type="email" class="input" value="{{ old('email', $email) }}" required autofocus>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium mb-1.5">New password</label>
            <input id="password" name="password" type="password" class="input" autocomplete="new-password" required>
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-medium mb-1.5">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="input" autocomplete="new-password" required>
          </div>

          <button type="submit" class="btn-primary shine-btn glow-primary w-full">
            <span>Update password</span>
          </button>
        </form>
      </div>
    </div>
  </div>
</section>

@include('site._footer')
@endsection
