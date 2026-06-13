<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Services\AuditIntegrityService;
use Illuminate\Console\Command;

class AuditVerifyCommand extends Command
{
    protected $signature = 'audit:verify
                            {--from= : Starting audit log id}
                            {--to= : Ending audit log id}';

    protected $description = 'Verify audit log SHA-256 hash chain integrity.';

    public function handle(AuditIntegrityService $integrityService): int
    {
        $from = $this->option('from') !== null ? (int) $this->option('from') : null;
        $to = $this->option('to') !== null ? (int) $this->option('to') : null;

        $result = $integrityService->verifyChain($from, $to);

        if ($result['checked'] === 0) {
            $this->warn('No audit logs to verify.');

            return self::SUCCESS;
        }

        if ($result['valid']) {
            $this->info('Integrity chain valid ('.$result['checked'].' rows checked).');

            return self::SUCCESS;
        }

        $this->error('Integrity chain broken at id '.$result['broken_at_id'].' ('.$result['checked'].' rows checked).');

        return self::FAILURE;
    }
}
