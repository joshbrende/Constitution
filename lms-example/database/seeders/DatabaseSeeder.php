<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Section;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesSeeder::class);

        $admin = User::firstOrCreate(
            ['email' => 'admin@lms.test'],
            ['name' => 'Admin', 'password' => bcrypt('password')]
        );
        $admin->roles()->sync([\App\Models\Role::where('name', 'admin')->first()->id]);

        $instructor = User::firstOrCreate(
            ['email' => 'instructor@lms.test'],
            ['name' => 'Instructor', 'password' => bcrypt('password')]
        );
        $instructor->roles()->sync([\App\Models\Role::where('name', 'facilitator')->first()->id]);

        $student = User::firstOrCreate(
            ['email' => 'student@lms.test'],
            ['name' => 'Student', 'password' => bcrypt('password')]
        );
        $student->roles()->sync([\App\Models\Role::where('name', 'student')->first()->id]);

        $course = Course::create([
            'title' => 'Introduction to Laravel LMS',
            'slug' => 'intro-laravel-lms',
            'description' => "This sample course demonstrates the Laravel LMS built from the wplms-example structure. It includes sections, lessons, and a Knowledge Check.",
            'excerpt' => 'Learn the basics of this LMS.',
            'instructor_id' => $instructor->id,
            'published' => true,
            'students_count' => 0,
        ]);

        $s1 = Section::create(['course_id' => $course->id, 'title' => 'Getting Started', 'sort_order' => 1]);
        Unit::create([
            'section_id' => $s1->id,
            'title' => 'Welcome',
            'content' => '<p>Welcome to the course. This is a <strong>lesson</strong> with HTML content.</p><p>Complete it and click "Mark complete" to continue.</p>',
            'duration_minutes' => 5,
            'sort_order' => 1,
            'type' => 'lesson',
        ]);
        Unit::create([
            'section_id' => $s1->id,
            'title' => 'How to navigate',
            'content' => '<p>Use the <strong>curriculum</strong> on the left to jump between lessons and Knowledge Checks. Use Previous/Next to move in order.</p>',
            'duration_minutes' => 3,
            'sort_order' => 2,
            'type' => 'lesson',
        ]);

        $s2 = Section::create(['course_id' => $course->id, 'title' => 'Knowledge Check', 'sort_order' => 2]);
        $quiz = Unit::create([
            'section_id' => $s2->id,
            'title' => 'Quick Knowledge Check',
            'content' => null,
            'duration_minutes' => 5,
            'sort_order' => 1,
            'type' => 'quiz',
        ]);
        $quiz->quizQuestions()->create([
            'question_type' => 'multiple_choice',
            'content' => 'What is this LMS built with?',
            'options' => [
                ['text' => 'WordPress', 'value' => 'wp', 'correct' => false],
                ['text' => 'Laravel', 'value' => 'laravel', 'correct' => true],
                ['text' => 'Django', 'value' => 'django', 'correct' => false],
            ],
            'sort_order' => 1,
            'points' => 1,
        ]);
        $quiz->quizQuestions()->create([
            'question_type' => 'true_false',
            'content' => 'This LMS was inspired by WPLMS.',
            'correct_answer' => '1',
            'sort_order' => 2,
            'points' => 1,
        ]);
    }
}
