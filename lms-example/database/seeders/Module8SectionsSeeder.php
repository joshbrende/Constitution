<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Unit;
use App\Services\StepContentParser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Replaces the "Module 8: Compliance and Risk Monitoring" unit with 15 sections:
 * theory (MFMA compliance, legislative compliance, risk identification and
 * assessment, early warning systems, audit preparation), practical (compliance
 * checklist and tracker, risk register and early warning), AI application
 * (automated compliance checking, risk scoring, early warning for governance
 * risks, audit trail and predictive risk), key takeaways, reflection, and
 * next steps.
 *
 * Run: php artisan db:seed --class=Module8SectionsSeeder
 */
class Module8SectionsSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::where('slug', 'like', '%performance-management%')
            ->orWhere('title', 'like', '%Performance Management%')
            ->first();

        if (!$course) {
            $this->command->warn('Performance Management course not found. Skipping Module 8 sections seeder.');
            return;
        }

        $oldUnit = $course->units()
            ->where('title', 'like', '%Module 8%')
            ->where(function ($q) {
                $q->where('title', 'like', '%Compliance%')
                    ->orWhere('title', 'like', '%Risk%');
            })
            ->first();

        if (!$oldUnit) {
            $this->command->warn('Module 8: Compliance and Risk Monitoring unit not found. Skipping.');
            return;
        }

        $baseOrder = (int) $oldUnit->order;
        $courseId = $course->id;
        $sectionsDir = __DIR__ . '/module8_sections';
        $sectionsConfig = require $sectionsDir . '/sections.php';

        DB::transaction(function () use ($oldUnit, $baseOrder, $courseId, $sectionsDir, $sectionsConfig) {
            $oldUnit->forceDelete();

            $count = count($sectionsConfig);
            Unit::where('course_id', $courseId)->where('order', '>', $baseOrder)->increment('order', $count - 1);

            foreach ($sectionsConfig as $i => $config) {
                $path = $sectionsDir . '/' . $config['file'];
                $md = is_file($path) ? file_get_contents($path) : '';
                $md = str_replace('**Time Slot:**', 'Time Slot:', $md);
                $html = StepContentParser::toHtml($md);

                Unit::create([
                    'course_id' => $courseId,
                    'title' => $config['title'],
                    'slug' => Str::slug($config['title']) . '-' . uniqid(),
                    'content' => $html,
                    'order' => $baseOrder + $i,
                    'unit_type' => 'text',
                    'duration' => $config['duration'],
                ]);
            }
        });

        $this->command->info('Module 8 split into ' . count($sectionsConfig) . ' sections.');
    }
}
