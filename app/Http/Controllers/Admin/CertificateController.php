<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CertificateRequest;
use App\Models\Certificate;
use App\Models\User;
use App\Support\CertificateTemplates;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::query()
            ->with('user:id,name,email')
            ->latest()
            ->paginate(10);

        return view('admin.certificates.index', compact('certificates'));
    }

    public function create()
    {
        return view('admin.certificates.create', [
            'certificate' => new Certificate([
                'assigned_by' => settings('brand.name', 'Assemblies of God'),
                'issued_on' => now()->toDateString(),
            ]),
            'templates' => CertificateTemplates::all(),
            'members' => $this->members(),
        ]);
    }

    public function store(CertificateRequest $req)
    {
        $certificate = Certificate::create($req->certificateData() + [
            'created_by' => $req->user('admin')?->id,
        ]);

        $member = $req->member();
        if ($member) {
            $certificate->assignTo($member, $req->user('admin'));
        }

        return redirect()
            ->route('admin.certificates.show', $certificate)
            ->with('success', $member
                ? 'Certificate saved and assigned to '.$member->name.'.'
                : 'Certificate saved.');
    }

    public function show(Certificate $certificate)
    {
        $certificate->load('user:id,name,email');

        return view('admin.certificates.show', compact('certificate'));
    }

    public function edit(Certificate $certificate)
    {
        $certificate->load('user:id,name,email');

        return view('admin.certificates.edit', [
            'certificate' => $certificate,
            'templates' => CertificateTemplates::all(),
            'members' => $this->members(),
        ]);
    }

    public function update(CertificateRequest $req, Certificate $certificate)
    {
        $certificate->update($req->certificateData());

        $member = $req->member();
        $message = 'Certificate updated.';

        if ($member && (int) $certificate->user_id !== (int) $member->id) {
            $certificate->assignTo($member, $req->user('admin'));
            $message = 'Certificate updated and assigned to '.$member->name.'.';
        }

        return redirect()
            ->route('admin.certificates.show', $certificate)
            ->with('success', $message);
    }

    public function destroy(Certificate $certificate)
    {
        $certificate->delete();

        return redirect()
            ->route('admin.certificates.index')
            ->with('success', 'Certificate deleted.');
    }

    public function preview(Certificate $certificate)
    {
        return view('certificates.print', [
            'certificate' => $certificate,
            'autoPrint' => false,
            'backUrl' => route('admin.certificates.index'),
            'editUrl' => route('admin.certificates.edit', $certificate),
            'pdfUrl' => route('admin.certificates.pdf', $certificate),
        ]);
    }

    public function print(Certificate $certificate)
    {
        return view('certificates.print', [
            'certificate' => $certificate,
            'autoPrint' => true,
            'backUrl' => route('admin.certificates.index'),
            'editUrl' => route('admin.certificates.edit', $certificate),
            'pdfUrl' => route('admin.certificates.pdf', $certificate),
        ]);
    }

    public function pdf(Certificate $certificate)
    {
        $filename = Str::slug($certificate->recipient_name.'-'.$certificate->templateLabel()).'.pdf';

        // Explicit A4 landscape in points (width × height) so DomPDF never flips to portrait.
        return Pdf::loadView('certificates.pdf', compact('certificate'))
            ->setPaper([0, 0, 841.89, 595.28])
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false)
            ->setOption('isFontSubsettingEnabled', true)
            ->setOption('defaultFont', 'DejaVu Serif')
            ->setOption('dpi', 96)
            ->download($filename);
    }

    private function members()
    {
        return User::query()
            ->where('is_admin', false)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }
}
