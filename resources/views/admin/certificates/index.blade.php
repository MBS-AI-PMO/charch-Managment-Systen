<x-admin.layout title="Certificates">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-serif">Certificates</h1>
            <p class="text-sm text-ink-muted mt-1">Create, preview, print, or export certificates as PDF.</p>
        </div>
        @can('manage-certificates')
            <a href="{{ route('admin.certificates.create') }}" class="btn-primary text-sm">+ New certificate</a>
        @endcan
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface text-left text-xs uppercase tracking-wide text-ink-muted">
                    <tr>
                        <th class="px-4 py-3 font-medium">Recipient</th>
                        <th class="px-4 py-3 font-medium">Template</th>
                        <th class="px-4 py-3 font-medium">Assigned by</th>
                        <th class="px-4 py-3 font-medium">Date</th>
                        <th class="px-4 py-3 font-medium">Sent to</th>
                        <th class="px-4 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[rgb(var(--border))]">
                    @forelse($certificates as $certificate)
                        <tr class="hover:bg-surface/60">
                            <td class="px-4 py-3">
                                <div class="font-medium text-ink">{{ $certificate->recipient_name }}</div>
                                @if($certificate->title)
                                    <div class="text-xs text-ink-muted mt-0.5">{{ $certificate->title }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-ink-muted">{{ $certificate->templateLabel() }}</td>
                            <td class="px-4 py-3 text-ink-muted">{{ $certificate->assigned_by }}</td>
                            <td class="px-4 py-3 text-ink-muted whitespace-nowrap">{{ $certificate->issued_on?->format('M j, Y') }}</td>
                            <td class="px-4 py-3 text-ink-muted">
                                @if($certificate->isSent())
                                    <div class="text-ink">{{ $certificate->user?->name }}</div>
                                    <div class="text-xs">{{ $certificate->sent_at?->diffForHumans() }}</div>
                                @else
                                    <span class="text-xs">Not sent</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="row-actions">
                                    <x-row-action type="preview" href="{{ route('admin.certificates.preview', $certificate) }}" target="_blank" />
                                    <x-row-action type="pdf" href="{{ route('admin.certificates.pdf', $certificate) }}" label="Export PDF" />
                                    <x-row-action type="print" href="{{ route('admin.certificates.print', $certificate) }}" target="_blank" />
                                    <x-row-action type="edit" href="{{ route('admin.certificates.edit', $certificate) }}" />
                                    <form method="POST" action="{{ route('admin.certificates.destroy', $certificate) }}" onsubmit="return confirm('Delete this certificate?')">
                                        @csrf @method('DELETE')
                                        <x-row-action type="delete" />
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-ink-muted">
                                No certificates yet.
                                <a href="{{ route('admin.certificates.create') }}" class="text-brand-primary hover:underline">Create one</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($certificates->hasPages())
            <div class="px-4 py-3 border-t border-[rgb(var(--border))]">
                {{ $certificates->links() }}
            </div>
        @endif
    </div>
</x-admin.layout>
