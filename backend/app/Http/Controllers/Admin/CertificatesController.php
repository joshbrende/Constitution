<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificatesController extends Controller
{
    public function __construct(
        protected AuditLogger $auditLogger
    ) {}

    /**
     * List all certificates for admin verification. Search by certificate number.
     */
    public function index(Request $request): View
    {
        $query = Certificate::with(['user', 'course'])->orderByDesc('issued_at');

        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'q' => ['nullable', 'string', 'max:255'],
            'number' => ['nullable', 'string', 'max:255'],
            'search_mode' => ['nullable', 'string', 'in:certificate_number,member_name,verification_code'],
        ]);

        if ($request->filled('number')) {
            $query->where('certificate_number', 'like', '%' . $request->input('number') . '%');
        }

        // Dedicated search dropdown:
        // - certificate_number
        // - member_name (name/surname/email)
        // - verification_code
        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $mode = (string) $request->input('search_mode', 'member_name');
            $query->where(function ($sub) use ($q, $mode) {
                if ($mode === 'certificate_number') {
                    $sub->where('certificate_number', 'like', "%{$q}%");
                    return;
                }

                if ($mode === 'verification_code') {
                    $sub->where('verification_code', 'like', "%{$q}%");
                    return;
                }

                // member_name (default)
                $sub->whereHas('user', function ($u) use ($q) {
                    $u->where('name', 'like', "%{$q}%")
                        ->orWhere('surname', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('issued_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('issued_at', '<=', $request->input('to'));
        }

        $certificates = $query->paginate(25)->withQueryString();

        return view('admin.certificates.index', compact('certificates'));
    }

    public function revoke(Request $request, Certificate $certificate): RedirectResponse
    {
        $this->authorize('admin.section', 'certificates');
        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $certificate->update([
            'revoked_at' => now(),
            'revoked_by_user_id' => auth()->id(),
            'revoked_reason' => $data['reason'] ?? null,
        ]);

        $this->auditLogger->log(
            action: 'certificate.revoked',
            targetType: Certificate::class,
            targetId: $certificate->id,
            metadata: [
                'certificate_number' => $certificate->certificate_number,
                'reason' => $data['reason'] ?? null,
            ],
            request: $request
        );

        return redirect()
            ->route('admin.certificates.index')
            ->with('success', 'Certificate revoked successfully.');
    }

    public function unrevoke(Request $request, Certificate $certificate): RedirectResponse
    {
        $this->authorize('admin.section', 'certificates');
        $priorReason = $certificate->revoked_reason;
        $certificate->update([
            'revoked_at' => null,
            'revoked_by_user_id' => null,
            'revoked_reason' => null,
        ]);

        $this->auditLogger->log(
            action: 'certificate.reinstated',
            targetType: Certificate::class,
            targetId: $certificate->id,
            metadata: [
                'certificate_number' => $certificate->certificate_number,
                'previous_reason' => $priorReason,
            ],
            request: $request
        );

        return redirect()
            ->route('admin.certificates.index')
            ->with('success', 'Certificate reinstated successfully.');
    }
}
