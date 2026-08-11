<x-admin.layout title="Certificate">
    <div class="w-full space-y-6">
        <div class="flex items-end justify-between flex-wrap gap-3">
            <div class="min-w-0">
                <div class="text-xs text-ink-muted mb-1">
                    <a href="{{ route('admin.certificates.index') }}" class="hover:underline">Certificates</a>
                    <span class="mx-1">/</span>
                    <span>View</span>
                </div>
                <h1 class="text-2xl font-serif truncate">{{ $certificate->recipient_name }}</h1>
                <p class="text-sm text-ink-muted mt-1">{{ $certificate->templateLabel() }}</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap shrink-0">
                <a href="{{ route('admin.certificates.preview', $certificate) }}" target="_blank" class="btn-ghost text-sm">Preview</a>
                <a href="{{ route('admin.certificates.pdf', $certificate) }}" class="btn-ghost text-sm">Export PDF</a>
                <a href="{{ route('admin.certificates.print', $certificate) }}" target="_blank" class="btn-primary text-sm">Print</a>
                <a href="{{ route('admin.certificates.edit', $certificate) }}" class="btn-ghost text-sm">Edit</a>
            </div>
        </div>

        <div class="card p-5 md:p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-5 text-sm">
                <div>
                    <div class="text-xs uppercase tracking-wide text-ink-muted">Recipient</div>
                    <div class="mt-1.5 font-medium text-ink break-words">{{ $certificate->recipient_name }}</div>
                </div>
                <div>
                    <div class="text-xs uppercase tracking-wide text-ink-muted">Assigned by</div>
                    <div class="mt-1.5 text-ink break-words">{{ $certificate->assigned_by }}</div>
                </div>
                <div>
                    <div class="text-xs uppercase tracking-wide text-ink-muted">Date</div>
                    <div class="mt-1.5 text-ink">{{ $certificate->issued_on?->format('F j, Y') }}</div>
                </div>
                <div>
                    <div class="text-xs uppercase tracking-wide text-ink-muted">Template</div>
                    <div class="mt-1.5 text-ink break-words">{{ $certificate->templateLabel() }}</div>
                </div>
                <div>
                    <div class="text-xs uppercase tracking-wide text-ink-muted">Title / purpose</div>
                    <div class="mt-1.5 text-ink break-words">{{ $certificate->title ?: '—' }}</div>
                </div>
            </div>
        </div>

        <div class="card p-4 md:p-6">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div class="text-xs uppercase tracking-wide text-ink-muted">Preview</div>
                <a href="{{ route('admin.certificates.preview', $certificate) }}" target="_blank" class="text-xs text-brand-primary hover:underline">Open full size →</a>
            </div>

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
        @media (min-width: 1280px) {
            .cert-admin-preview-stage { height: 640px; }
            .cert-admin-preview-scale { transform: scale(0.78); }
        }
    </style>
</x-admin.layout>
