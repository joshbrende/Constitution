<?php

namespace App\Services;

use App\Models\CertificateApplication;
use App\Models\Province;
use App\Models\User;

class AcademyPaymentOfficeService
{
    /**
     * @return list<array{name: string, address: string, phone?: string, hours?: string}>
     */
    public function officesForUser(?User $user): array
    {
        $offices = config('academy.payment_offices', []);
        if ($offices === []) {
            return [];
        }

        $provinceCode = null;
        if ($user?->province_id) {
            $provinceCode = Province::query()->whereKey($user->province_id)->value('code');
        }

        $matched = [];
        $fallback = [];
        foreach ($offices as $office) {
            $codes = $office['province_codes'] ?? null;
            if ($codes === null) {
                $fallback[] = $this->normalizeOffice($office);
                continue;
            }
            if ($provinceCode && in_array($provinceCode, $codes, true)) {
                $matched[] = $this->normalizeOffice($office);
            }
        }

        if ($matched !== []) {
            return array_merge($matched, $fallback);
        }

        return array_map(fn ($office) => $this->normalizeOffice($office), $offices);
    }

    public function paymentInstructions(CertificateApplication $application): string
    {
        $courseInstructions = trim((string) ($application->course?->payment_office_instructions ?? ''));
        if ($courseInstructions !== '') {
            return $courseInstructions;
        }

        return (string) config('academy.default_payment_instructions', '');
    }

    /**
     * @param  array<string, mixed>  $office
     * @return array{name: string, address: string, phone?: string, hours?: string}
     */
    private function normalizeOffice(array $office): array
    {
        $normalized = [
            'name' => (string) ($office['name'] ?? ''),
            'address' => (string) ($office['address'] ?? ''),
        ];
        if (! empty($office['phone'])) {
            $normalized['phone'] = (string) $office['phone'];
        }
        if (! empty($office['hours'])) {
            $normalized['hours'] = (string) $office['hours'];
        }

        return $normalized;
    }
}
