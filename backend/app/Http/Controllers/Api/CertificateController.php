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

/**
 * @group Certificates
 *
 * Certificate preview, generation, and PDF download (when enabled for the deployment).
 */
class CertificateController extends Controller
{
    public function __construct(
        protected CertificatePdfService $certificatePdf
    ) {}

    /**
     * Preview certificate PDF
     *
     * Streams a sample certificate PDF using the authenticated user's name (or placeholder data).
     * Requires `certificates:read` ability.
     *
     * @authenticated
     * @response 503 {"message":"Certificate generation is not available. Template or PDF library missing."}
     */
    public function preview(Request $request): StreamedResponse|JsonResponse|Response
    {
        if (! $this->certificatePdf->canGenerate()) {
            return response()->json([
                'message' => 'Certificate generation is not available. Template or PDF library missing.',
            ], 503);
        }

        $user = $request->user();
        $prefix = (string) config('certificates.certificate_number_prefix', 'ZPF-MEM');
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
            'certificate_number' => $prefix.'-'.date('Y').'-PREVIEW01',
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
     * List my certificates
     *
     * Returns issued certificates with `pdf_status` for polling. When the government workflow is active,
     * returns an empty list and points clients to academy applications instead.
     *
     * @authenticated
     * @response 200 {"data":[{"id":1,"certificate_number":"ZPF-MEM-2026-00001","pdf_status":"ready","issued_at":"2026-04-01T12:00:00+00:00"}]}
     * @response 200 scenario="Workflow mode" {"data":[],"meta":{"certificates_disabled":true,"message":"Certificates are processed after payment and Presidium approval. View your payment receipt under Academy applications.","applications_url":"/api/v1/academy/applications"}}
     */
    public function index(Request $request)
    {
        if (! config('academy.student_certificate_download_enabled', false)) {
            return response()->json([
                'data' => [],
                'meta' => [
                    'certificates_disabled' => true,
                    'message' => 'Certificates are processed after payment and Presidium approval. View your payment receipt under Academy applications.',
                    'applications_url' => '/api/v1/academy/applications',
                ],
            ]);
        }

        $certificates = Certificate::where('user_id', $request->user()->id)
            ->with('course:id,title,code')
            ->orderByDesc('issued_at')
            ->get();

        return response()->json(['data' => $certificates]);
    }

    /**
     * Queue certificate PDF generation
     *
     * Dispatches async PDF generation when status is `pending`. Poll until `pdf_status` is `ready`.
     *
     * @authenticated
     * @urlParam certificate integer required Certificate ID. Example: 1
     * @response 200 {"message":"Certificate PDF is ready.","pdf_status":"ready"}
     * @response 202 {"message":"Certificate generation started.","pdf_status":"pending"}
     */
    public function generate(Certificate $certificate): JsonResponse|Response
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
     * Download certificate PDF
     *
     * Streams the PDF when ready; otherwise returns 202 with `pdf_status` and may queue generation.
     *
     * @authenticated
     * @urlParam certificate integer required Certificate ID. Example: 1
     * @response 202 {"message":"PDF is not ready yet. Poll certificates list or generate endpoint for status.","pdf_status":"generating"}
     * @response 503 {"message":"Certificate generation is not available. Template or PDF library missing."}
     */
    public function download(Certificate $certificate): StreamedResponse|JsonResponse|Response
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
