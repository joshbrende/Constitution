<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Creates 32 courses from single-course-1.html … single-course-32.html.
 * Run: php artisan db:seed --class=SingleCourseSeeder
 * These appear on the student "All Courses" view; locked until enrollment.
 */
class SingleCourseSeeder extends Seeder
{
    protected string $basePath;

    public function __construct()
    {
        $this->basePath = base_path('../');
        if (!str_ends_with($this->basePath, \DIRECTORY_SEPARATOR)) {
            $this->basePath .= \DIRECTORY_SEPARATOR;
        }
    }

    public function run(): void
    {
        $instructor = User::whereHas('roles', fn ($q) => $q->whereIn('roles.name', ['admin', 'facilitator', 'instructor']))
            ->first() ?? User::first();

        for ($i = 1; $i <= 32; $i++) {
            $path = $this->basePath . "single-course-{$i}.html";
            if (!is_file($path)) {
                $this->command?->warn("Skip single-course-{$i}.html: file not found.");
                continue;
            }

            $html = file_get_contents($path);
            $title = $this->extractTitle($html);
            $description = $this->extractDescription($html);

            if (!$title) {
                $title = "Course {$i}";
            }
            if (!$description) {
                $description = "3-Day Intensive Short Course | TTM Group.";
            }

            $slug = "single-course-{$i}";

            $course = Course::firstOrCreate(
                ['slug' => $slug],
                [
                    'title' => $title,
                    'short_description' => $description,
                    'description' => $description,
                    'status' => 'published',
                    'instructor_id' => $instructor?->id ?? 1,
                    'enrollment_count' => 0,
                ]
            );

            if (!$course->wasRecentlyCreated) {
                $course->update([
                    'title' => $title,
                    'short_description' => $description,
                    'description' => $description,
                    'status' => 'published',
                    'instructor_id' => $instructor?->id ?? 1,
                ]);
            }
        }

        $this->command?->info('32 single-course entries created or updated.');
    }

    private function extractTitle(string $html): ?string
    {
        if (preg_match('/<title>\s*(.+?)\s*\|/', $html, $m)) {
            return trim(html_entity_decode($m[1], \ENT_QUOTES | \ENT_HTML5, 'UTF-8'));
        }
        if (preg_match('/<title>\s*(.+?)\s*<\/title>/s', $html, $m)) {
            return trim(html_entity_decode($m[1], \ENT_QUOTES | \ENT_HTML5, 'UTF-8'));
        }
        return null;
    }

    private function extractDescription(string $html): ?string
    {
        if (preg_match('/<meta\s+name=["\']description["\']\s+content=["\'](.+?)["\']/s', $html, $m)) {
            return trim(html_entity_decode($m[1], \ENT_QUOTES | \ENT_HTML5, 'UTF-8'));
        }
        return null;
    }
}
