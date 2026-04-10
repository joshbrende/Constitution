<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Day2Day3Seeder extends Seeder
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

        $units = $course->units()->orderByRaw('`order` asc')->get();

        $day2Content = '<h2>Day 2: Opening &amp; Recap</h2><p><strong>Time Slot:</strong> 08:30 – 09:00</p>'
            . '<p>Review of Day 1, address questions, set Day 2 objectives.</p>'
            . '<p><strong>Today you will:</strong></p><ul>'
            . '<li>Recap SALGA 2026 context, AI fundamentals, performance framework design, and data foundations</li>'
            . '<li>Address any questions from Day 1</li>'
            . '<li>Set objectives for Day 2: AI-powered KPI dashboards, financial and service-delivery monitoring, compliance and risk</li></ul>'
            . '<p><strong>SALGA Outcome 1:</strong> Agile Force – Building on foundation.</p>';
        $day3Content = '<h2>Day 3: Opening &amp; Recap</h2><p><strong>Time Slot:</strong> 08:30 – 09:00</p>'
            . '<p>Review of Day 2, address questions, set Day 3 objectives.</p>'
            . '<p><strong>Today you will:</strong></p><ul>'
            . '<li>Recap KPI dashboards, financial and service-delivery monitoring, compliance and risk</li>'
            . '<li>Address any questions from Day 2</li>'
            . '<li>Set objectives for Day 3: predictive analytics, citizen engagement, AI implementation roadmap, and action planning</li></ul>'
            . '<p><strong>SALGA Outcome 1:</strong> Agile Force – Building momentum.</p>';

        $day2Unit = $units->first(fn ($u) => stripos($u->title, 'Day 2: Opening') !== false);
        $day3Unit = $units->first(fn ($u) => stripos($u->title, 'Day 3: Opening') !== false);
        if ($day2Unit && $day3Unit) {
            $day2Unit->update(['content' => $day2Content, 'description' => 'Review of Day 1, address questions, set Day 2 objectives.']);
            $day3Unit->update(['content' => $day3Content, 'description' => 'Review of Day 2, address questions, set Day 3 objectives.']);
            $this->command->info('Day 2 and Day 3 units updated.');
            return;
        }

        $module4 = $units->first(fn ($u) => preg_match('/Module\s*4\b/i', $u->title));
        $module8 = $units->first(fn ($u) => preg_match('/Module\s*8\b/i', $u->title));

        if (!$module4 || !$module8) {
            $this->command->warn('Module 4 or Module 8 not found. Skipping.');
            return;
        }

        DB::transaction(function () use ($course, $units, $module4, $module8, $day2Content, $day3Content) {
            $idx4 = $units->search(fn ($u) => (int) $u->id === (int) $module4->id);
            $idx8 = $units->search(fn ($u) => (int) $u->id === (int) $module8->id);
            if ($idx4 === false || $idx8 === false) {
                return;
            }

            $day2 = Unit::create([
                'course_id' => $course->id,
                'title' => 'Day 2: Opening & Recap',
                'slug' => 'day-2-opening-recap-' . uniqid(),
                'content' => $day2Content,
                'description' => 'Review of Day 1, address questions, set Day 2 objectives.',
                'order' => 0,
                'unit_type' => 'text',
                'duration' => 30,
            ]);

            $day3 = Unit::create([
                'course_id' => $course->id,
                'title' => 'Day 3: Opening & Recap',
                'slug' => 'day-3-opening-recap-' . uniqid(),
                'content' => $day3Content,
                'description' => 'Review of Day 2, address questions, set Day 3 objectives.',
                'order' => 0,
                'unit_type' => 'text',
                'duration' => 30,
            ]);

            $ordered = $units->values()->all();
            $newList = [];
            $pos = 0;
            foreach ($ordered as $i => $u) {
                $pos++;
                $newList[] = ['unit' => $u, 'order' => $pos];
                if ($i === $idx4) {
                    $pos++;
                    $newList[] = ['unit' => $day2, 'order' => $pos];
                }
                if ($i === $idx8) {
                    $pos++;
                    $newList[] = ['unit' => $day3, 'order' => $pos];
                }
            }

            foreach ($newList as $entry) {
                $entry['unit']->update(['order' => $entry['order']]);
            }
        });

        $this->command->info('Day 2 and Day 3 units added.');
    }
}
