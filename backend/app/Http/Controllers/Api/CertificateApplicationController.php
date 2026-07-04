<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CertificateApplication;
use App\Services\AcademyPaymentOfficeService;
use App\Services\ReceiptPdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @group Academy
 *
 * Certificate applications, payment receipts, and workflow status.
 */
class CertificateApplicationController extends Controller
{
    public function __construct(
        protected AcademyPaymentOfficeService $paymentOfficeService,
        protected ReceiptPdfService $receiptPdfService
    ) {}

    /**
     * List my certificate applications
     *
     * Returns payment and workflow status for each application after passing the exam.
     *
     * @authenticated
     * @response 200 {"data":[{"id":1,"public_id":"9b7c8f2e-1a3b-4c5d-9e0f-123456789abc","receipt_number":"RCP-2026-00042","payment_reference_code":"PAY-2026-ABC123","fee_amount":25,"fee_currency":"USD","status":"payment_pending","status_label":"Payment pending","exam_passed_at":"2026-04-10T09:00:00+00:00","course":{"id":2,"title":"Foundational Constitutional Studies","code":"FCS-101"}}]}
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', CertificateApplication::class);

        $applications = CertificateApplication::query()
            ->where('user_id', $request->user()->id)
            ->with(['course:id,title,code'])
            ->orderByDesc('exam_passed_at')
            ->get()
            ->map(fn (CertificateApplication $app) => $this->transformApplication($app, includeTimeline: false));

        return response()->json(['data' => $applications]);
    }

    /**
     * Show certificate application detail
     *
     * Includes payment instructions, office locations, timeline, and portal messaging.
     *
     * @authenticated
     * @urlParam application integer required Application ID. Example: 1
     * @response 200 {"data":{"id":1,"status":"payment_pending","payment_instructions":"Pay at your provincial office...","payment_offices":[],"timeline":[],"portal_message":"You passed the exam. Download your payment receipt..."}}
     */
    public function show(Request $request, CertificateApplication $application): JsonResponse
    {
        $this->authorize('view', $application);

        $application->load(['course:id,title,code,certificate_title', 'user.province']);

        return response()->json([
            'data' => $this->transformApplication($application, includeTimeline: true, user: $request->user()),
        ]);
    }

    /**
     * Download payment receipt PDF
     *
     * Available after the member passes the exam and a receipt number is assigned.
     *
     * @authenticated
     * @urlParam application integer required Application ID. Example: 1
     * @response 503 {"message":"Receipt PDF generation is not available."}
     */
    public function receiptPdf(CertificateApplication $application): StreamedResponse|JsonResponse|Response
    {
        $this->authorize('downloadReceipt', $application);

        if (! $this->receiptPdfService->canGenerate()) {
            return response()->json([
                'message' => 'Receipt PDF generation is not available.',
            ], 503);
        }

        $application->load(['user.province', 'course']);
        $pdf = $this->receiptPdfService->generate($application);
        $filename = 'payment-receipt-'.$application->receipt_number.'.pdf';

        return response()->streamDownload(
            fn () => print($pdf),
            $filename,
            ['Content-Type' => 'application/pdf'],
            'inline'
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function transformApplication(
        CertificateApplication $application,
        bool $includeTimeline,
        ?\App\Models\User $user = null
    ): array {
        $payload = [
            'id' => $application->id,
            'public_id' => $application->public_id,
            'receipt_number' => $application->receipt_number,
            'payment_reference_code' => $application->payment_reference_code,
            'fee_amount' => (float) $application->fee_amount,
            'fee_currency' => $application->fee_currency,
            'status' => $application->status->value,
            'status_label' => $application->status->label(),
            'exam_passed_at' => optional($application->exam_passed_at)->toIso8601String(),
            'course' => $application->course ? [
                'id' => $application->course->id,
                'title' => $application->course->title,
                'code' => $application->course->code,
            ] : null,
        ];

        if ($includeTimeline) {
            $payload['payment_instructions'] = $this->paymentOfficeService->paymentInstructions($application);
            $payload['payment_offices'] = $this->paymentOfficeService->officesForUser($user ?? $application->user);
            $payload['timeline'] = $this->buildTimeline($application);
            $payload['portal_message'] = $this->portalMessage($application);
        }

        return $payload;
    }

    /**
     * @return list<array{key: string, label: string, at: string|null, completed: bool}>
     */
    private function buildTimeline(CertificateApplication $application): array
    {
        return [
            [
                'key' => 'exam_passed',
                'label' => 'Exam passed',
                'at' => optional($application->exam_passed_at)->toIso8601String(),
                'completed' => true,
            ],
            [
                'key' => 'payment_pending',
                'label' => 'Payment at government office',
                'at' => optional($application->exam_passed_at)->toIso8601String(),
                'completed' => $application->payment_confirmed_at !== null,
            ],
            [
                'key' => 'payment_confirmed',
                'label' => 'Payment confirmed',
                'at' => optional($application->payment_confirmed_at)->toIso8601String(),
                'completed' => $application->payment_confirmed_at !== null,
            ],
            [
                'key' => 'presidium_approved',
                'label' => 'Presidium approval',
                'at' => optional($application->presidium_approved_at)->toIso8601String(),
                'completed' => $application->presidium_approved_at !== null,
            ],
            [
                'key' => 'ready_for_collection',
                'label' => 'Ready for collection',
                'at' => optional($application->ready_for_collection_at)->toIso8601String(),
                'completed' => $application->ready_for_collection_at !== null,
            ],
            [
                'key' => 'collected',
                'label' => 'Certificate collected',
                'at' => optional($application->collected_at)->toIso8601String(),
                'completed' => $application->collected_at !== null,
            ],
        ];
    }

    private function portalMessage(CertificateApplication $application): string
    {
        return match ($application->status->value) {
            'payment_pending' => 'You passed the exam. Download your payment receipt and pay the certificate fee at the designated government office.',
            'payment_confirmed', 'presidium_pending' => 'Payment received. Your certificate is awaiting Presidium approval.',
            'presidium_approved', 'print_ready', 'printed' => 'Your certificate has been approved and is being prepared for collection.',
            'ready_for_collection' => 'Your certificate is ready for collection at the party office.',
            'collected' => 'Your certificate has been collected. Thank you.',
            default => 'Your certificate application is being processed.',
        };
    }
}
