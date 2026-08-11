<x-admin.layout title="Record gift">
    <div class="space-y-6 w-full">
        <div>
            <div class="text-xs text-ink-muted mb-1">
                <a href="{{ route('admin.tithes.index') }}" class="hover:underline">Tithes</a>
                <span class="mx-1">/</span>
                <span>Create</span>
            </div>
            <h1 class="text-2xl font-semibold tracking-tight text-ink">Record gift</h1>
            <p class="text-sm text-ink-muted mt-1">Log a tithe or offering against a fund.</p>
        </div>

        @include('admin.tithes._form')
    </div>
</x-admin.layout>
