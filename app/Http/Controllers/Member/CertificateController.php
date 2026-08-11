<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $certificates = Certificate::query()
            ->where('user_id', $request->user()->id)
            ->whereNotNull('sent_at')
            ->latest('sent_at')
            ->paginate(10);

        return view('member.certificates.index', compact('certificates'));
    }

    public function show(Request $request, Certificate $certificate)
    {
        $this->authorizeOwned($request, $certificate);

        return view('member.certificates.show', compact('certificate'));
    }

    public function preview(Request $request, Certificate $certificate)
    {
        $this->authorizeOwned($request, $certificate);

        return view('certificates.print', [
            'certificate' => $certificate,
            'autoPrint' => false,
            'backUrl' => route('member.certificates.show', $certificate),
            'editUrl' => null,
            'pdfUrl' => route('member.certificates.pdf', $certificate),
        ]);
    }

    public function print(Request $request, Certificate $certificate)
    {
        $this->authorizeOwned($request, $certificate);

        return view('certificates.print', [
            'certificate' => $certificate,
            'autoPrint' => true,
            'backUrl' => route('member.certificates.show', $certificate),
            'editUrl' => null,
            'pdfUrl' => route('member.certificates.pdf', $certificate),
        ]);
    }

    public function pdf(Request $request, Certificate $certificate)
    {
        $this->authorizeOwned($request, $certificate);

        $filename = \Illuminate\Support\Str::slug($certificate->recipient_name.'-'.$certificate->templateLabel()).'.pdf';

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('certificates.pdf', compact('certificate'))
            ->setPaper([0, 0, 841.89, 595.28])
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false)
            ->setOption('isFontSubsettingEnabled', true)
            ->setOption('defaultFont', 'DejaVu Serif')
            ->setOption('dpi', 96)
            ->download($filename);
    }

    protected function authorizeOwned(Request $request, Certificate $certificate): void
    {
        abort_unless(
            $certificate->user_id === $request->user()->id && $certificate->sent_at !== null,
            404
        );
    }
}
