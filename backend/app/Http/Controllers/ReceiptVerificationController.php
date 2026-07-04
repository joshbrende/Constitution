<?php

namespace App\Http\Controllers;

use App\Models\CertificateApplication;
use Illuminate\View\View;

class ReceiptVerificationController extends Controller
{
    /**
     * Public receipt verification (finance / provincial office lookup).
     */
    public function show(?string $publicId = null): View
    {
        $receipt = trim((string) request('receipt', ''));
        $paymentRef = strtoupper(trim((string) request('ref', '')));
        $publicId = $publicId ?? trim((string) request('id', ''));

        $application = null;
        if ($publicId !== '') {
            $application = CertificateApplication::query()
                ->where('public_id', $publicId)
                ->first();
        } elseif ($receipt !== '') {
            $application = CertificateApplication::query()
                ->where('receipt_number', $receipt)
                ->when($paymentRef !== '', fn ($q) => $q->where('payment_reference_code', $paymentRef))
                ->first();
        } elseif ($paymentRef !== '') {
            $application = CertificateApplication::query()
                ->where('payment_reference_code', $paymentRef)
                ->first();
        }

        return view('receipt-verify', [
            'receipt' => $receipt,
            'paymentRef' => $paymentRef,
            'publicId' => $publicId,
            'application' => $application,
            'verified' => $application !== null,
        ]);
    }
}
