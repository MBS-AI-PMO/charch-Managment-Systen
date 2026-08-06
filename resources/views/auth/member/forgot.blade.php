@extends('layouts.site')

@section('title', 'Reset password | Members')

@section('content')
<main class="min-h-screen flex items-center justify-center px-4 py-16 bg-surface">
    <div class="w-full max-w-md card p-8">
        <div class="text-center mb-8">
            <h1 class="font-serif text-3xl">Forgot your password?</h1>
            <p class="text-ink-muted text-sm mt-2">Enter your email and we'll send a reset link.</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 bg-red-50 text-red-700 border border-red-200 p-3 rounded text-sm">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium mb-1.5">Email</label>
                <input id="email" name="email" type="email" class="input" value="{{ old('email') }}" required autofocus>
            </div>
            <button type="submit" class="btn-primary w-full">Send reset link</button>
        </form>

        <p class="text-xs text-ink-muted text-center mt-8">
            <a href="{{ route('login') }}" class="text-brand-primary hover:underline">Back to sign in</a>
        </p>
    </div>
</main>
@endsection
