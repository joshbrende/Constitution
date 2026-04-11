<?php

namespace App\Services\GovIdVerification;

use App\Support\GovIntegrationClient;
use Illuminate\Support\Facades\Log;

/**
 * Stub client for Zimbabwe Govt National ID verification.
 *
 * This is intentionally non-functional until the government portal API
 * details are provided. It provides a stable interface so we can wire policy
 * decisions now and swap the internals later.
 */
final class GovIdVerificationClient
{
    public function verifyNationalId(string $nationalId): GovIdVerificationResult
    {
        if (trim($nationalId) === '') {
            return new GovIdVerificationResult(
                verified: false,
                status: 'unverified',
                reference: null,
                reason: 'National ID is required',
            );
        }

        $baseUrl = (string) config('services.gov_id.base_url', '');
        if ($baseUrl === '') {
            return new GovIdVerificationResult(
                verified: false,
                status: 'unavailable',
                reference: null,
                reason: 'Gov ID portal not configured',
            );
        }

        // Placeholder for future implementation.
        // When spec arrives, this will call the internal government portal through
        // GovIntegrationClient::forBaseUrl($baseUrl) with mTLS/JWT as required.
        try {
            GovIntegrationClient::forBaseUrl($baseUrl);
        } catch (\Throwable $e) {
            Log::warning('gov_id_verify_blocked', ['message' => $e->getMessage()]);
            return new GovIdVerificationResult(
                verified: false,
                status: 'unavailable',
                reference: null,
                reason: 'Gov ID portal blocked by allowlist',
            );
        }

        return new GovIdVerificationResult(
            verified: false,
            status: 'unverified',
            reference: null,
            reason: 'Gov ID portal integration not implemented yet',
        );
    }
}

