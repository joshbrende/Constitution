<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Services\AuditArchiveService;
use App\Services\AuditIntegrityService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class AuditExportCommand extends Command
{
    protected $signature = 'audit:export
                            {--from= : ISO date (inclusive) filter on created_at}
                            {--to= : ISO date (inclusive) filter on created_at}
                            {--verify : Verify integrity hash chain for exported range}';

    protected $description = 'Export audit logs to JSONL archive (compliance / cold storage).';

    public function handle(AuditArchiveService $archiveService, AuditIntegrityService $integrityService): int
    {
        $query = AuditLog::query()->orderBy('id');

        if ($this->option('from')) {
            $query->where('created_at', '>=', Carbon::parse((string) $this->option('from'))->startOfDay());
        }
        if ($this->option('to')) {
            $query->where('created_at', '<=', Carbon::parse((string) $this->option('to'))->endOfDay());
        }

        $count = (clone $query)->count();
        if ($count === 0) {
            $this->warn('No audit logs match the export filters.');

            return self::SUCCESS;
        }

        if ($this->option('verify')) {
            $result = $integrityService->verifyQueryChain($query);
            if (! $result['valid']) {
                $this->error('Integrity chain invalid before export at id '.$result['broken_at_id']);

                return self::FAILURE;
            }
            $this->info('Integrity chain verified ('.$result['checked'].' rows).');
        }

        $export = $archiveService->exportQueryToJsonl($query, 'export');
        $this->info('Exported '.$export['count'].' rows to storage/app/'.$export['path']);

        return self::SUCCESS;
    }
}
