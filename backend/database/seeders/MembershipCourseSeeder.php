<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Database\Seeder;

class MembershipCourseSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::updateOrCreate(
            ['code' => 'MEMBERSHIP'],
            [
                'title' => 'Foundational Constitutional Studies Certificate',
                'description' => 'A tertiary-standard programme in party and national constitutional literacy. You will analyse the ZANU PF Constitution alongside the Constitution of Zimbabwe (2013), examining institutions, rights, elections, and governance. Passing the final assessment demonstrates competence for membership candidacy.',
                'level' => 'intermediate',
                'is_mandatory' => true,
                'grants_membership' => true,
                'requires_membership' => false,
                'audience' => 'all',
                'issues_certificate' => true,
                'certificate_number_prefix' => config('certificates.certificate_number_prefix', 'ZPF-MEM'),
                'certificate_title' => 'Certificate of Competence',
                'certificate_fee_amount' => config('academy.default_membership_fee_amount', 25.00),
                'certificate_fee_currency' => config('academy.default_fee_currency', 'USD'),
                'status' => 'published',
                'created_by' => null,
            ]
        );

        $modules = [
            ['title' => 'ZANU PF Constitution – Foundation & Objectives', 'lessons' => [
                ['title' => 'Preamble, Unity Accord and Party Identity', 'content' => 'Study the Party as a body corporate with perpetual succession. Analyse how the preamble locates ZANU PF in the liberation struggle, the Patriotic Front Alliance, and the Unity Accord of 22 December 1987. Consider how constitutional identity shapes cadre discipline and national unity.'],
                ['title' => 'Aims, Objectives and Constitutional Values', 'content' => 'Examine aims including sovereignty, democratic order, rule of law, and opposition to tribalism, corruption and discrimination. Reflect on how these objectives align with mass-party organisation and accountable leadership.'],
            ]],
            ['title' => 'ZANU PF Constitution – Membership', 'lessons' => [
                ['title' => 'Qualifications, Application and Appeals', 'content' => 'Analyse open membership for citizens and residents, branch-based applications, exceptional Politburo routes, and Central Committee appeals. Understand procedural fairness as a foundation of internal democracy.'],
                ['title' => 'Rights, Duties and Discipline', 'content' => 'Study voting rights, subscriptions, honourable conduct, prohibition orders, representation in hearings, and National Disciplinary Committee powers. Link party due process to broader constitutional culture.'],
            ]],
            ['title' => 'ZANU PF Constitution – Structure & Organs', 'lessons' => [
                ['title' => 'Congress, Central Committee and Conference', 'content' => 'Congress as supreme organ (five-year cycle), election of leadership, quorum rules, and the Central Committee as the highest organ between Congresses. Distinguish policy-making from day-to-day execution.'],
                ['title' => 'Politburo and Provincial Structures', 'content' => 'Analyse the Politburo as the executive committee of the Central Committee, meeting frequency, and Provincial Executive Councils as implementation structures. Map how decisions flow from Congress to branches.'],
            ]],
            ['title' => 'ZANU PF Constitution – Leagues & Wings', 'lessons' => [
                ['title' => 'Women\'s League and Gender Inclusion', 'content' => 'Study the Women\'s League mandate, branch enrolment for women members 18+, and the one-third representation principle in organs at provincial level and below. Evaluate inclusion as organisational strength.'],
                ['title' => 'Youth League and Inter-generational Leadership', 'content' => 'Examine Youth League aims, constitutional alignment with Article 2, and structures for youth participation. Consider how wings sustain constitutional values across generations.'],
            ]],
            ['title' => 'Constitution of Zimbabwe – Founding Values', 'lessons' => [
                ['title' => 'Supremacy, Republicanism and the Preamble', 'content' => 'Analyse constitutional supremacy, invalidity of inconsistent law, and Zimbabwe as a unitary democratic sovereign republic. Interpret the Preamble\'s commitment to freedom, justice, equality and good governance.'],
                ['title' => 'Founding Values and National Identity', 'content' => 'Study rule of law, human rights, gender equality, liberation struggle recognition, cultural diversity, and accountability. Relate founding values to everyday citizenship and public leadership.'],
            ]],
            ['title' => 'Constitution of Zimbabwe – Rights & Citizenship', 'lessons' => [
                ['title' => 'Citizenship and Civic Duties', 'content' => 'Examine citizenship by birth, descent and registration; equal citizenship; duties of loyalty, constitutional observance and defence of sovereignty. Balance rights with responsibilities.'],
                ['title' => 'Declaration of Rights (Chapter 4)', 'content' => 'Study binding effect on the State, duties to respect, protect, promote and fulfil rights; core freedoms including life, expression, equality, vote and political association. Understand rights as tools of empowerment and accountability.'],
            ]],
            ['title' => 'Constitution of Zimbabwe – Executive & Legislature', 'lessons' => [
                ['title' => 'President, Cabinet and Executive Authority', 'content' => 'Analyse executive authority derived from the people, presidential roles, Cabinet accountability, and constitutional obedience by the Head of State.'],
                ['title' => 'Parliament and Legislative Power', 'content' => 'Study bicameral Parliament, legislative authority, democratic governance duties, and law-making within constitutional limits.'],
                ['title' => 'Multi-party Democracy and Political Pluralism', 'content' => 'Examine constitutional multi-partyism, political party funding legislation, and respect for all parties. Distinguish party membership from State office.'],
            ]],
            ['title' => 'Constitution of Zimbabwe – Elections', 'lessons' => [
                ['title' => 'Electoral Principles and ZEC', 'content' => 'Analyse free, fair and regular elections, secret ballot, universal suffrage, media access, and the Zimbabwe Electoral Commission\'s oversight role. Evaluate how elections translate popular will into legitimate authority.'],
            ]],
            ['title' => 'Constitution of Zimbabwe – Judiciary & Rule of Law', 'lessons' => [
                ['title' => 'Judicial Independence and Constitutional Court', 'content' => 'Study judicial independence, fair trial rights, enforcement of Chapter 4, and the Constitutional Court\'s role. Understand courts as guardians of rights and limits on power.'],
            ]],
            ['title' => 'Constitution of Zimbabwe – Provincial & Local Government', 'lessons' => [
                ['title' => 'Devolution, Local Authorities and Leadership Ethics', 'content' => 'Analyse provincial councils, local government initiative, devolution of functions, equitable resource sharing, and leadership principles of loyalty, honesty and accountability in public office.'],
            ]],
        ];

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
            [
                'course_id' => $course->id,
                'title' => 'Membership Assessment',
            ],
            [
                'description' => 'Final competence assessment. Each attempt presents 25 questions drawn from a 120-item bank, balanced across all modules and difficulty levels. Score at least 70% to pass.',
                'duration_minutes' => 60,
                'pass_mark' => 70,
                'questions_per_attempt' => 25,
                'status' => 'published',
            ]
        );

        $this->seedQuestions($assessment, $course);
    }

    private function seedQuestions(Assessment $assessment, Course $course): void
    {
        $assessment->questions()->delete();
        $modules = $course->modules()->orderBy('order')->get();
        $questionsByModule = require __DIR__.'/data/membership_assessment_questions.php';

        $order = 0;
        foreach ($modules as $moduleIndex => $module) {
            $questions = $questionsByModule[$moduleIndex] ?? [];
            foreach ($questions as $q) {
                $order++;
                $difficulty = $q['difficulty'] ?? 'medium';
                if (! in_array($difficulty, ['easy', 'medium', 'hard'], true)) {
                    $difficulty = 'medium';
                }

                $question = Question::create([
                    'assessment_id' => $assessment->id,
                    'module_id' => $module->id,
                    'body' => $q['body'],
                    'order' => $order,
                    'marks' => 1,
                    'difficulty' => $difficulty,
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
}
