<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogger
{
    public function __construct(
        protected AuditIntegrityService $integrityService
    ) {}

    public function log(
        string $action,
        ?string $targetType = null,
        int|string|null $targetId = null,
        array $metadata = [],
        ?Request $request = null
    ): AuditLog {
        $request ??= request();

        $attributes = [
            'actor_user_id' => auth()->id(),
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId !== null ? (int) $targetId : null,
            'metadata' => $metadata,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'request_id' => (string) ($request?->headers->get('X-Request-Id') ?? ''),
            'created_at' => now(),
        ];

        $hashes = $this->integrityService->hashAttributesForInsert($attributes);

        return AuditLog::create(array_merge($attributes, $hashes));
    }
}
