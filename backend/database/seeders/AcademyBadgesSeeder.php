<?php

namespace Database\Seeders;

use App\Models\AcademyBadge;
use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AcademyBadgesSeeder extends Seeder
{
    public function run(): void
    {
        // Only set up badges for the current active membership course
        $course = Course::where('grants_membership', true)->where('status', 'published')->first();
        if (! $course) {
            return;
        }

        $badges = [
            // Engagement / learning
            [
                'slug' => 'enrolled_course_1',
                'title' => 'Enrolled',
                'description' => 'Enroll in the course.',
                'icon' => '🎓',
                'rule_type' => 'enrolled_n',
                'target_value' => 1,
            ],
            [
                'slug' => 'completed_course_1',
                'title' => 'Course Completed',
                'description' => 'Complete the course.',
                'icon' => '🏁',
                'rule_type' => 'completed_n',
                'target_value' => 1,
            ],
            [
                'slug' => 'assessment_started_1',
                'title' => 'First Attempt',
                'description' => 'Start the membership assessment at least once.',
                'icon' => '🧠',
                'rule_type' => 'assessment_started_n',
                'target_value' => 1,
            ],
            [
                'slug' => 'assessment_submitted_1',
                'title' => 'Submitted',
                'description' => 'Submit the membership assessment at least once.',
                'icon' => '✍️',
                'rule_type' => 'assessment_submitted_n',
                'target_value' => 1,
            ],

            // Mastery (assessment scores)
            [
                'slug' => 'pass_score_70_plus',
                'title' => 'Certified (70+)',
                'description' => 'Pass the assessment with 70+ score.',
                'icon' => '✅',
                'rule_type' => 'pass_score_at_least',
                'target_value' => 70,
            ],
            [
                'slug' => 'pass_score_80_plus',
                'title' => 'Cadre Grade (80+)',
                'description' => 'Pass the assessment with 80+ score.',
                'icon' => '🏅',
                'rule_type' => 'pass_score_at_least',
                'target_value' => 80,
            ],
            [
                'slug' => 'pass_score_90_plus',
                'title' => 'Distinction (90+)',
                'description' => 'Pass the assessment with 90+ score.',
                'icon' => '🏆',
                'rule_type' => 'pass_score_at_least',
                'target_value' => 90,
            ],

            // Membership / achievements
            [
                'slug' => 'membership_granted',
                'title' => 'Member Granted',
                'description' => 'Your membership role is granted after passing.',
                'icon' => '🛡️',
                'rule_type' => 'membership_granted',
                'target_value' => 1,
            ],
            [
                'slug' => 'certificate_issued',
                'title' => 'Certificate Issued',
                'description' => 'A certificate record exists for this course.',
                'icon' => '📜',
                'rule_type' => 'certificate_issued',
                'target_value' => 1,
            ],
            [
                'slug' => 'perfect_attempt',
                'title' => 'Top Performer',
                'description' => 'Reach a 100 score on any attempt.',
                'icon' => '🌟',
                'rule_type' => 'perfect_attempt',
                'target_value' => 100,
            ],
        ];

        foreach ($badges as $b) {
            AcademyBadge::updateOrCreate(
                ['course_id' => $course->id, 'slug' => $b['slug']],
                [
                    'title' => $b['title'],
                    'description' => $b['description'] ?? null,
                    'icon' => $b['icon'] ?? null,
                    'rule_type' => $b['rule_type'],
                    'target_value' => (int) $b['target_value'],
                ]
            );
        }
    }
}

