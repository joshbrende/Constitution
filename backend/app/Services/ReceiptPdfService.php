<?php

namespace App\Services;

use App\Models\CertificateApplication;
use App\Models\User;

class ReceiptPdfService
{
    public function __construct(
        protected AcademyPaymentOfficeService $paymentOfficeService
    ) {}

    public function canGenerate(): bool
    {
        return class_exists(\TCPDF::class);
    }

    public function generate(CertificateApplication $application): string
    {
        if (! $this->canGenerate()) {
            throw new \RuntimeException('PDF library not available.');
        }

        $application->loadMissing(['user.province', 'course']);

        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        $user = $application->user;
        $course = $application->course;
        $offices = $this->paymentOfficeService->officesForUser($user);
        $instructions = $this->paymentOfficeService->paymentInstructions($application);

        $html = view('pdf.payment-receipt', [
            'application' => $application,
            'user' => $user,
            'course' => $course,
            'nationalIdMasked' => $this->maskNationalId((string) ($user?->national_id ?? '')),
            'provinceName' => $user?->province?->name,
            'offices' => $offices,
            'instructions' => $instructions,
            'verifyUrl' => $this->verifyUrl($application),
        ])->render();

        $pdf->writeHTML($html, true, false, true, false, '');

        return $pdf->Output('', 'S');
    }

    public function maskNationalId(string $nationalId): string
    {
        $nationalId = trim($nationalId);
        if ($nationalId === '') {
            return '—';
        }

        $parts = preg_split('/[-\s]+/', $nationalId) ?: [];
        if (count($parts) < 2) {
            return str_repeat('*', max(0, strlen($nationalId) - 4)).substr($nationalId, -4);
        }

        $masked = [];
        foreach ($parts as $i => $part) {
            if ($i === 0 || $i === count($parts) - 1) {
                $masked[] = $part;
                continue;
            }
            $masked[] = str_repeat('*', max(2, strlen($part)));
        }

        return implode('-', $masked);
    }

    private function verifyUrl(CertificateApplication $application): string
    {
        return rtrim((string) config('app.url'), '/').'/verify-receipt/'.$application->public_id;
    }
}
