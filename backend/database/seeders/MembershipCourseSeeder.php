<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
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
                'description' => 'This course covers the ZANU PF Constitution and the Constitution of the Republic of Zimbabwe (2013). Successful completion and passing the final assessment grants membership in ZANU PF.',
                'level' => 'basic',
                'is_mandatory' => true,
                'grants_membership' => true,
                'certificate_title' => 'Certificate of Competence',
                'status' => 'published',
                'created_by' => null,
            ]
        );

        $modules = [
            ['title' => 'ZANU PF Constitution – Foundation & Objectives', 'lessons' => [
                ['title' => 'Preamble and Unity Accord', 'content' => 'The ZANU PF Constitution preamble acknowledges the liberation struggle, the Unity Accord of 22nd December 1987, and the Patriotic Front Alliance. The Party was united and reconstituted under the name ZANU PF.'],
                ['title' => 'The Party: Name, Aims and Objectives', 'content' => 'The Party is the Zimbabwe African National Union Patriotic Front (ZANU PF). It is a body corporate with perpetual succession. The Party\'s aims include preserving national sovereignty, creating democratic order, upholding the rule of law, and opposing tribalism, corruption and discrimination.'],
            ]],
            ['title' => 'ZANU PF Constitution – Membership', 'lessons' => [
                ['title' => 'Qualifications and Application', 'content' => 'Membership is open to citizens and residents who subscribe to the Constitution. Applications are made to the local branch; exceptional cases go through the Secretary for Administration. Appeals lie to the Central Committee.'],
                ['title' => 'Rights and Duties of Members', 'content' => 'Members have the right to vote, be elected, have audience with officers, and seek remedies for grievances. Duties include loyalty, paying subscriptions, and conducting oneself honourably.'],
                ['title' => 'Discipline', 'content' => 'Only the National Disciplinary Committee may expel a member. Members receive a prohibition order and notice of charges before any hearing. They may be represented by a member of their choice.'],
            ]],
            ['title' => 'ZANU PF Constitution – Structure & Organs', 'lessons' => [
                ['title' => 'Congress and Central Committee', 'content' => 'Congress is the supreme organ, convening every five years. It elects the President and Central Committee. The Central Committee is the highest organ between Congress, meeting every three months.'],
                ['title' => 'Politburo and Provincial Structures', 'content' => 'The Politburo is the executive committee of the Central Committee, meeting monthly. Provincial Executive Councils implement party decisions at provincial level.'],
            ]],
            ['title' => 'ZANU PF Constitution – Leagues & Wings', 'lessons' => [
                ['title' => 'Women\'s League', 'content' => 'The Women\'s League is the Women\'s Wing of ZANU PF. Its aims include mobilising women, promoting rights, education and equality. Every woman member aged 18+ is entitled to join through her Branch.'],
                ['title' => 'Youth League', 'content' => 'The Youth League is the Youth Wing. Its aims align with Article 2 of the Constitution. The Youth League promotes youth participation and development.'],
            ]],
            ['title' => 'Constitution of Zimbabwe – Founding Values', 'lessons' => [
                ['title' => 'Supremacy and the Republic', 'content' => 'The Constitution is the supreme law. Zimbabwe is a unitary, democratic and sovereign republic. Any law inconsistent with the Constitution is invalid.'],
                ['title' => 'Founding Values and Principles', 'content' => 'Values include supremacy of the Constitution, rule of law, fundamental rights, gender equality, good governance, and recognition of the liberation struggle.'],
            ]],
            ['title' => 'Constitution of Zimbabwe – Rights & Citizenship', 'lessons' => [
                ['title' => 'Citizenship and Duties', 'content' => 'Citizenship is by birth, descent or registration. Citizens have the duty to be loyal to Zimbabwe, observe the Constitution, and defend the nation.'],
                ['title' => 'Declaration of Rights', 'content' => 'Chapter 4 binds the State and all institutions. The State must respect, protect, promote and fulfil rights. Every person has the right to life, freedom of expression, and equality before the law.'],
            ]],
            ['title' => 'Constitution of Zimbabwe – Executive & Legislature', 'lessons' => [
                ['title' => 'President and Cabinet', 'content' => 'The President is Head of State and Government and Commander-in-Chief. Executive authority derives from the people. The President exercises it through the Cabinet.'],
                ['title' => 'Parliament', 'content' => 'Parliament consists of the Senate and National Assembly. Legislative authority is derived from the people. Parliament protects the Constitution and promotes democratic governance.'],
                ['title' => 'Multi-party Democracy', 'content' => 'The Constitution establishes a multi-party democratic system. It provides for the funding of political parties and respect for the rights of all political parties.'],
            ]],
            ['title' => 'Constitution of Zimbabwe – Elections', 'lessons' => [
                ['title' => 'Electoral Systems', 'content' => 'Elections must be free, fair and regular. The Zimbabwe Electoral Commission oversees elections. All political parties must have reasonable access to materials and media.'],
            ]],
            ['title' => 'Constitution of Zimbabwe – Judiciary & Rule of Law', 'lessons' => [
                ['title' => 'Courts and Judicial Independence', 'content' => 'The Judiciary is independent. Courts safeguard human rights and the rule of law. The Constitutional Court determines constitutional matters.'],
            ]],
            ['title' => 'Constitution of Zimbabwe – Provincial & Local Government', 'lessons' => [
                ['title' => 'Devolution and Local Authorities', 'content' => 'Zimbabwe has provincial councils and local authorities. Government functions may be devolved. Leadership principles include loyalty, honesty and accountability.'],
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
                'description' => 'Final assessment for membership. You must score at least 70% to pass.',
                'duration_minutes' => 45,
                'pass_mark' => 70,
                'questions_per_attempt' => 25,
                'status' => 'published',
            ]
        );

        $this->seedQuestions($assessment, $course);
    }

    /**
     * Seed multiple-choice questions. Each module gets 10+ questions; questions are
     * keyed by module index (0–9) to ensure correct topic coverage.
     */
    private function seedQuestions(Assessment $assessment, Course $course): void
    {
        $assessment->questions()->delete();
        $modules = $course->modules()->orderBy('order')->get();
        $questionsByModule = $this->getQuestionsByModule();

        $order = 0;
        foreach ($modules as $moduleIndex => $module) {
            $questions = $questionsByModule[$moduleIndex] ?? [];
            foreach ($questions as $q) {
                $order++;
                $question = Question::create([
                    'assessment_id' => $assessment->id,
                    'module_id' => $module->id,
                    'body' => $q['body'],
                    'order' => $order,
                    'marks' => 1,
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

    /**
     * Returns questions keyed by module index (0–9). Module order matches the course:
     * 0=ZANU PF Foundation, 1=Membership, 2=Structure, 3=Leagues, 4=Zim Values,
     * 5=Rights & Citizenship, 6=Executive & Legislature, 7=Elections, 8=Judiciary, 9=Provincial & Local.
     */
    private function getQuestionsByModule(): array
    {
        return [
            // Module 0: ZANU PF – Foundation & Objectives
            [
                ['body' => 'The full name of the Party is the Zimbabwe African National Union Patriotic Front, hereinafter referred to as "ZANU PF" or "the Party".', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'How is the Party described in the Constitution?', 'options' => ['An unincorporated association', 'A body corporate with perpetual succession', 'A trust', 'A partnership'], 'correct' => 1],
                ['body' => 'The Unity Accord that united ZANU PF and PF-ZAPU was concluded on 22nd December 1987.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'What was the Patriotic Front Alliance?', 'options' => ['A colonial institution', 'The effective instrument for prosecuting the armed struggle and winning democracy and national independence', 'A regional trade bloc', 'A religious organisation'], 'correct' => 1],
                ['body' => 'Where are the Party headquarters located?', 'options' => ['Bulawayo', 'Mutare', 'Harare', 'Gweru'], 'correct' => 2],
                ['body' => 'The Party flag comprises the colours green, yellow, red and black.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'According to the Constitution, black on the Party flag represents:', 'options' => ['The vegetation and agriculture of Zimbabwe', 'The mineral wealth of Zimbabwe', 'The indigenous people as sovereign owners and custodians of Zimbabwe', 'The blood of the liberation struggle'], 'correct' => 2],
                ['body' => 'The Party\'s vision is to forever remain the mass revolutionary socialist Party in the emancipation process of the people of Zimbabwe from all forms of oppression.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Among the aims and objectives of the Party is:', 'options' => ['To preserve and defend the National Sovereignty and Independence of Zimbabwe', 'To abolish private property', 'To establish a monarchy', 'To join a foreign federation'], 'correct' => 0],
                ['body' => 'The Party aims to create conditions for a democratic political and social order with periodic free and fair elections based on universal adult suffrage.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The Party opposes resolutely:', 'options' => ['National sovereignty', 'The rule of law', 'Tribalism, regionalism, nepotism, corruption and discrimination', 'Universal adult suffrage'], 'correct' => 2],
                ['body' => 'The Party supports the worldwide struggle for the complete eradication of imperialism, colonialism and all forms of racism.', 'options' => ['True', 'False'], 'correct' => 0],
            ],
            // Module 1: ZANU PF – Membership
            [
                ['body' => 'Membership of the Party shall be open to all citizens and residents of Zimbabwe who subscribe to the Constitution, aims, objectives and policies of the Party.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'To become a member, a person shall ordinarily make application to:', 'options' => ['The President and First Secretary', 'The National Disciplinary Committee', 'The local branch nearest to where they reside or work', 'The Central Committee'], 'correct' => 2],
                ['body' => 'In exceptional circumstances, a person may apply for membership through the Secretary for Administration to the Politburo.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Any person whose membership application has been rejected may appeal to:', 'options' => ['The Branch Disciplinary Committee', 'The National Consultative Assembly', 'The Central Committee, whose decision shall be final', 'The Provincial Executive Council'], 'correct' => 2],
                ['body' => 'Every member of the Party has the right to vote in Party elections in accordance with rules and regulations of the Central Committee.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Every member has the duty to pay regular subscriptions.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Every member has the duty to conduct himself or herself honestly and honourably and not to bring the Party into disrepute or ridicule.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The only organ of the Party that has the power to expel a member is:', 'options' => ['The Branch Disciplinary Committee', 'The District Disciplinary Committee', 'The Provincial Disciplinary Committee', 'The National Disciplinary Committee'], 'correct' => 3],
                ['body' => 'Before disciplinary action, a member must receive a prohibition order and notice of charges in writing.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'A member has the right to be assisted or represented in disciplinary proceedings by any member of his or her own choice.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Disciplinary punishments prescribed by the Constitution include oral reprimand, written reprimand, fine, suspension or removal from office, and expulsion.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Every member has the right not to be subjected to arbitrary or vexatious treatment by those in authority over him or her.', 'options' => ['True', 'False'], 'correct' => 0],
            ],
            // Module 2: ZANU PF – Structure & Organs
            [
                ['body' => 'The National People\'s Congress is the supreme organ of the Party.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Congress shall convene in ordinary session once every five years.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Congress shall elect the President and First Secretary and members of the Central Committee.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Half of the total membership of Congress shall form a quorum for ordinary sessions.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The Central Committee is the highest organ of the Party in-between Congress.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The Central Committee shall meet once every three months in ordinary session.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The Politburo is the executive committee of the Central Committee.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The Politburo shall meet at least once a month in ordinary session.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Two-thirds of the total membership of the Politburo shall constitute a quorum.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The National People\'s Conference shall declare the President of the Party elected at Congress as the State Presidential Candidate.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The Provincial Executive Council shall meet at least once every month.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The principal organs of the Party include the National People\'s Congress, the Central Committee, the Provincial Coordinating Committees, and Branch Committees.', 'options' => ['True', 'False'], 'correct' => 0],
            ],
            // Module 3: ZANU PF – Leagues & Wings
            [
                ['body' => 'The Women\'s Wing of ZANU PF is known as the Women\'s League.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Every woman member aged 18 or over is entitled to join the Women\'s League through her Branch Executive Committee.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The Youth Wing is known as the Youth League.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The Women\'s League aims include mobilising women and promoting rights, education and equality.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The Youth League promotes youth participation and development.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Women shall constitute at least one-third of the total membership of the principal organs at provincial level and below.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The Women\'s League is an autonomous wing of the Party.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Every fully paid up member of the Women\'s League is entitled to be issued with a membership card of the Women\'s League.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The Youth League aims align with Article 2 of the Party Constitution.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The Leagues have their own structures including conferences and executive councils.', 'options' => ['True', 'False'], 'correct' => 0],
            ],
            // Module 4: Constitution of Zimbabwe – Founding Values
            [
                ['body' => 'The Constitution of Zimbabwe (2013) is the supreme law of Zimbabwe.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Any law, practice, custom or conduct inconsistent with the Constitution of Zimbabwe is invalid to the extent of the inconsistency.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'According to the Constitution, Zimbabwe is:', 'options' => ['A federal republic', 'A unitary, democratic and sovereign republic', 'A confederation', 'A constitutional monarchy'], 'correct' => 1],
                ['body' => 'The obligations imposed by the Constitution are binding on every person, including the State and all executive, legislative and judicial institutions and agencies of government at every level.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Founding values and principles of Zimbabwe include supremacy of the Constitution and the rule of law.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Zimbabwe is founded on recognition of and respect for the liberation struggle.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The Preamble of the Constitution affirms the people\'s desire for freedom, justice and equality.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The Preamble commits the people to build a nation founded on values of transparency, equality, freedom, fairness, honesty and the dignity of hard work.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The Constitution recognises the need to entrench democracy, good, transparent and accountable governance and the rule of law.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Among the founding values is the nation\'s diverse cultural, religious and traditional values.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Founding values and principles include fundamental human rights and freedoms.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The Constitution acknowledges the supremacy of Almighty God, in whose hands our future lies.', 'options' => ['True', 'False'], 'correct' => 0],
            ],
            // Module 5: Constitution of Zimbabwe – Rights & Citizenship
            [
                ['body' => 'Persons are Zimbabwean citizens by birth, descent or registration.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'All Zimbabwean citizens are equally entitled to the rights, privileges and benefits of citizenship.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Zimbabwean citizens have the duty to be loyal to Zimbabwe and to observe the Constitution.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Every Zimbabwean citizen has the duty, to the best of their ability, to defend Zimbabwe and its sovereignty.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Chapter 4 (Declaration of Rights) binds the State and all executive, legislative and judicial institutions and agencies of government at every level.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The State and every person must respect, protect, promote and fulfil the rights and freedoms set out in Chapter 4.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Every person has the right to life.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Every person has the right to freedom of expression, which includes freedom to seek, receive and communicate ideas and other information.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Every Zimbabwean citizen aged 18 or over has the right to vote in all elections and referendums, and to do so in secret.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Every Zimbabwean citizen has the right to form, join and participate in the activities of a political party of their choice.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'All persons are equal before the law and have the right to equal protection and benefit of the law.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Zimbabwean citizenship is not lost through marriage or the dissolution of marriage.', 'options' => ['True', 'False'], 'correct' => 0],
            ],
            // Module 6: Constitution of Zimbabwe – Executive & Legislature
            [
                ['body' => 'Executive authority in Zimbabwe derives from the people of Zimbabwe and must be exercised in accordance with the Constitution.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The President is the Head of State and Government and the Commander-in-Chief of the Defence Forces.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The executive authority of Zimbabwe vests in the President who exercises it, subject to the Constitution, through the Cabinet.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The President must uphold, defend, obey and respect the Constitution as the supreme law of the nation.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The Legislature of Zimbabwe consists of Parliament and the President acting in accordance with Chapter 6.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Parliament consists of the Senate and the National Assembly.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Parliament must protect the Constitution and promote democratic governance in Zimbabwe.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The legislative authority of Zimbabwe is derived from the people and is vested in and exercised by the Legislature in accordance with the Constitution.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The Constitution establishes a multi-party democratic political system.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The Constitution requires observance of the principle of separation of powers.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'For the purpose of promoting multi-party democracy, an Act of Parliament must provide for the funding of political parties.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The Constitution provides for respect for the rights of all political parties.', 'options' => ['True', 'False'], 'correct' => 0],
            ],
            // Module 7: Constitution of Zimbabwe – Elections
            [
                ['body' => 'Elections must be free, fair and regular.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The Zimbabwe Electoral Commission oversees elections in Zimbabwe.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'All political parties must have reasonable access to materials and media during elections.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Voting in Zimbabwean elections must be by secret ballot.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Elections in Zimbabwe must be held at regular intervals as prescribed by law.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The Constitution provides for universal adult suffrage.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Which body supervises elections in Zimbabwe?', 'options' => ['The President', 'The Zimbabwe Electoral Commission', 'Parliament', 'The Cabinet'], 'correct' => 1],
                ['body' => 'Elections in Zimbabwe must be conducted in a manner that ensures the will of the people prevails.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The right to vote is protected under the Declaration of Rights.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Multi-party democracy requires that elections be competitive and transparent.', 'options' => ['True', 'False'], 'correct' => 0],
            ],
            // Module 8: Constitution of Zimbabwe – Judiciary & Rule of Law
            [
                ['body' => 'The Judiciary is independent and subject only to the Constitution.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Courts safeguard human rights and the rule of law.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The Constitutional Court determines constitutional matters.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Every person accused of an offence has the right to a fair trial.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Judges must not be subject to the direction or control of any person or authority in the exercise of their judicial functions.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Which court determines constitutional matters?', 'options' => ['The Magistrates\' Court', 'The High Court only', 'The Constitutional Court', 'The Supreme Court only'], 'correct' => 2],
                ['body' => 'The rule of law requires that all persons and institutions be subject to the law.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The Judiciary has the power to protect and enforce fundamental rights.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Constitutional matters include the interpretation of the Constitution.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The independence of the Judiciary is essential for the protection of rights.', 'options' => ['True', 'False'], 'correct' => 0],
            ],
            // Module 9: Constitution of Zimbabwe – Provincial & Local Government
            [
                ['body' => 'Zimbabwe has provincial councils and local authorities.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Government functions may be devolved to provincial and local levels.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Leadership principles in the Constitution include loyalty to Zimbabwe, honesty and accountability.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Devolution of power and responsibilities to provincial and metropolitan councils and local authorities is recognised.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Public officers must act in the interests of the people and not further the interests of any political party in their official duties.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Provincial councils exercise such functions as may be assigned to them by or under an Act of Parliament.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Local authorities have the right to govern on their own initiative the local affairs of their communities.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Leadership in Zimbabwe must be based on merit, integrity and commitment to the people.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'The Constitution provides for equitable sharing of national resources.', 'options' => ['True', 'False'], 'correct' => 0],
                ['body' => 'Provincial and local government structures must promote democratic participation and accountability.', 'options' => ['True', 'False'], 'correct' => 0],
            ],
        ];
    }
}
