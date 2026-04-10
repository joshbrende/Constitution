<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Services\CertificatePdfService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class CertificateController extends Controller
{
    /**
     * Preview the certificate PDF template (sample data). Facilitators and admins only.
     */
    public function previewPdf(CertificatePdfService $pdfService)
    {
        if (!Auth::check() || !Auth::user()->canEditCourses()) {
            abort(403, 'Only facilitators and admins can preview the certificate template.');
        }

        $certificate = Certificate::with(['user', 'course'])->first();
        if (!$certificate) {
            $certificate = $this->makeSampleCertificate();
        }

        if ($pdfService->supportsBackgroundPdf($certificate)) {
            $content = $pdfService->generate($certificate);
            $filename = 'certificate-preview-' . now()->format('Y-m-d') . '.pdf';
            return response($content, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        }

        if (!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            abort(503, 'PDF export is not available. Run: composer require barryvdh/laravel-dompdf');
        }
        $filename = 'certificate-preview-' . now()->format('Y-m-d') . '.pdf';
        return Pdf::loadView('certificates.pdf', compact('certificate'))
            ->setPaper('a4', 'landscape')
            ->download($filename);
    }

    private function makeSampleCertificate(): object
    {
        $cert = new \stdClass();
        $cert->certificate_number = 'PREVIEW-' . now()->format('YmdHis');
        $cert->issued_at = now();
        $cert->user = new \stdClass();
        $cert->user->name = 'Sample';
        $cert->user->surname = 'Participant';
        $cert->course = new \stdClass();
        $cert->course->title = 'Introduction to TTM Group Training';
        $cert->course->instructor_id = Auth::id();

        return $cert;
    }

    public function show(Certificate $certificate)
    {
        if ($certificate->user_id !== Auth::id()) {
            abort(403, 'You can only view your own certificates.');
        }

        $certificate->load(['user', 'course']);

        return view('certificates.show', compact('certificate'));
    }

    public function downloadPdf(Certificate $certificate, CertificatePdfService $pdfService)
    {
        if ($certificate->user_id !== Auth::id()) {
            abort(403, 'You can only download your own certificates.');
        }

        $certificate->load(['user', 'course']);

        if ($pdfService->supportsBackgroundPdf($certificate)) {
            $content = $pdfService->generate($certificate);
            $filename = 'certificate-' . preg_replace('/[^a-z0-9-]/i', '-', $certificate->certificate_number ?? '') . '.pdf';
            return response($content, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        }

        if (!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            abort(503, 'PDF export is not available. Run: composer require barryvdh/laravel-dompdf');
        }
        $filename = 'certificate-' . preg_replace('/[^a-z0-9-]/i', '-', $certificate->certificate_number ?? '') . '.pdf';
        return Pdf::loadView('certificates.pdf', compact('certificate'))
            ->setPaper('a4', 'landscape')
            ->download($filename);
    }
}
