<x-admin.layout title="Edit certificate">
    <form method="POST" action="{{ route('admin.certificates.update', $certificate) }}" class="space-y-6 w-full">
        @csrf
        @method('PUT')

        <div class="flex items-end justify-between flex-wrap gap-3">
            <div>
                <div class="text-xs text-ink-muted mb-1">
                    <a href="{{ route('admin.certificates.index') }}" class="hover:underline">Certificates</a>
                    <span class="mx-1">/</span>
                    <span>Edit</span>
                </div>
                <h1 class="text-2xl font-serif">Edit certificate</h1>
                <p class="text-sm text-ink-muted mt-1">Update recipient details or switch the template.</p>
            </div>
            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.certificates.show', $certificate) }}" class="btn-ghost text-sm">Cancel</a>
                <button type="submit" class="btn-primary text-sm">Update certificate</button>
            </div>
        </div>

        @include('admin.certificates._form')

        <div class="flex justify-end gap-2 lg:hidden">
            <a href="{{ route('admin.certificates.show', $certificate) }}" class="btn-ghost text-sm">Cancel</a>
            <button type="submit" class="btn-primary text-sm">Update certificate</button>
        </div>
    </form>
</x-admin.layout>
