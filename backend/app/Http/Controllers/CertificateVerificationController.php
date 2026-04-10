<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Support\Facades\Cache;

class CertificateVerificationController extends Controller
{
    private const VERIFY_CACHE_TTL_SECONDS = 300; // 5 minutes

    /**
     * Public verification page. Verification code is mandatory.
     * If a signed token is provided (from QR), it is validated server-side.
     * Result cached to smooth demand on viral shares.
     */
    public function show()
    {
        $publicId = substr(trim((string) request('id', '')), 0, 36);
        $number = substr(trim((string) request('number', '')), 0, 50);
        $code = strtoupper(substr(trim((string) request('code', '')), 0, 12));
        $token = strtolower(substr(trim((string) request('token', '')), 0, 64));
        $certificate = null;
        $invalid = false;
        $status = null;
        $tokenValid = null;
        $isActive = false;

        if ($number !== '' || $publicId !== '') {
            if ($code === '') {
                $invalid = true;
                return view('certificate-verify', compact('certificate', 'invalid', 'number', 'code', 'publicId', 'token', 'status', 'tokenValid', 'isActive'));
            }

            $cacheKey = 'certificate.verify:' . $publicId . ':' . $number . ':' . $code . ':' . $token;
            $certificate = Cache::remember($cacheKey, self::VERIFY_CACHE_TTL_SECONDS, function () use ($publicId, $number, $code) {
                $query = Certificate::query()
                    ->with(['user', 'course']);
                if ($publicId !== '') {
                    $query->where('public_id', $publicId);
                } else {
                    $query->where('certificate_number', $number);
                }
                $query->where('verification_code', $code);
                return $query->first();
            });

            if (! $certificate) {
                $invalid = true;
            } else {
                $status = $certificate->verificationStatus();
                $isActive = $status === 'active';
                if ($token !== '') {
                    $tokenValid = $certificate->hasValidVerificationToken($token);
                    if (! $tokenValid) {
                        $invalid = true;
                        $certificate = null;
                        $status = null;
                        $isActive = false;
                    }
                }
            }
        }

        return view('certificate-verify', compact('certificate', 'invalid', 'number', 'code', 'publicId', 'token', 'status', 'tokenValid', 'isActive'));
    }
}
