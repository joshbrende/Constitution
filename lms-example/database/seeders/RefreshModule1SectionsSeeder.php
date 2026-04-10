<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Unit;
use App\Services\StepContentParser;
use Illuminate\Database\Seeder;

/**
 * Refreshes all Module 1 section units from their .md files.
 * Supports [STEP] syntax for the panning/stepper UI.
 * Run: php artisan db:seed --class=RefreshModule1SectionsSeeder
 */
class RefreshModule1SectionsSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::where('slug', 'like', '%performance-management%')
            ->orWhere('title', 'like', '%Performance Management%')
            ->first();

        if (!$course) {
            $this->command->warn('Performance Management course not found. Skipping.');
            return;
        }

        $sectionsDir = base_path('database/seeders/module1_sections');
        $config = require $sectionsDir . '/sections.php';
        $updated = 0;

        foreach ($config as $c) {
            $path = $sectionsDir . '/' . $c['file'];
            if (!is_file($path)) {
                $this->command->warn('File not found: ' . $c['file']);
                continue;
            }

            $unit = $course->units()->where('title', $c['title'])->first();
            if (!$unit) {
                $this->command->warn('Unit not found: ' . $c['title']);
                continue;
            }

            $md = file_get_contents($path);
            $html = StepContentParser::toHtml($md);

            $unit->update([
                'content' => $html,
                'duration' => (int) ($c['duration'] ?? $unit->duration),
            ]);
            $updated++;
        }

        $this->command->info("Refreshed {$updated} Module 1 section(s).");
    }
}
