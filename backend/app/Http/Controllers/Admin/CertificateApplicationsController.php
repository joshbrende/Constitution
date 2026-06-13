<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateApplication;
use App\Services\AdminScopeService;
use App\Services\CertificateApplicationService;
use App\Services\CertificatePdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CertificateApplicationsController extends Controller
{
    public function __construct(
        protected CertificateApplicationService $applicationService,
        protected AdminScopeService $adminScopeService,
        protected CertificatePdfService $certificatePdfService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('admin.section', 'certificates');

        $request->validate([
            'tab' => ['nullable', 'string', 'in:all,payment_pending,presidium,print,collection,completed'],
            'q' => ['nullable', 'string', 'max:255'],
        ]);

        $tab = (string) $request->input('tab', 'payment_pending');
        $query = CertificateApplication::query()
            ->with(['user.province', 'course:id,title,code'])
            ->orderByDesc('exam_passed_at');

        $this->adminScopeService->applyToCertificateApplicationQuery($query, $request->user());

        $statuses = CertificateApplicationService::statusesForTab($tab);
        if ($statuses !== []) {
            $query->whereIn('status', $statuses);
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('receipt_number', 'like', "%{$q}%")
                    ->orWhere('payment_reference_code', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($u) use ($q) {
                        $u->where('name', 'like', "%{$q}%")
                            ->orWhere('surname', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        $applications = $query->paginate(25)->withQueryString();

        $countsQuery = CertificateApplication::query();
        $this->adminScopeService->applyToCertificateApplicationQuery($countsQuery, $request->user());
        $allStatuses = $countsQuery->pluck('status')->map(fn ($s) => $s->value ?? (string) $s);

        $tabCounts = [
            'payment_pending' => $allStatuses->filter(fn ($s) => $s === 'payment_pending')->count(),
            'presidium' => $allStatuses->filter(fn ($s) => $s === 'presidium_pending')->count(),
            'print' => $allStatuses->filter(fn ($s) => $s === 'print_ready')->count(),
            'collection' => $allStatuses->filter(fn ($s) => in_array($s, ['printed', 'ready_for_collection'], true))->count(),
            'completed' => $allStatuses->filter(fn ($s) => $s === 'collected')->count(),
        ];

        return view('admin.certificate-applications.index', compact('applications', 'tab', 'tabCounts'));
    }

    public function show(CertificateApplication $application): View
    {
        $this->authorize('admin.section', 'certificates');
        $this->assertCanAccess($application);

        $application->load([
            'user.province',
            'course',
            'certificate',
            'paymentConfirmedBy:id,name,surname',
            'presidiumApprovedBy:id,name,surname',
            'printedBy:id,name,surname',
            'collectedBy:id,name,surname',
        ]);

        return view('admin.certificate-applications.show', compact('application'));
    }

    public function confirmPayment(Request $request, CertificateApplication $application): RedirectResponse
    {
        $this->authorize('admin.action', 'academy_payment_confirm');
        $this->assertCanAccess($application);

        $data = $request->validate([
            'payment_reference_note' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $this->applicationService->confirmPayment(
                $application,
                $request->user(),
                $data['payment_reference_note'] ?? null
            );
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.certificate-applications.show', $application)
            ->with('success', 'Payment confirmed. Application is awaiting Presidium approval.');
    }

    public function presidiumApprove(Request $request, CertificateApplication $application): RedirectResponse
    {
        $this->authorize('admin.action', 'academy_certificate_presidium_approve');
        $this->assertCanAccess($application);

        $data = $request->validate([
            'presidium_note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->applicationService->presidiumApprove(
                $application,
                $request->user(),
                $data['presidium_note'] ?? null
            );
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.certificate-applications.show', $application)
            ->with('success', 'Presidium approved. Certificate PDF generation has started.');
    }

    public function markPrinted(Request $request, CertificateApplication $application): RedirectResponse
    {
        $this->authorize('admin.action', 'academy_certificate_print');
        $this->assertCanAccess($application);

        try {
            $this->applicationService->markPrinted($application, $request->user());
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.certificate-applications.show', $application)
            ->with('success', 'Certificate marked as printed.');
    }

    public function markReadyForCollection(Request $request, CertificateApplication $application): RedirectResponse
    {
        $this->authorize('admin.action', 'academy_certificate_collection');
        $this->assertCanAccess($application);

        $data = $request->validate([
            'collection_office' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $this->applicationService->markReadyForCollection(
                $application,
                $request->user(),
                $data['collection_office'] ?? null
            );
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.certificate-applications.show', $application)
            ->with('success', 'Applicant notified path: certificate ready for collection.');
    }

    public function markCollected(Request $request, CertificateApplication $application): RedirectResponse
    {
        $this->authorize('admin.action', 'academy_certificate_collection');
        $this->assertCanAccess($application);

        try {
            $this->applicationService->markCollected($application, $request->user());
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.certificate-applications.show', $application)
            ->with('success', 'Certificate marked as collected.');
    }

    public function downloadCertificate(CertificateApplication $application): StreamedResponse|RedirectResponse
    {
        $this->authorize('admin.action', 'academy_certificate_print');
        $this->assertCanAccess($application);

        $application->loadMissing('certificate');
        $certificate = $application->certificate;

        if (! $certificate instanceof Certificate) {
            return back()->with('error', 'No certificate linked yet. Presidium approval is required.');
        }

        if ($certificate->pdf_status === 'ready' && $certificate->pdf_path && Storage::exists($certificate->pdf_path)) {
            $filename = 'certificate-'.$certificate->certificate_number.'.pdf';

            return response()->streamDownload(
                fn () => print(Storage::get($certificate->pdf_path)),
                $filename,
                ['Content-Type' => 'application/pdf'],
                'inline'
            );
        }

        if ($this->certificatePdfService->canGenerate()) {
            $certificate->load(['user', 'course']);
            $pdfContent = $this->certificatePdfService->generate($certificate);
            $filename = 'certificate-'.$certificate->certificate_number.'.pdf';

            return response()->streamDownload(
                fn () => print($pdfContent),
                $filename,
                ['Content-Type' => 'application/pdf'],
                'inline'
            );
        }

        return back()->with('error', 'Certificate PDF is not ready yet.');
    }

    private function assertCanAccess(CertificateApplication $application): void
    {
        $application->loadMissing('user');
        $this->adminScopeService->assertCanAccessUser(auth()->user(), $application->user);
    }
}
