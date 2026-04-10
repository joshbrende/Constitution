<?php

namespace App\Services\GovIdVerification;

final class GovIdVerificationResult
{
    public function __construct(
        public readonly bool $verified,
        public readonly string $status, // verified | unverified | unavailable
        public readonly ?string $reference = null,
        public readonly ?string $reason = null,
    ) {}
}

