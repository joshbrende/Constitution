<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ModuleQuizzesSeeder extends Seeder
{
    private const PASS_PERCENTAGE = 70;

    /** Module number => [['question' => '...', 'options' => [...], 'correct' => 'value'], ...] */
    private function moduleQuestions(): array
    {
        return [
            1 => [
                ['q' => 'How many strategic outcomes does SALGA identify for 2026?', 'opts' => ['4', '5', '6', '7'], 'correct' => '6'],
                ['q' => 'What approximate municipal debt (in billion rand) was cited as a critical challenge?', 'opts' => ['R206.7B', 'R306.7B', 'R406.7B', 'R506.7B'], 'correct' => 'R306.7B'],
                ['q' => 'Which framework links IDPs, SDBIPs, and budgets?', 'opts' => ['DDM', 'MTSF', 'SALGA APP', 'All of the above'], 'correct' => 'All of the above'],
            ],
            2 => [
                ['q' => 'Which of these is a branch of AI?', 'opts' => ['Machine Learning', 'Natural Language Processing', 'Computer Vision', 'All of the above'], 'correct' => 'All of the above'],
                ['q' => 'Data privacy in South Africa is governed by:', 'opts' => ['POPI Act', 'MFMA', 'Municipal Systems Act', 'Labour Relations Act'], 'correct' => 'POPI Act'],
                ['q' => 'AI in government can support:', 'opts' => ['Document analysis', 'Sentiment analysis of feedback', 'Predictive analytics', 'All of the above'], 'correct' => 'All of the above'],
            ],
            3 => [
                ['q' => 'Results-based performance management often uses:', 'opts' => ['Theory of Change', 'Logic Models', 'Outcome indicators', 'All of the above'], 'correct' => 'All of the above'],
                ['q' => 'IDP stands for:', 'opts' => ['Integrated Development Plan', 'Internal Data Platform', 'Indicator Design Process', 'Institutional Development Policy'], 'correct' => 'Integrated Development Plan'],
                ['q' => 'The District Development Model (DDM) supports:', 'opts' => ['Integrated planning', 'Aligned delivery', 'Coordination across spheres', 'All of the above'], 'correct' => 'All of the above'],
            ],
            4 => [
                ['q' => 'Data foundation for AI includes:', 'opts' => ['Data quality and governance', 'Data integration', 'Data cataloging', 'All of the above'], 'correct' => 'All of the above'],
                ['q' => 'Big data in municipal context refers to:', 'opts' => ['Large volumes of structured/unstructured data', 'High-velocity data', 'Data from multiple sources', 'All of the above'], 'correct' => 'All of the above'],
                ['q' => 'AI can help with:', 'opts' => ['Data quality assessment', 'Automated data cleaning', 'Data discovery', 'All of the above'], 'correct' => 'All of the above'],
            ],
            5 => [
                ['q' => 'KPI dashboards can show:', 'opts' => ['Real-time metrics', 'Predictive indicators', 'Anomaly alerts', 'All of the above'], 'correct' => 'All of the above'],
                ['q' => 'Leading indicators help:', 'opts' => ['Predict future performance', 'Explain past performance only', 'Replace lagging indicators', 'None of the above'], 'correct' => 'Predict future performance'],
                ['q' => 'Dashboard design should consider:', 'opts' => ['Visual clarity', 'Mobile responsiveness', 'User needs', 'All of the above'], 'correct' => 'All of the above'],
            ],
            6 => [
                ['q' => 'AI can support revenue collection by:', 'opts' => ['Predicting collection', 'Prioritising debt', 'Fraud detection', 'All of the above'], 'correct' => 'All of the above'],
                ['q' => 'The target debt collection period (days) often cited is:', 'opts' => ['Under 60', 'Under 90', 'Under 120', 'Under 150'], 'correct' => 'Under 90'],
                ['q' => 'Budget variance analysis can be:', 'opts' => ['Automated', 'Explained by AI', 'Used for forecasting', 'All of the above'], 'correct' => 'All of the above'],
            ],
            7 => [
                ['q' => 'Service delivery standards include:', 'opts' => ['Accessibility', 'Affordability', 'Quality', 'All of the above'], 'correct' => 'All of the above'],
                ['q' => 'IoT sensors can support:', 'opts' => ['Infrastructure monitoring', 'Predictive maintenance', 'Service quality scoring', 'All of the above'], 'correct' => 'All of the above'],
                ['q' => 'Citizen satisfaction can be measured using:', 'opts' => ['Surveys', 'Sentiment analysis', 'Feedback systems', 'All of the above'], 'correct' => 'All of the above'],
            ],
            8 => [
                ['q' => 'MFMA compliance monitoring can be:', 'opts' => ['Automated', 'Supported by risk scoring', 'Part of early warning systems', 'All of the above'], 'correct' => 'All of the above'],
                ['q' => 'Early warning systems help:', 'opts' => ['Identify governance risks', 'Prepare for audits', 'Track legislative compliance', 'All of the above'], 'correct' => 'All of the above'],
                ['q' => 'Audit preparation can be supported by:', 'opts' => ['AI-assisted audit trails', 'Predictive risk modeling', 'Automated compliance checks', 'All of the above'], 'correct' => 'All of the above'],
            ],
            9 => [
                ['q' => 'Predictive analytics can support:', 'opts' => ['Demand forecasting', 'Resource allocation', 'Scenario planning', 'All of the above'], 'correct' => 'All of the above'],
                ['q' => 'Population growth models help with:', 'opts' => ['Long-term planning', 'Service demand', 'Infrastructure planning', 'All of the above'], 'correct' => 'All of the above'],
                ['q' => 'Budget forecasting with AI enables:', 'opts' => ['Proactive decisions', 'Better allocation', 'Risk awareness', 'All of the above'], 'correct' => 'All of the above'],
            ],
            10 => [
                ['q' => 'Citizen engagement platforms can include:', 'opts' => ['Digital feedback', 'Complaint management', 'IDP participation', 'All of the above'], 'correct' => 'All of the above'],
                ['q' => 'AI chatbots can:', 'opts' => ['Answer citizen queries', 'Route complaints', 'Support transparency', 'All of the above'], 'correct' => 'All of the above'],
                ['q' => 'Sentiment analysis of feedback helps:', 'opts' => ['Understand satisfaction', 'Improve services', 'Identify priorities', 'All of the above'], 'correct' => 'All of the above'],
            ],
            11 => [
                ['q' => 'AI implementation typically requires:', 'opts' => ['Change management', 'Phased approach', 'Resource planning', 'All of the above'], 'correct' => 'All of the above'],
                ['q' => 'Success factors for AI adoption include:', 'opts' => ['Stakeholder buy-in', 'Clear metrics', 'Vendor selection', 'All of the above'], 'correct' => 'All of the above'],
                ['q' => 'An AI maturity assessment helps:', 'opts' => ['Plan implementation', 'Set realistic expectations', 'Identify gaps', 'All of the above'], 'correct' => 'All of the above'],
            ],
            12 => [
                ['q' => 'Action planning should include:', 'opts' => ['Prioritised initiatives', 'Resources and timeline', 'Success metrics', 'All of the above'], 'correct' => 'All of the above'],
                ['q' => 'AI-assisted action plan tools can:', 'opts' => ['Generate drafts', 'Support validation', 'Facilitate peer review', 'All of the above'], 'correct' => 'All of the above'],
                ['q' => 'Implementation readiness depends on:', 'opts' => ['Concrete next steps', 'Stakeholder alignment', 'Resource identification', 'All of the above'], 'correct' => 'All of the above'],
            ],
        ];
    }

    public function run(): void
    {
        $course = Course::where('slug', 'like', '%performance-management%')
            ->orWhere('title', 'like', '%Performance Management%')
            ->first();

        if (!$course) {
            $this->command->warn('Performance Management course not found.');
            return;
        }

        $questionsConfig = $this->moduleQuestions();

        foreach (range(1, 12) as $modNum) {
            $units = $course->units()->orderByRaw('`order` asc')->get();
            $quizUnit = $units->first(fn ($u) => $u->unit_type === 'quiz' && preg_match('/Module\s*' . $modNum . '\s*:?\s*(Quiz|Knowledge\s*Check)/i', $u->title));
            if ($quizUnit) {
                $this->command->info("Module {$modNum} Knowledge Check already exists.");
                continue;
            }

            $moduleUnits = $units->filter(fn ($u) => preg_match('/Module\s*' . $modNum . '\b/i', $u->title));
            $last = $moduleUnits->sortByDesc('order')->first();
            if (!$last) {
                $this->command->warn("No units found for Module {$modNum}. Skipping.");
                continue;
            }

            $baseOrder = (int) $last->order;
            $config = $questionsConfig[$modNum] ?? $this->defaultQuestions($modNum);

            DB::transaction(function () use ($course, $config, $baseOrder, $modNum) {
                $totalPoints = count($config) * 1; // 1 point per question
                $quiz = Quiz::create([
                    'course_id' => $course->id,
                    'title' => "Module {$modNum} Knowledge Check",
                    'slug' => Str::slug("module-{$modNum}-quiz") . '-' . uniqid(),
                    'description' => "Assessment for Module {$modNum}. Pass mark: " . self::PASS_PERCENTAGE . '%.',
                    'instructions' => 'Answer all questions. You must score at least ' . self::PASS_PERCENTAGE . '% to unlock the next module.',
                    'duration' => 10,
                    'pass_percentage' => self::PASS_PERCENTAGE,
                    'max_attempts' => 5,
                    'randomize_questions' => false,
                    'show_results' => true,
                    'show_correct_answers' => true,
                    'total_points' => $totalPoints,
                    'grading_type' => 'auto',
                    'assessment_type' => 'summative',
                ]);

                foreach ($config as $i => $c) {
                    $opts = [];
                    $correctVal = null;
                    foreach ($c['opts'] as $idx => $text) {
                        $val = 'v' . $idx;
                        $opts[] = ['text' => $text, 'value' => $val];
                        if ((string) $text === (string) $c['correct']) {
                            $correctVal = $val;
                        }
                    }
                    $correct = $correctVal ? [$correctVal] : ['v0'];
                    Question::create([
                        'quiz_id' => $quiz->id,
                        'question' => $c['q'],
                        'type' => 'multiple_choice',
                        'options' => $opts,
                        'correct_answers' => $correct,
                        'points' => 1,
                        'order' => $i + 1,
                    ]);
                }

                Unit::where('course_id', $course->id)->where('order', '>', $baseOrder)->increment('order', 1);

                Unit::create([
                    'course_id' => $course->id,
                    'title' => "Module {$modNum}: Knowledge Check",
                    'slug' => 'module-' . $modNum . '-quiz-' . uniqid(),
                    'content' => '<p>Complete this Knowledge Check to verify your understanding of Module ' . $modNum . '. You need at least ' . self::PASS_PERCENTAGE . '% to unlock the next module.</p>',
                    'description' => "Module {$modNum} assessment",
                    'order' => $baseOrder + 1,
                    'unit_type' => 'quiz',
                    'duration' => 10,
                    'quiz_id' => $quiz->id,
                ]);
            });

            $this->command->info("Module {$modNum} Knowledge Check created.");
        }
    }

    private function defaultQuestions(int $modNum): array
    {
        return [
            ['q' => "What is a key takeaway from Module {$modNum}?", 'opts' => ['Option A', 'Option B', 'Option C', 'Option D'], 'correct' => 'Option A'],
            ['q' => "Which concept from Module {$modNum} is most relevant to your context?", 'opts' => ['Concept 1', 'Concept 2', 'Concept 3', 'Concept 4'], 'correct' => 'Concept 1'],
            ['q' => "How would you apply Module {$modNum} content in practice?", 'opts' => ['Approach 1', 'Approach 2', 'Approach 3', 'Approach 4'], 'correct' => 'Approach 1'],
        ];
    }
}
