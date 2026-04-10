<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Unit;
use App\Services\StepContentParser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Replaces the "Module 9: Predictive Analytics for Municipal Planning" unit with 15 sections:
 * theory (predictive modeling fundamentals, demand forecasting, resource allocation
 * optimization, scenario planning, long-term trend analysis), practical (demand
 * forecast worksheet, scenario planning template), AI application (population
 * growth prediction, service demand forecasting, infrastructure lifecycle and
 * budget forecasting, climate change impact modeling), key takeaways, reflection,
 * and next steps.
 *
 * Run: php artisan db:seed --class=Module9SectionsSeeder
 */
class Module9SectionsSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::where('slug', 'like', '%performance-management%')
            ->orWhere('title', 'like', '%Performance Management%')
            ->first();

        if (!$course) {
            $this->command->warn('Performance Management course not found. Skipping Module 9 sections seeder.');
            return;
        }

        $oldUnit = $course->units()
            ->where('title', 'like', '%Module 9%')
            ->where(function ($q) {
                $q->where('title', 'like', '%Predictive%')
                    ->orWhere('title', 'like', '%Municipal Planning%');
            })
            ->first();

        if (!$oldUnit) {
            $this->command->warn('Module 9: Predictive Analytics for Municipal Planning unit not found. Skipping.');
            return;
        }

        $baseOrder = (int) $oldUnit->order;
        $courseId = $course->id;
        $sectionsDir = __DIR__ . '/module9_sections';
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

        $this->command->info('Module 9 split into ' . count($sectionsConfig) . ' sections.');
    }
}
