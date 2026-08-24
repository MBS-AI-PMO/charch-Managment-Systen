<x-member.layout title="My Certificates">
    <div class="member-shell space-y-6">
        <div>
            <h1 class="font-serif text-3xl">My Certificates</h1>
            <p class="text-ink-muted mt-1 text-sm">Certificates sent to you by the church admin. Open and print anytime.</p>
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-surface text-left text-xs uppercase tracking-wide text-ink-muted">
                        <tr>
                            <th class="px-4 py-3 font-medium">Certificate</th>
                            <th class="px-4 py-3 font-medium">Date</th>
                            <th class="px-4 py-3 font-medium">Received</th>
                            <th class="px-4 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[rgb(var(--border))]">
                        @forelse($certificates as $certificate)
                            <tr class="hover:bg-surface/60">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-ink">{{ $certificate->templateLabel() }}</div>
                                    <div class="text-xs text-ink-muted mt-0.5">{{ $certificate->recipient_name }}</div>
                                    @if($certificate->title)
                                        <div class="text-xs text-ink-muted">{{ $certificate->title }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-ink-muted whitespace-nowrap">{{ $certificate->issued_on?->format('M j, Y') }}</td>
                                <td class="px-4 py-3 text-ink-muted whitespace-nowrap">{{ $certificate->sent_at?->diffForHumans() }}</td>
                                <td class="px-4 py-3">
                                    <div class="row-actions">
                                        <x-row-action type="view" href="{{ route('member.certificates.show', $certificate) }}" />
                                        <x-row-action type="preview" href="{{ route('member.certificates.preview', $certificate) }}" target="_blank" />
                                        <x-row-action type="pdf" href="{{ route('member.certificates.pdf', $certificate) }}" label="Export PDF" />
                                        <x-row-action type="print" href="{{ route('member.certificates.print', $certificate) }}" target="_blank" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-ink-muted">
                                    No certificates have been sent to you yet.
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
    </div>
</x-member.layout>
