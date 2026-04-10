<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\Unit;
use App\Services\StepContentParser;
use Illuminate\Console\Command;

class PerformanceSyncDocsCommand extends Command
{
    protected $signature = 'performance:sync-docs
                            {--dry-run : Show what would be updated without writing}';

    protected $description = 'Sync Module 1 unit content from performance-C1/docs into the Performance Management course';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $docsBase = base_path('public/performance-C1/docs');
        $manifestPath = $docsBase . DIRECTORY_SEPARATOR . 'module-1-sections.php';

        if (!is_file($manifestPath)) {
            $this->error('Manifest not found: ' . $manifestPath);
            return self::FAILURE;
        }

        $sections = require $manifestPath;
        if (!is_array($sections)) {
            $this->error('Manifest must return an array.');
            return self::FAILURE;
        }

        $course = Course::where('slug', 'like', '%performance-management%')
            ->orWhere('title', 'like', '%Performance Management%')
            ->first();

        if (!$course) {
            $this->error('Performance Management course not found.');
            return self::FAILURE;
        }

        $this->info('Course: ' . $course->title . ' (id=' . $course->id . ')');
        if ($dryRun) {
            $this->warn('DRY RUN — no changes will be written.');
        }

        $updated = 0;
        $missing = 0;

        foreach ($sections as $cfg) {
            $file = $cfg['file'] ?? null;
            $title = $cfg['title'] ?? null;
            $duration = (int) ($cfg['duration'] ?? 0);

            if (!$file || !$title) {
                $this->warn('Skipping invalid entry: ' . json_encode($cfg));
                continue;
            }

            $path = $docsBase . DIRECTORY_SEPARATOR . $file;
            if (!is_file($path)) {
                $this->warn("File not found: {$file}");
                $missing++;
                continue;
            }

            $md = file_get_contents($path);
            $md = str_replace('**Time Slot:**', 'Time Slot:', $md);
            $html = StepContentParser::toHtml($md);

            $unit = Unit::where('course_id', $course->id)
                ->where('title', $title)
                ->first();

            if (!$unit) {
                $this->warn("Unit not found: {$title}");
                $missing++;
                continue;
            }

            if ($dryRun) {
                $this->line("Would update: {$title} (duration={$duration})");
                $updated++;
                continue;
            }

            $unit->update([
                'content' => $html,
                'duration' => $duration > 0 ? $duration : $unit->duration,
            ]);
            $this->line("Updated: {$title}");
            $updated++;
        }

        $this->newLine();
        $this->info("Done. Updated: {$updated}; missing/skipped: {$missing}.");
        return self::SUCCESS;
    }
}
