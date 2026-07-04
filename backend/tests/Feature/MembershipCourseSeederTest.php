<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\Course;
use App\Models\Question;
use Database\Seeders\MembershipCourseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembershipCourseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeds_membership_course_with_balanced_question_bank(): void
    {
        $this->app->make(MembershipCourseSeeder::class)->run();

        $course = Course::query()->where('code', 'MEMBERSHIP')->first();
        $this->assertNotNull($course);
        $this->assertTrue($course->grants_membership);
        $this->assertSame('intermediate', $course->level);

        $assessment = Assessment::query()->where('course_id', $course->id)->first();
        $this->assertNotNull($assessment);
        $this->assertSame(25, $assessment->questions_per_attempt);
        $this->assertSame(60, $assessment->duration_minutes);

        $questions = Question::query()->where('assessment_id', $assessment->id)->get();
        $this->assertCount(120, $questions);

        $difficultyCounts = $questions->countBy('difficulty');
        $this->assertGreaterThan(50, $difficultyCounts->get('easy', 0));
        $this->assertGreaterThan(35, $difficultyCounts->get('medium', 0));
        $this->assertGreaterThan(18, $difficultyCounts->get('hard', 0));

        $moduleCounts = $questions->groupBy('module_id')->map->count();
        $this->assertCount(10, $moduleCounts);
        $this->assertSame(12, $moduleCounts->min());
        $this->assertSame(12, $moduleCounts->max());
    }
}
