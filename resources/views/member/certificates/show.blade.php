<x-member.layout title="Certificate">
    <div class="max-w-container mx-auto px-4 py-10 space-y-6">
        <div class="flex items-end justify-between flex-wrap gap-3">
            <div>
                <div class="text-xs text-ink-muted mb-1">
                    <a href="{{ route('member.certificates.index') }}" class="hover:underline">Certificates</a>
                    <span class="mx-1">/</span>
                    <span>View</span>
                </div>
                <h1 class="font-serif text-3xl">{{ $certificate->templateLabel() }}</h1>
                <p class="text-sm text-ink-muted mt-1">Issued to {{ $certificate->recipient_name }}</p>
            </div>
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('member.certificates.preview', $certificate) }}" target="_blank" class="btn-ghost text-sm">Preview</a>
                <a href="{{ route('member.certificates.pdf', $certificate) }}" class="btn-ghost text-sm">Export PDF</a>
                <a href="{{ route('member.certificates.print', $certificate) }}" target="_blank" class="btn-primary text-sm">Print</a>
            </div>
        </div>

        <div class="card p-5 md:p-6">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5 text-sm">
                <div>
                    <div class="text-xs uppercase tracking-wide text-ink-muted">Recipient</div>
                    <div class="mt-1.5 font-medium">{{ $certificate->recipient_name }}</div>
                </div>
                <div>
                    <div class="text-xs uppercase tracking-wide text-ink-muted">Assigned by</div>
                    <div class="mt-1.5">{{ $certificate->assigned_by }}</div>
                </div>
                <div>
                    <div class="text-xs uppercase tracking-wide text-ink-muted">Date</div>
                    <div class="mt-1.5">{{ $certificate->issued_on?->format('F j, Y') }}</div>
                </div>
                <div>
                    <div class="text-xs uppercase tracking-wide text-ink-muted">Title / purpose</div>
                    <div class="mt-1.5">{{ $certificate->title ?: '—' }}</div>
                </div>
            </div>
        </div>

        <div class="card p-4 md:p-6">
            <div class="text-xs uppercase tracking-wide text-ink-muted mb-4">Preview</div>
            <div class="cert-admin-preview rounded-xl border border-[rgb(var(--border))] bg-[#ebe4d8] p-4 md:p-6 overflow-x-auto">
                <div class="cert-admin-preview-stage mx-auto">
                    <div class="cert-admin-preview-scale">
                        @include($certificate->templateView(), ['certificate' => $certificate])
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .cert-admin-preview-stage {
            width: 100%;
            max-width: 980px;
            margin-left: auto;
            margin-right: auto;
            height: 420px;
            overflow: hidden;
            display: flex;
            justify-content: center;
        }
        .cert-admin-preview-scale {
            width: 1122px;
            height: 793px;
            transform: scale(0.48);
            transform-origin: top center;
            flex-shrink: 0;
        }
        @media (min-width: 640px) {
            .cert-admin-preview-stage { height: 480px; }
            .cert-admin-preview-scale { transform: scale(0.58); }
        }
        @media (min-width: 1024px) {
            .cert-admin-preview-stage { height: 560px; max-width: 1100px; }
            .cert-admin-preview-scale { transform: scale(0.68); }
        }
    </style>
</x-member.layout>
