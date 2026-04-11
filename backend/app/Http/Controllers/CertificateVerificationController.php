<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CertificateVerificationController extends Controller
{
    private const VERIFY_CACHE_TTL_SECONDS = 300; // 5 minutes

    /**
     * Public verification page. Verification code is mandatory.
     * If a signed token is provided (from QR), it is validated server-side.
     * Result cached to smooth demand on viral shares.
     */
    public function show(): View
    {
        $query = $this->readVerificationInputs();

        return view('certificate-verify', $this->buildVerifyPayload($query));
    }

    /**
     * @return array{publicId: string, number: string, code: string, token: string}
     */
    private function readVerificationInputs(): array
    {
        return [
            'publicId' => substr(trim((string) request('id', '')), 0, 36),
            'number' => substr(trim((string) request('number', '')), 0, 50),
            'code' => strtoupper(substr(trim((string) request('code', '')), 0, 12)),
            'token' => strtolower(substr(trim((string) request('token', '')), 0, 64)),
        ];
    }

    /**
     * @param  array{publicId: string, number: string, code: string, token: string}  $query
     * @return array<string, mixed>
     */
    private function buildVerifyPayload(array $query): array
    {
        $emptyState = $this->emptyVerifyState($query);

        if ($query['number'] === '' && $query['publicId'] === '') {
            return $emptyState;
        }

        if ($query['code'] === '') {
            return array_merge($emptyState, ['invalid' => true]);
        }

        $certificate = $this->resolveCertificateFromCache($query);

        if (! $certificate) {
            return array_merge($emptyState, ['invalid' => true]);
        }

        $status = $certificate->verificationStatus();
        $isActive = $status === 'active';
        $tokenValid = null;

        if ($query['token'] !== '') {
            $tokenValid = $certificate->hasValidVerificationToken($query['token']);
            if (! $tokenValid) {
                return array_merge($emptyState, [
                    'invalid' => true,
                    'certificate' => null,
                    'status' => null,
                    'tokenValid' => false,
                    'isActive' => false,
                ]);
            }
        }

        return array_merge($emptyState, [
            'certificate' => $certificate,
            'invalid' => false,
            'status' => $status,
            'tokenValid' => $tokenValid,
            'isActive' => $isActive,
        ]);
    }

    /**
     * @param  array{publicId: string, number: string, code: string, token: string}  $query
     * @return array<string, mixed>
     */
    private function emptyVerifyState(array $query): array
    {
        return [
            'certificate' => null,
            'invalid' => false,
            'number' => $query['number'],
            'code' => $query['code'],
            'publicId' => $query['publicId'],
            'token' => $query['token'],
            'status' => null,
            'tokenValid' => null,
            'isActive' => false,
        ];
    }

    /**
     * @param  array{publicId: string, number: string, code: string, token: string}  $query
     */
    private function resolveCertificateFromCache(array $query): ?Certificate
    {
        $cacheKey = 'certificate.verify:' . $query['publicId'] . ':' . $query['number'] . ':' . $query['code'] . ':' . $query['token'];

        return Cache::remember($cacheKey, self::VERIFY_CACHE_TTL_SECONDS, function () use ($query) {
            $q = Certificate::query()->with(['user', 'course']);

            if ($query['publicId'] !== '') {
                $q->where('public_id', $query['publicId']);
            } else {
                $q->where('certificate_number', $query['number']);
            }

            return $q->where('verification_code', $query['code'])->first();
        });
    }
}
