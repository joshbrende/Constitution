<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Unit;
use App\Services\StepContentParser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Module1SectionsSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::where('slug', 'like', '%performance-management%')
            ->orWhere('title', 'like', '%Performance Management%')
            ->first();

        if (!$course) {
            $this->command->warn('Course "Performance Management and Monitoring Using AI in the Public Sector" not found. Skipping Module 1 sections seeder.');
            return;
        }

        $oldUnit = $course->units()
            ->where('title', 'like', '%Module 1%Understanding SALGA%')
            ->first();

        if (!$oldUnit) {
            $this->command->warn('Module 1 unit (Understanding SALGA 2026 Context) not found. Skipping.');
            return;
        }

        $baseOrder = (int) $oldUnit->order;
        $courseId = $course->id;

        // Prefer performance-C1/docs (template) when present; else database/seeders/module1_sections
        $docsPath = base_path('public/performance-C1/docs');
        if (is_file($docsPath . '/module-1-sections.php')) {
            $sectionsConfig = require $docsPath . '/module-1-sections.php';
            $sectionsDir = $docsPath;
        } else {
            $sectionsDir = __DIR__ . '/module1_sections';
            $sectionsConfig = require $sectionsDir . '/sections.php';
        }

        DB::transaction(function () use ($oldUnit, $baseOrder, $courseId, $sectionsDir, $sectionsConfig) {
            $oldUnit->forceDelete();

            $count = count($sectionsConfig);
            Unit::where('course_id', $courseId)->where('order', '>', $baseOrder)->increment('order', $count - 1);

            foreach ($sectionsConfig as $i => $config) {
                $path = $sectionsDir . '/' . $config['file'];
                $md = is_file($path) ? file_get_contents($path) : '';
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

        $this->command->info('Module 1 split into ' . count($sectionsConfig) . ' sections.');
    }
}
