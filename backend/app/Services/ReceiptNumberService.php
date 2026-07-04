<?php

namespace App\Services;

use App\Models\CertificateApplication;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Structured receipt and payment reference numbers for finance tracking.
 *
 * Receipt:  ZPF-REC-{YYYY}-{PROV}-{SEQ6}   e.g. ZPF-REC-2026-HAR-000042
 * Payment:  {PROV}-{SEQ6}-{CHK2}           e.g. HAR-000042-K9
 */
class ReceiptNumberService
{
    /**
     * @return array{receipt_number: string, payment_reference_code: string}
     */
    public function generateForUser(User $user): array
    {
        $user->loadMissing('province');

        $prefix = (string) config('academy.receipt_number_prefix', 'ZPF-REC');
        $year = (string) date('Y');
        $provinceCode = $this->provinceCode($user);
        $sequence = $this->nextSequence($year, $provinceCode);

        $receiptNumber = sprintf('%s-%s-%s-%06d', $prefix, $year, $provinceCode, $sequence);
        $paymentReference = $this->paymentReference($provinceCode, $sequence, $receiptNumber);

        if (CertificateApplication::where('receipt_number', $receiptNumber)->exists()
            || CertificateApplication::where('payment_reference_code', $paymentReference)->exists()) {
            throw new \RuntimeException('Receipt number collision; retry application creation.');
        }

        return [
            'receipt_number' => $receiptNumber,
            'payment_reference_code' => $paymentReference,
        ];
    }

    public function provinceCode(?User $user): string
    {
        $code = strtolower(trim((string) ($user?->province?->code ?? '')));
        if ($code === '') {
            return 'NAT';
        }

        return strtoupper(substr(preg_replace('/[^a-z]/', '', $code) ?: 'nat', 0, 3));
    }

    private function nextSequence(string $year, string $provinceCode): int
    {
        $prefix = (string) config('academy.receipt_number_prefix', 'ZPF-REC');
        $pattern = sprintf('%s-%s-%s-%%', $prefix, $year, $provinceCode);

        $latest = CertificateApplication::query()
            ->where('receipt_number', 'like', $pattern)
            ->orderByDesc('receipt_number')
            ->value('receipt_number');

        if (is_string($latest) && preg_match('/-(\d{6})$/', $latest, $m)) {
            return ((int) $m[1]) + 1;
        }

        return CertificateApplication::query()
            ->where('receipt_number', 'like', $pattern)
            ->count() + 1;
    }

    private function paymentReference(string $provinceCode, int $sequence, string $receiptNumber): string
    {
        $base = sprintf('%s-%06d', $provinceCode, $sequence);
        $checksum = $this->checksum2($receiptNumber);

        return $base.'-'.$checksum;
    }

    /** Two-character checksum for manual teller verification. */
    public function checksum2(string $receiptNumber): string
    {
        $hash = strtoupper(substr(hash('crc32b', $receiptNumber), 0, 4));
        $chars = '0123456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $n = hexdec(substr($hash, 0, 4)) % (strlen($chars) * strlen($chars));

        return $chars[(int) floor($n / strlen($chars))].$chars[$n % strlen($chars)];
    }

    public function receiptLogoPath(): ?string
    {
        $configured = trim((string) config('academy.receipt_logo_path', ''));
        $candidates = array_filter([
            $configured !== '' ? public_path(ltrim($configured, '/')) : null,
            public_path('download.png'),
            public_path('favicon_io/android-chrome-192x192.png'),
            public_path('icon-1.png'),
        ]);

        foreach ($candidates as $path) {
            if (is_file($path) && is_readable($path)) {
                return $path;
            }
        }

        return null;
    }
}
