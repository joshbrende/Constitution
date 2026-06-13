<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\RefreshToken;
use App\Services\AuditArchiveService;
use App\Services\AuditLogger;
use Illuminate\Console\Command;

class CleanupSecurityDataCommand extends Command
{
    protected $signature = 'ops:cleanup-security-data
                            {--dry-run : Report counts without deleting}
                            {--skip-archive : Skip JSONL export before audit purge (not for production)}';

    protected $description = 'Archive and prune aged audit logs; prune expired refresh tokens.';

    public function handle(AuditArchiveService $archiveService, AuditLogger $auditLogger): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $skipArchive = (bool) $this->option('skip-archive');

        $auditRetentionDays = (int) config('operations.cleanup.audit_log_retention_days', 365);
        $tokenRetentionDays = (int) config('operations.cleanup.refresh_token_retention_days', 30);

        $auditCutoff = now()->subDays(max(1, $auditRetentionDays));
        $tokenCutoff = now()->subDays(max(1, $tokenRetentionDays));

        $auditQuery = AuditLog::query()->where('created_at', '<', $auditCutoff);
        $tokenQuery = RefreshToken::query()
            ->where(function ($q) {
                $q->whereNotNull('revoked_at')
                    ->orWhere('expires_at', '<', now());
            })
            ->where('updated_at', '<', $tokenCutoff);

        $auditCount = (clone $auditQuery)->count();
        $tokenCount = (clone $tokenQuery)->count();

        if ($dryRun) {
            $this->info('Dry run complete.');
            $this->line('Audit logs to delete: '.$auditCount);
            $this->line('Refresh tokens to delete: '.$tokenCount);

            return self::SUCCESS;
        }

        $archivePath = null;
        if ($auditCount > 0) {
            $requireArchive = (bool) config('audit.archive.require_before_purge', true);
            if ($requireArchive && ! $skipArchive) {
                $result = $archiveService->exportQueryToJsonl($auditQuery, 'purge');
                $archivePath = $result['path'];
                $this->line('Archived audit logs: '.$result['count'].' → storage/app/'.$archivePath);
            } elseif ($requireArchive && $skipArchive) {
                $this->warn('Skipping audit archive (--skip-archive). Not recommended for production.');
            }

            $deletedAudit = AuditLog::allowingMutation(function () use ($auditQuery) {
                $deleted = 0;
                foreach ((clone $auditQuery)->orderBy('id')->lazyById() as $log) {
                    $log->delete();
                    $deleted++;
                }

                return $deleted;
            });
            $this->line('Deleted audit logs: '.$deletedAudit);

            $auditLogger->log(
                action: 'audit_logs.purged',
                targetType: AuditLog::class,
                metadata: [
                    'deleted_count' => $deletedAudit,
                    'cutoff' => $auditCutoff->toIso8601String(),
                    'archive_path' => $archivePath,
                ],
            );
        } else {
            $this->line('Deleted audit logs: 0');
        }

        $deletedTokens = $tokenQuery->delete();
        $this->line('Deleted refresh tokens: '.$deletedTokens);

        $this->info('Cleanup complete.');

        return self::SUCCESS;
    }
}
