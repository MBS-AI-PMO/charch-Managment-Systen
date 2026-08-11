<x-admin.layout title="New certificate">
    <form method="POST" action="{{ route('admin.certificates.store') }}" class="space-y-6 w-full">
        @csrf

        <div class="flex items-end justify-between flex-wrap gap-3">
            <div>
                <div class="text-xs text-ink-muted mb-1">
                    <a href="{{ route('admin.certificates.index') }}" class="hover:underline">Certificates</a>
                    <span class="mx-1">/</span>
                    <span>New</span>
                </div>
                <h1 class="text-2xl font-serif">New certificate</h1>
                <p class="text-sm text-ink-muted mt-1">Choose a template and fill in the recipient details.</p>
            </div>
            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.certificates.index') }}" class="btn-ghost text-sm">Cancel</a>
                <button type="submit" class="btn-primary text-sm">Save certificate</button>
            </div>
        </div>

        @include('admin.certificates._form')

        <div class="flex justify-end gap-2 lg:hidden">
            <a href="{{ route('admin.certificates.index') }}" class="btn-ghost text-sm">Cancel</a>
            <button type="submit" class="btn-primary text-sm">Save certificate</button>
        </div>
    </form>
</x-admin.layout>
