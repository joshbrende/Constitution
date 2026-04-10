<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class GamificationSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            ['name' => 'First steps', 'slug' => 'first-steps', 'description' => 'Enrolled in your first course', 'icon' => 'bi-star', 'points_required' => 0],
            ['name' => 'Knowledge Check master', 'slug' => 'quiz-master', 'description' => 'Passed a module Knowledge Check', 'icon' => 'bi-trophy', 'points_required' => 0],
            ['name' => 'Course complete', 'slug' => 'course-complete', 'description' => 'Completed a course', 'icon' => 'bi-award', 'points_required' => 0],
        ];
        foreach ($badges as $b) {
            Badge::firstOrCreate(['slug' => $b['slug']], $b);
        }
    }
}
