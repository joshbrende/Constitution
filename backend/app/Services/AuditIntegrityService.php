<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AuditIntegrityService
{
    /**
     * @param  array<string, mixed>  $attributes
     * @return array{previous_hash: ?string, integrity_hash: ?string}
     */
    public function hashAttributesForInsert(array $attributes): array
    {
        if (! config('audit.integrity.enabled', true)) {
            return ['previous_hash' => null, 'integrity_hash' => null];
        }

        return DB::connection(AuditLog::query()->getConnection()->getName())->transaction(function () use ($attributes) {
            $previous = AuditLog::query()->orderByDesc('id')->lockForUpdate()->first();
            $previousHash = $previous?->integrity_hash;
            $integrityHash = $this->computeHash($previousHash, $attributes);

            return [
                'previous_hash' => $previousHash,
                'integrity_hash' => $integrityHash,
            ];
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function computeHash(?string $previousHash, array $attributes): string
    {
        $payload = json_encode($this->normalizePayload($attributes), JSON_THROW_ON_ERROR);
        $seed = ($previousHash ?? 'GENESIS').'|'.$payload;

        return hash('sha256', $seed);
    }

    /**
     * Verify hash chain for a range of audit log IDs (inclusive).
     *
     * @return array{valid: bool, broken_at_id: ?int, checked: int}
     */
    public function verifyChain(?int $fromId = null, ?int $toId = null): array
    {
        if (! config('audit.integrity.enabled', true)) {
            return ['valid' => true, 'broken_at_id' => null, 'checked' => 0];
        }

        $query = AuditLog::query()->orderBy('id');
        if ($fromId !== null) {
            $query->where('id', '>=', $fromId);
        }
        if ($toId !== null) {
            $query->where('id', '<=', $toId);
        }

        $previousHash = null;
        $checked = 0;

        foreach ($query->cursor() as $log) {
            $checked++;

            if ($log->integrity_hash === null) {
                continue;
            }

            $expectedPrevious = $previousHash;
            if ($log->previous_hash !== $expectedPrevious) {
                return ['valid' => false, 'broken_at_id' => $log->id, 'checked' => $checked];
            }

            $expectedHash = $this->computeHash($expectedPrevious, $this->attributesFromLog($log));
            if ($log->integrity_hash !== $expectedHash) {
                return ['valid' => false, 'broken_at_id' => $log->id, 'checked' => $checked];
            }

            $previousHash = $log->integrity_hash;
        }

        return ['valid' => true, 'broken_at_id' => null, 'checked' => $checked];
    }

    /**
     * @param  Builder<AuditLog>  $query
     */
    public function verifyQueryChain(Builder $query): array
    {
        $clone = clone $query;
        $first = (clone $clone)->orderBy('id')->first();
        $last = (clone $clone)->orderByDesc('id')->first();

        if (! $first || ! $last) {
            return ['valid' => true, 'broken_at_id' => null, 'checked' => 0];
        }

        return $this->verifyChain($first->id, $last->id);
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function normalizePayload(array $attributes): array
    {
        ksort($attributes);

        $normalized = [];
        foreach ($attributes as $key => $value) {
            if ($value instanceof \DateTimeInterface) {
                $normalized[$key] = $value->format(\DateTimeInterface::ATOM);

                continue;
            }
            if (is_array($value)) {
                $normalized[$key] = $this->normalizeArray($value);

                continue;
            }
            $normalized[$key] = $value;
        }

        return $normalized;
    }

    /**
     * @param  array<mixed>  $value
     * @return array<mixed>
     */
    private function normalizeArray(array $value): array
    {
        ksort($value);

        foreach ($value as $k => $v) {
            if (is_array($v)) {
                $value[$k] = $this->normalizeArray($v);
            }
        }

        return $value;
    }

    /**
     * @return array<string, mixed>
     */
    private function attributesFromLog(AuditLog $log): array
    {
        return [
            'actor_user_id' => $log->actor_user_id,
            'action' => $log->action,
            'target_type' => $log->target_type,
            'target_id' => $log->target_id,
            'metadata' => $log->metadata ?? [],
            'ip_address' => $log->ip_address,
            'user_agent' => $log->user_agent,
            'request_id' => $log->request_id,
            'created_at' => $log->created_at,
        ];
    }
}
