<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RefreshModule1IntroductionSeeder extends Seeder
{
    /**
     * Updates the "Module 1: Introduction" unit content from 01_introduction.md.
     * Run: php artisan db:seed --class=RefreshModule1IntroductionSeeder
     */
    public function run(): void
    {
        $course = Course::where('slug', 'like', '%performance-management%')
            ->orWhere('title', 'like', '%Performance Management%')
            ->first();

        if (!$course) {
            $this->command->warn('Performance Management course not found. Skipping.');
            return;
        }

        $unit = $course->units()->where('title', 'Module 1: Introduction')->first();

        if (!$unit) {
            $this->command->warn('Unit "Module 1: Introduction" not found. Skipping.');
            return;
        }

        $path = base_path('database/seeders/module1_sections/01_introduction.md');
        if (!is_file($path)) {
            $this->command->warn('01_introduction.md not found. Skipping.');
            return;
        }

        $md = file_get_contents($path);
        $md = str_replace('**Time Slot:**', 'Time Slot:', $md);
        $html = Str::markdown($md);

        $unit->update(['content' => $html, 'duration' => 6]);
        $this->command->info('Updated "Module 1: Introduction" content and duration from 01_introduction.md.');
    }
}
