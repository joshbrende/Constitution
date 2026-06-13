<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class AuditArchiveService
{
    /**
     * Export matching audit rows to JSONL under storage/app/audit-archives/.
     *
     * @param  Builder<AuditLog>  $query
     * @return array{path: string, count: int}
     */
    public function exportQueryToJsonl(Builder $query, ?string $label = null): array
    {
        $relativeDir = trim((string) config('audit.archive.path', 'audit-archives'), '/')
            .'/'.now()->format('Y-m-d');
        $filename = 'batch-'.now()->format('His-u').($label ? '-'.$label : '').'.jsonl';
        $relativePath = $relativeDir.'/'.$filename;

        Storage::disk('local')->makeDirectory($relativeDir);

        $absolutePath = Storage::disk('local')->path($relativePath);
        $handle = fopen($absolutePath, 'wb');
        if ($handle === false) {
            throw new \RuntimeException('Unable to create audit archive file.');
        }

        $count = 0;
        try {
            foreach ((clone $query)->orderBy('id')->cursor() as $log) {
                $row = [
                    'id' => $log->id,
                    'actor_user_id' => $log->actor_user_id,
                    'action' => $log->action,
                    'target_type' => $log->target_type,
                    'target_id' => $log->target_id,
                    'metadata' => $log->metadata,
                    'ip_address' => $log->ip_address,
                    'user_agent' => $log->user_agent,
                    'request_id' => $log->request_id,
                    'previous_hash' => $log->previous_hash,
                    'integrity_hash' => $log->integrity_hash,
                    'created_at' => $log->created_at?->toIso8601String(),
                    'updated_at' => $log->updated_at?->toIso8601String(),
                ];
                fwrite($handle, json_encode($row, JSON_THROW_ON_ERROR)."\n");
                $count++;
            }
        } finally {
            fclose($handle);
        }

        return ['path' => $relativePath, 'count' => $count];
    }
}
