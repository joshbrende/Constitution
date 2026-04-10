<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateCertificatePdfJob;
use App\Models\Certificate;
use App\Services\CertificatePdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CertificateController extends Controller
{
    public function __construct(
        protected CertificatePdfService $certificatePdf
    ) {}

    /**
     * Preview the certificate template with sample data (name, date, cert number).
     */
    public function preview(Request $request): StreamedResponse|JsonResponse|Response
    {
        if (! $this->certificatePdf->canGenerate()) {
            return response()->json([
                'message' => 'Certificate generation is not available. Template or PDF library missing.',
            ], 503);
        }

        $user = $request->user();
        $certificate = (object) [
            'public_id' => (string) \Illuminate\Support\Str::uuid(),
            'user' => (object) [
                'name' => $user?->name ?? 'Sample',
                'surname' => $user?->surname ?? 'Member',
            ],
            'course' => (object) [
                'title' => 'Foundational Constitutional Studies Certificate',
                'certificate_title' => 'Certificate of Competence',
            ],
            'certificate_number' => 'ZP-MEM-' . date('Y') . '-00000',
            'verification_code' => 'PREVIEW1',
            'verification_token' => hash_hmac('sha256', 'preview|' . date('Y-m-d'), (string) config('app.key', '')),
            'issued_at' => now(),
        ];

        $pdfContent = $this->certificatePdf->generate($certificate);

        return response()->streamDownload(
            fn () => print $pdfContent,
            'certificate-preview.pdf',
            ['Content-Type' => 'application/pdf'],
            'inline'
        );
    }

    /**
     * List the authenticated user's certificates (includes pdf_status for polling).
     */
    public function index(Request $request)
    {
        $certificates = Certificate::where('user_id', $request->user()->id)
            ->with('course:id,title,code')
            ->orderByDesc('issued_at')
            ->get();

        return response()->json(['data' => $certificates]);
    }

    /**
     * Request PDF generation (queued). Returns 202 with pdf_status until ready.
     */
    public function generate(Request $request, Certificate $certificate): JsonResponse|Response
    {
        $this->authorize('generate', $certificate);

        $status = $certificate->pdf_status ?? 'pending';
        if ($status === 'ready') {
            return response()->json([
                'message' => 'Certificate PDF is ready.',
                'pdf_status' => 'ready',
            ], 200);
        }

        if ($status === 'pending' || $status === 'generating') {
            if ($status === 'pending') {
                GenerateCertificatePdfJob::dispatch($certificate);
            }
            return response()->json([
                'message' => $status === 'generating' ? 'Certificate is being generated.' : 'Certificate generation started.',
                'pdf_status' => $status === 'generating' ? 'generating' : 'pending',
            ], 202);
        }

        return response()->json(['message' => 'Invalid state.', 'pdf_status' => $status], 400);
    }

    /**
     * Download the certificate PDF. Streams from disk when ready; otherwise 202 with pdf_status.
     */
    public function download(Request $request, Certificate $certificate): StreamedResponse|JsonResponse|Response
    {
        $this->authorize('download', $certificate);

        $status = $certificate->pdf_status ?? 'pending';

        if ($status === 'ready' && $certificate->pdf_path && Storage::exists($certificate->pdf_path)) {
            $filename = 'certificate-membership-' . $certificate->certificate_number . '.pdf';
            return response()->streamDownload(
                function () use ($certificate) {
                    echo Storage::get($certificate->pdf_path);
                },
                $filename,
                ['Content-Type' => 'application/pdf'],
                'inline'
            );
        }

        if ($status === 'pending' || $status === 'generating') {
            if ($status === 'pending') {
                GenerateCertificatePdfJob::dispatch($certificate);
            }
            return response()->json([
                'message' => 'PDF is not ready yet. Poll certificates list or generate endpoint for status.',
                'pdf_status' => $status === 'pending' ? 'pending' : 'generating',
            ], 202);
        }

        if (! $this->certificatePdf->canGenerate()) {
            return response()->json([
                'message' => 'Certificate generation is not available. Template or PDF library missing.',
            ], 503);
        }

        GenerateCertificatePdfJob::dispatch($certificate);
        return response()->json([
            'message' => 'Certificate generation started.',
            'pdf_status' => 'pending',
        ], 202);
    }
}
