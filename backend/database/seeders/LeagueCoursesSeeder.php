<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Database\Seeder;

class LeagueCoursesSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedLeagueCourse(
            code: 'YOUTH-101',
            title: 'Youth League Constitutional Leadership',
            description: 'Orientation for Youth League members who have completed membership. Covers youth wing structures, inter-generational leadership, and constitutional duties of youth cadres.',
            audience: 'youth',
            prefix: 'ZPF-YOUTH',
            certificateTitle: 'Youth League Certificate of Orientation',
            fee: 15.00,
            modules: [
                ['title' => 'Youth League Mandate', 'lessons' => [
                    ['title' => 'Aims and Constitutional Alignment', 'content' => 'Study Youth League aims under the ZANU PF Constitution and Article 2. Examine patriotism, youth development, and participation in party organs.'],
                    ['title' => 'Structures from Branch to National', 'content' => 'Map Youth League organs at branch, district, provincial and national levels and their relationship to the Central Committee.'],
                ]],
                ['title' => 'Leadership and Conduct', 'lessons' => [
                    ['title' => 'Cadre Discipline and Representation', 'content' => 'Analyse duties of youth members: honourable conduct, loyalty to constitution, and accountable representation of peers.'],
                ]],
            ],
            assessmentTitle: 'Youth League Assessment',
            questions: [
                ['body' => 'The Youth League is best described as:', 'options' => ['A judicial organ', 'The youth wing of the Party', 'A separate political party', 'An electoral commission'], 'correct' => 1],
                ['body' => 'Youth League membership for party members is primarily organised through:', 'options' => ['National Congress only', 'The branch structure', 'Foreign embassies', 'The judiciary'], 'correct' => 1],
                ['body' => 'True or False: Youth League aims should align with the Party Constitution.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Inter-generational leadership requires youth cadres to:', 'options' => ['Reject all senior guidance', 'Uphold constitutional values while participating in organs', 'Operate outside party structures', 'Avoid provincial coordination'], 'correct' => 1],
                ['body' => 'A youth member who breaches party discipline may face:', 'options' => ['No process', 'Prohibition order and hearing before appropriate organs', 'Immediate expulsion without notice', 'Automatic Presidium membership'], 'correct' => 1],
            ],
        );

        $this->seedLeagueCourse(
            code: 'WOMEN-101',
            title: "Women's League Governance & Inclusion",
            description: "Programme for Women's League members. Women's wing structures, gender inclusion principles, and provincial representation duties.",
            audience: 'women',
            prefix: 'ZPF-WOMEN',
            certificateTitle: "Women's League Certificate of Orientation",
            fee: 15.00,
            modules: [
                ['title' => "Women's League Mandate", 'lessons' => [
                    ['title' => 'Enrolment and Branch Pathway', 'content' => 'Every woman member aged 18+ may join through her branch. Study enrolment procedure and league autonomy within Central Committee authority.'],
                    ['title' => 'One-Third Representation', 'content' => 'Examine the constitutional principle of at least one-third women in principal organs at provincial level and below.'],
                ]],
                ['title' => 'Organisational Practice', 'lessons' => [
                    ['title' => 'Mobilisation and Accountability', 'content' => 'Analyse how the Women\'s League mobilises women, promotes equality, and advances party objectives through disciplined structures.'],
                ]],
            ],
            assessmentTitle: "Women's League Assessment",
            questions: [
                ['body' => "Women's League membership is open to women party members aged:", 'options' => ['Under 18 only', '18 and above through the branch', '65 and above only', 'Presidium appointees only'], 'correct' => 1],
                ['body' => 'The one-third representation principle applies at:', 'options' => ['Provincial level and below', 'Foreign missions only', 'Judiciary benches only', 'No organs'], 'correct' => 0],
                ['body' => "True or False: The Women's League has its own organs subject to Central Committee authority.", 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Gender inclusion in party organs strengthens:', 'options' => ['Only ceremonial roles', 'Organisational legitimacy and constitutional compliance', 'Exclusion of youth', 'Abandonment of Congress decisions'], 'correct' => 1],
                ['body' => 'A primary aim of the Women\'s League includes:', 'options' => ['Opposing party unity', 'Mobilising women and promoting equality', 'Replacing Congress', 'Managing elections independently'], 'correct' => 1],
            ],
        );

        $this->seedLeagueCourse(
            code: 'VETERANS-101',
            title: 'Veterans League Legacy & Service',
            description: 'Orientation for Veterans League members on liberation legacy, veterans mandates, and service to the party and nation.',
            audience: 'veterans',
            prefix: 'ZPF-VET',
            certificateTitle: 'Veterans League Certificate of Orientation',
            fee: 15.00,
            modules: [
                ['title' => 'Veterans League Role', 'lessons' => [
                    ['title' => 'Liberation Legacy', 'content' => 'Study recognition of the liberation struggle in national founding values and the veterans wing mandate to preserve historical memory and unity.'],
                    ['title' => 'Structures and Service', 'content' => 'Examine veterans league organs and how veterans cadres support branch and provincial work with discipline and experience.'],
                ]],
            ],
            assessmentTitle: 'Veterans League Assessment',
            questions: [
                ['body' => 'The liberation struggle is recognised in:', 'options' => ['Party slogans only', 'Zimbabwe\'s founding constitutional values', 'Foreign treaties alone', 'Local council by-laws only'], 'correct' => 1],
                ['body' => 'Veterans League members serve the party by:', 'options' => ['Operating outside constitutional structures', 'Supporting organs with experience and discipline', 'Replacing provincial executives automatically', 'Avoiding branch meetings'], 'correct' => 1],
                ['body' => 'True or False: Veterans cadres should uphold the same constitutional duties as all members.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Preserving liberation legacy includes:', 'options' => ['Denying national unity', 'Teaching constitutional values across generations', 'Rejecting Congress authority', 'Bypassing disciplinary procedures'], 'correct' => 1],
            ],
        );
    }

    /**
     * @param  list<array{title: string, lessons: list<array{title: string, content: string}>}>  $modules
     * @param  list<array{body: string, options: list<string>, correct: int}>  $questions
     */
    private function seedLeagueCourse(
        string $code,
        string $title,
        string $description,
        string $audience,
        string $prefix,
        string $certificateTitle,
        float $fee,
        array $modules,
        string $assessmentTitle,
        array $questions,
    ): void {
        $course = Course::updateOrCreate(
            ['code' => $code],
            [
                'title' => $title,
                'description' => $description,
                'level' => 'basic',
                'is_mandatory' => false,
                'grants_membership' => false,
                'requires_membership' => true,
                'audience' => $audience,
                'issues_certificate' => true,
                'certificate_number_prefix' => $prefix,
                'certificate_title' => $certificateTitle,
                'certificate_fee_amount' => $fee,
                'certificate_fee_currency' => config('academy.default_fee_currency', 'USD'),
                'status' => 'published',
                'created_by' => null,
            ]
        );

        $order = 0;
        foreach ($modules as $m) {
            $module = Module::updateOrCreate(
                ['course_id' => $course->id, 'title' => $m['title']],
                ['description' => null, 'order' => ++$order]
            );
            foreach ($m['lessons'] as $i => $l) {
                Lesson::updateOrCreate(
                    ['module_id' => $module->id, 'title' => $l['title']],
                    ['content' => $l['content'], 'order' => $i + 1]
                );
            }
        }

        $assessment = Assessment::updateOrCreate(
            ['course_id' => $course->id, 'title' => $assessmentTitle],
            [
                'description' => 'Pass mark 70%. Complete all lessons before attempting.',
                'duration_minutes' => 30,
                'pass_mark' => 70,
                'questions_per_attempt' => count($questions),
                'status' => 'published',
            ]
        );

        $assessment->questions()->delete();
        $firstModuleId = $course->modules()->orderBy('order')->value('id');
        foreach ($questions as $i => $q) {
            $question = Question::create([
                'assessment_id' => $assessment->id,
                'module_id' => $firstModuleId,
                'body' => $q['body'],
                'order' => $i + 1,
                'marks' => 1,
                'difficulty' => 'medium',
            ]);
            foreach ($q['options'] as $j => $optBody) {
                Option::create([
                    'question_id' => $question->id,
                    'body' => $optBody,
                    'is_correct' => $j === $q['correct'],
                ]);
            }
        }
    }
}
