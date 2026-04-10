<?php

namespace App\Console\Commands;

use App\Models\Certificate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class QueueHealthCheckCommand extends Command
{
    protected $signature = 'ops:queue-health {--json : Output JSON summary}';

    protected $description = 'Check queue and async certificate generation health.';

    public function handle(): int
    {
        $maxFailed = (int) config('operations.queue_health.max_failed_jobs', 10);
        $staleMinutes = (int) config('operations.queue_health.stale_certificate_minutes', 30);
        $staleCutoff = now()->subMinutes(max(1, $staleMinutes));

        $failedJobs = Schema::hasTable('failed_jobs')
            ? (int) DB::table('failed_jobs')->count()
            : 0;

        $stalePending = Schema::hasTable('certificates')
            ? Certificate::query()
                ->whereIn('pdf_status', ['pending', 'generating'])
                ->where('updated_at', '<=', $staleCutoff)
                ->count()
            : 0;

        $status = ($failedJobs > $maxFailed || $stalePending > 0) ? 'degraded' : 'healthy';
        $summary = [
            'status' => $status,
            'failed_jobs' => $failedJobs,
            'max_failed_jobs' => $maxFailed,
            'stale_pending_certificates' => $stalePending,
            'stale_threshold_minutes' => $staleMinutes,
            'checked_at' => now()->toIso8601String(),
        ];

        if ($this->option('json')) {
            $this->line(json_encode($summary, JSON_UNESCAPED_SLASHES));
        } else {
            $this->info('Queue health: ' . strtoupper($status));
            $this->line('Failed jobs: ' . $failedJobs . ' (threshold ' . $maxFailed . ')');
            $this->line('Stale pending/generating certificates: ' . $stalePending . ' (older than ' . $staleMinutes . ' min)');
        }

        return $status === 'healthy' ? self::SUCCESS : self::FAILURE;
    }
}

