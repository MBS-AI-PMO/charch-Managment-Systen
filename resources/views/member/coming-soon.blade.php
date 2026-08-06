@extends('layouts.site')

@section('title', 'Member portal')

@section('content')
<main class="min-h-screen flex items-center justify-center px-4 py-16 bg-surface">
    <div class="w-full max-w-md card p-10 text-center">
        <svg class="w-12 h-12 mx-auto text-brand-primary mb-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z"/>
        </svg>
        <h1 class="font-serif text-3xl mb-3">Member portal coming soon</h1>
        <p class="text-ink-muted mb-8">
            You're signed in{{ auth('web')->check() ? ' as '.auth('web')->user()->name : '' }}.
            We're putting the finishing touches on this space.
        </p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-ghost w-full">Sign out</button>
        </form>

        <p class="text-xs text-ink-muted mt-8">
            <a href="/" class="text-brand-primary hover:underline">Return to the website</a>
        </p>
    </div>
</main>
@endsection
