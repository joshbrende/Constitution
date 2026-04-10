<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Unit;
use App\Services\StepContentParser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Replaces the "Module 10: Citizen Engagement and Feedback Systems" unit with 15 sections:
 * theory (digital citizen engagement platforms, feedback collection and analysis,
 * complaint management systems, public participation in IDP processes, transparency
 * and accountability), practical (engagement and feedback tracker, IDP participation
 * and transparency checklist), AI application (chatbots, sentiment analysis of
 * social media and feedback, complaint categorisation/routing/NLP, predictive
 * citizen satisfaction), key takeaways, reflection, and next steps.
 *
 * Run: php artisan db:seed --class=Module10SectionsSeeder
 */
class Module10SectionsSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::where('slug', 'like', '%performance-management%')
            ->orWhere('title', 'like', '%Performance Management%')
            ->first();

        if (!$course) {
            $this->command->warn('Performance Management course not found. Skipping Module 10 sections seeder.');
            return;
        }

        $oldUnit = $course->units()
            ->where('title', 'like', '%Module 10%')
            ->where(function ($q) {
                $q->where('title', 'like', '%Citizen%')
                    ->orWhere('title', 'like', '%Engagement%')
                    ->orWhere('title', 'like', '%Feedback%');
            })
            ->first();

        if (!$oldUnit) {
            $this->command->warn('Module 10: Citizen Engagement and Feedback Systems unit not found. Skipping.');
            return;
        }

        $baseOrder = (int) $oldUnit->order;
        $courseId = $course->id;
        $sectionsDir = __DIR__ . '/module10_sections';
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

        $this->command->info('Module 10 split into ' . count($sectionsConfig) . ' sections.');
    }
}
