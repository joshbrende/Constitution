<?php

namespace App\Http\Controllers;

use App\Services\CertificatePdfService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class CertificatePreviewController extends Controller
{
    /**
     * Inline PDF preview for certificate layout (authenticated).
     */
    public function show(Request $request, CertificatePdfService $service): Response
    {
        if (! $service->canGenerate()) {
            return response('Certificate template or PDF library not available.', 503);
        }

        $user = $request->user();
        $courseTitle = $request->query('course_title') ?? $request->query('title') ?? 'Foundational Constitutional Studies Certificate';
        $certificateTitle = $request->query('certificate_title') ?? 'Certificate of Competence';

        $cert = (object) [
            'public_id' => (string) Str::uuid(),
            'user' => (object) [
                'name' => $user?->name ?? 'Sample',
                'surname' => $user?->surname ?? 'Member',
            ],
            'course' => (object) [
                'title' => $courseTitle,
                'certificate_title' => $certificateTitle !== '' ? $certificateTitle : 'Certificate of Completion',
            ],
            'certificate_number' => 'ZP-MEM-' . date('Y') . '-00000',
            'verification_code' => 'PREVIEW1',
            'verification_token' => hash_hmac('sha256', 'preview|' . date('Y-m-d'), (string) config('app.key', '')),
            'issued_at' => now(),
        ];

        $pdf = $service->generate($cert);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="certificate-preview.pdf"',
        ]);
    }
}
