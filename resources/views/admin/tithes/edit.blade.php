<x-admin.layout title="Edit gift">
    <div class="space-y-6 w-full">
        <div>
            <div class="text-xs text-ink-muted mb-1">
                <a href="{{ route('admin.tithes.index') }}" class="hover:underline">Tithes</a>
                <span class="mx-1">/</span>
                <span>Edit</span>
            </div>
            <h1 class="text-2xl font-semibold tracking-tight text-ink">Edit gift</h1>
            <p class="text-sm text-ink-muted mt-1">Update the details for this recorded gift.</p>
        </div>

        @include('admin.tithes._form')
    </div>
</x-admin.layout>
