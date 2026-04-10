<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\RefreshToken;
use Illuminate\Console\Command;

class CleanupSecurityDataCommand extends Command
{
    protected $signature = 'ops:cleanup-security-data {--dry-run : Report counts without deleting}';

    protected $description = 'Prune aged audit logs and expired/revoked refresh tokens.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

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
            $this->line('Audit logs to delete: ' . $auditCount);
            $this->line('Refresh tokens to delete: ' . $tokenCount);
            return self::SUCCESS;
        }

        $deletedAudit = $auditQuery->delete();
        $deletedTokens = $tokenQuery->delete();

        $this->info('Cleanup complete.');
        $this->line('Deleted audit logs: ' . $deletedAudit);
        $this->line('Deleted refresh tokens: ' . $deletedTokens);

        return self::SUCCESS;
    }
}

