@extends('layouts.site')

@section('title', 'Set new password | Members')

@section('content')
<main class="min-h-screen flex items-center justify-center px-4 py-16 bg-surface">
    <div class="w-full max-w-md card p-8">
        <div class="text-center mb-8">
            <h1 class="font-serif text-3xl">Set a new password</h1>
            <p class="text-ink-muted text-sm mt-2">Minimum 8 characters.</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 bg-red-50 text-red-700 border border-red-200 p-3 rounded text-sm">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
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

            <button type="submit" class="btn-primary w-full">Update password</button>
        </form>
    </div>
</main>
@endsection
