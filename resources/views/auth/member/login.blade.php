@extends('layouts.site')

@section('title', 'Sign in | Members')

@section('content')
@php
  $brand = $brandName ?? (function_exists('settings') ? settings('brand.name', 'Assemblies of God') : 'Assemblies of God');
  $tagline = $brandTagline ?? (function_exists('settings') ? settings('brand.tagline', 'Rawalpindi') : 'Rawalpindi');
@endphp

<x-site.header :nav="$siteNav" />

<section class="auth-page relative overflow-hidden">
  <div class="auth-page-bg" aria-hidden="true"></div>

  <div class="relative max-w-container mx-auto px-4 sm:px-6 py-10 md:py-16">
    <div class="grid lg:grid-cols-2 gap-6 lg:gap-8 lg:items-stretch max-w-5xl mx-auto">
      {{-- Welcome --}}
      <aside class="contact-panel contact-panel--info reveal hidden lg:flex flex-col justify-between min-h-[28rem]">
        <div>
          <p class="text-xs uppercase tracking-[0.18em] text-brand-primary font-semibold mb-3">Member portal</p>
          <h1 class="font-serif text-3xl xl:text-4xl leading-tight text-ink">Welcome back.</h1>
          <p class="mt-4 text-ink-muted leading-relaxed max-w-sm">
            Sign in to access prayer requests, your community feed, and everything waiting for you in the member portal.
          </p>
        </div>

        <div class="mt-10 pt-8 border-t border-[rgb(var(--border))]">
          <div class="flex items-center gap-3">
            @if($brandLogoUrl ?? null)
              <img src="{{ $brandLogoUrl }}" alt="{{ $brand }}" class="h-12 w-auto max-w-[72px] object-contain shrink-0">
            @endif
            <div class="leading-tight min-w-0">
              <div class="font-serif text-lg font-semibold text-ink truncate">{{ $brand }}</div>
              @if($tagline)
                <div class="text-xs uppercase tracking-[0.12em] text-ink-muted mt-0.5">{{ $tagline }}</div>
              @endif
            </div>
          </div>
        </div>
      </aside>

      {{-- Form --}}
      <div class="contact-panel reveal delay-1">
        <div class="lg:hidden mb-6">
          <div class="flex items-center gap-3 mb-5">
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
          <p class="text-xs uppercase tracking-[0.18em] text-brand-primary font-semibold mb-2">Member portal</p>
        </div>

        <h2 class="font-serif text-2xl md:text-3xl text-ink">Sign in</h2>
        <p class="text-sm text-ink-muted mt-1.5">
          New here?
          <a href="{{ route('register') }}" class="text-brand-primary hover:underline font-medium">Create an account</a>
        </p>

        @if ($errors->any())
          <div class="mt-5 px-4 py-3 rounded-lg border border-rose-200 bg-rose-50 text-rose-800 text-sm">
            {{ $errors->first() }}
          </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="mt-7 space-y-5">
          @csrf

          <div>
            <label for="email" class="block text-sm font-medium mb-1.5">Email</label>
            <input
              id="email"
              name="email"
              type="email"
              class="input"
              value="{{ old('email') }}"
              placeholder="you@example.com"
              autocomplete="username"
              required
              autofocus
            >
          </div>

          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label for="password" class="block text-sm font-medium">Password</label>
              <a href="{{ route('password.request') }}" class="text-xs text-brand-primary hover:underline">Forgot password?</a>
            </div>
            <input
              id="password"
              name="password"
              type="password"
              class="input"
              placeholder="••••••••"
              autocomplete="current-password"
              required
            >
          </div>

          <label class="flex items-center gap-2 text-sm text-ink-muted">
            <input type="checkbox" name="remember" value="1" class="rounded border-[rgb(var(--border))] text-brand-primary focus:ring-brand-primary">
            Remember me
          </label>

          <button type="submit" class="btn-primary shine-btn glow-primary w-full">
            <span>Sign in</span>
          </button>
        </form>

        <p class="text-xs text-ink-muted text-center mt-8">
          <a href="{{ route('site.home') }}" class="text-brand-primary hover:underline">← Back to website</a>
        </p>
      </div>
    </div>
  </div>
</section>

@include('site._footer')
@endsection
