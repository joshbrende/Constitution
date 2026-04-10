<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\AttendanceRegister;
use App\Models\Certificate;
use App\Services\GamificationService;
use App\Models\Course;
use App\Models\CourseProgress;
use App\Models\Enrollment;
use App\Models\Note;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Unit;
use App\Models\UnitCompletion;
use App\Services\StepContentParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class LearnController extends Controller
{
    public function show(Request $request, Course $course)
    {
        $enrollment = Enrollment::where('user_id', Auth::id())->where('course_id', $course->id)->first();
        if (!$enrollment) {
            return redirect()->route('courses.show', $course)->with('message', 'Enroll first to access course content.');
        }

        $course->load(['units', 'instructor']);
        $units = $course->units;
        $this->recalculateProgress($enrollment, $course);
        $enrollment->refresh();
        $structured = $course->structured_curriculum;
        $unitIds = $this->curriculumOrderedUnitIds($structured);
        $start = $request->get('start') ? (int) $request->get('start') : null;
        $unitId = $request->get('unit') ? (int) $request->get('unit') : ($start === 1 && $unitIds->isNotEmpty() ? $unitIds->first() : null);

        // Use collection instead of separate query
        $current = $unitId ? $units->firstWhere('id', $unitId) : null;
        if ($current && !$units->contains('id', $current->id)) {
            $current = null;
        }

        $prev = null;
        $next = null;
        if ($current) {
            $idx = $unitIds->search($current->id);
            if ($idx > 0) {
                $prev = $unitIds->get($idx - 1);
            }
            if ($idx !== false && $idx < $unitIds->count() - 1) {
                $next = $unitIds->get($idx + 1);
            }
        }

        $completions = $enrollment->unitCompletions()->whereIn('unit_id', $unitIds)->get()->keyBy('unit_id');
        $unlockedUnitIds = $this->unlockedUnitIds($course, $unitIds);
        $currentLocked = $current && !$unlockedUnitIds->contains($current->id);

        $prevUnit = $prev ? $units->firstWhere('id', $prev) : null;
        $nextUnit = $next ? $units->firstWhere('id', $next) : null;
        $nextLocked = $nextUnit && !$unlockedUnitIds->contains($nextUnit->id);
        $currentIndex = $current ? $unitIds->search($current->id) + 1 : 0;
        $totalUnits = $unitIds->count();

        $showAttendanceRegister = $current && $this->isDay1OpeningUnit($current);
        $attendanceSubmitted = $showAttendanceRegister && $current
            ? AttendanceRegister::where('enrollment_id', $enrollment->id)->where('unit_id', $current->id)->exists()
            : false;

        $quiz = null;
        $quizPassed = false;
        $quizResultsAttempt = null;
        $showQuizResults = false;
        $userId = Auth::id();
        if ($current && $current->unit_type === 'quiz' && $current->quiz_id && !$currentLocked) {
            $quiz = Quiz::with('questions')->find($current->quiz_id);
            if ($quiz) {
                if (!$request->get('quiz_results') && $quiz->randomize_questions && $quiz->questions->isNotEmpty()) {
                    $quiz->setRelation('questions', $quiz->questions->shuffle());
                }
                // Optimize: single query for both checks
                $quizAttempts = QuizAttempt::where('quiz_id', $quiz->id)
                    ->where('user_id', $userId)
                    ->get();
                $quizPassed = $quizAttempts->where('status', 'passed')->isNotEmpty();
                if ($request->get('quiz_results')) {
                    $quizResultsAttempt = $quizAttempts->sortByDesc('completed_at')->first();
                    $showQuizResults = (bool) $quizResultsAttempt;
                }
            }
        }

        $assignment = null;
        $assignmentSubmission = null;
        if ($current && $current->unit_type === 'assignment' && !$currentLocked) {
            $assignment = $this->resolveAssignmentForUnit($current);
            $assignmentSubmission = $assignment
                ? AssignmentSubmission::where('assignment_id', $assignment->id)->where('user_id', Auth::id())->latest('submitted_at')->first()
                : null;
        }

        $currentNote = null;
        if ($current) {
            $currentNote = Note::where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->where('unit_id', $current->id)
                ->first();
        }

        $certificate = null;
        if ($enrollment && $enrollment->progress_percentage >= 100) {
            $certificate = \App\Models\Certificate::where('user_id', Auth::id())->where('course_id', $course->id)->first();
        }

        $steps = [];
        if ($current && in_array($current->unit_type, ['text', 'video', 'audio', 'document'])) {
            $steps = StepContentParser::fromHtml($current->content ?? '');
        }

        if ($current) {
            $user = Auth::user();
            if ($user) {
                $user->forceFill([
                    'last_learn_course_id' => $course->id,
                    'last_learn_unit_id' => $current->id,
                ])->save();
            }
        }

        return view('courses.learn', [
            'course' => $course,
            'enrollment' => $enrollment,
            'isFacilitator' => Auth::user() && Auth::user()->canEditCourse($course),
            'structuredCurriculum' => $structured,
            'units' => $units,
            'current' => $current,
            'prev' => $prev,
            'next' => $next,
            'prevUnit' => $prevUnit,
            'nextUnit' => $nextUnit,
            'nextLocked' => $nextLocked ?? false,
            'currentIndex' => $currentIndex,
            'totalUnits' => $totalUnits,
            'progress' => $completions,
            'showAttendanceRegister' => $showAttendanceRegister,
            'attendanceSubmitted' => $attendanceSubmitted,
            'unlockedUnitIds' => $unlockedUnitIds,
            'currentLocked' => $currentLocked,
            'quiz' => $quiz,
            'quizPassed' => $quizPassed,
            'showQuizResults' => $showQuizResults,
            'quizResultsAttempt' => $quizResultsAttempt,
            'assignment' => $assignment,
            'assignmentSubmission' => $assignmentSubmission,
            'certificate' => $certificate,
            'courseFinished' => (bool) $request->get('finished'),
            'steps' => $steps,
            'currentNote' => $currentNote,
        ]);
    }

    /** Ensure unit has an assignment; create from unit if missing. */
    private function resolveAssignmentForUnit(Unit $unit): Assignment
    {
        $unit->load('assignment');
        if ($unit->assignment_id && $unit->assignment) {
            return $unit->assignment;
        }
        $a = Assignment::create([
            'course_id' => $unit->course_id,
            'title' => $unit->title,
            'slug' => \Illuminate\Support\Str::slug($unit->title) . '-' . substr(md5((string) $unit->id), 0, 8),
            'description' => $unit->description,
            'instructions' => $unit->content,
            'duration' => (int) ($unit->duration ?? 0),
            'max_points' => 100,
            'allow_file_upload' => true,
            'max_file_size' => 5120,
            'assessment_type' => 'formative',
        ]);
        $unit->update(['assignment_id' => $a->id]);
        return $a;
    }

    public function storeAttendance(Request $request, Course $course)
    {
        $enrollment = Enrollment::where('user_id', Auth::id())->where('course_id', $course->id)->first();
        if (!$enrollment) {
            abort(403);
        }

        $valid = $request->validate([
            'unit_id' => 'required|integer|exists:units,id',
            'title' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'organisation' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'email' => 'required|email',
        ]);

        $unit = Unit::findOrFail($valid['unit_id']);
        if ($unit->course_id !== $course->id || !$this->isDay1OpeningUnit($unit)) {
            abort(400, 'Attendance register is only for Day 1: Opening & Course Overview.');
        }

        AttendanceRegister::updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'unit_id' => $unit->id,
            ],
            [
                'course_id' => $course->id,
                'user_id' => Auth::id(),
                'title' => $valid['title'] ?? null,
                'name' => $valid['name'],
                'surname' => $valid['surname'],
                'designation' => $valid['designation'] ?? null,
                'organisation' => $valid['organisation'] ?? null,
                'contact_number' => $valid['contact_number'] ?? null,
                'email' => $valid['email'],
            ]
        );

        return redirect()
            ->route('learn.show', ['course' => $course, 'unit' => $unit->id])
            ->with('message', 'Attendance registered successfully.');
    }

    public function submitAssignment(Request $request, Course $course, Unit $unit)
    {
        $enrollment = Enrollment::where('user_id', Auth::id())->where('course_id', $course->id)->first();
        if (!$enrollment || $unit->course_id !== $course->id || $unit->unit_type !== 'assignment') {
            abort(403);
        }
        if (!$this->isUnitUnlocked($unit, $course)) {
            return redirect()->route('learn.show', ['course' => $course, 'unit' => $unit->id])
                ->with('message', 'This module is locked. Pass the previous Knowledge Check first.');
        }

        $assignment = $this->resolveAssignmentForUnit($unit);
        $existing = AssignmentSubmission::where('assignment_id', $assignment->id)->where('user_id', Auth::id())->first();
        if ($existing) {
            return redirect()->route('learn.show', ['course' => $course, 'unit' => $unit->id])
                ->with('message', 'You have already submitted this assignment.');
        }

        $rules = [
            'content' => 'required|string|max:20000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:5120',
        ];
        $valid = $request->validate($rules);

        $paths = [];
        $dir = 'assignment-attachments';
        foreach ($request->file('attachments') ?? [] as $i => $file) {
            $name = $file->getClientOriginalName();
            $ext = $file->getClientOriginalExtension();
            $path = $file->storeAs($dir, time() . '-' . $i . '-' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $name), 'public');
            $paths[] = ['path' => $path, 'name' => $name];
        }

        AssignmentSubmission::create([
            'user_id' => Auth::id(),
            'assignment_id' => $assignment->id,
            'course_id' => $course->id,
            'content' => $valid['content'],
            'attachments' => $paths,
            'submitted_at' => now(),
            'max_points' => $assignment->max_points,
            'status' => 'submitted',
        ]);

        return redirect()->route('learn.show', ['course' => $course, 'unit' => $unit->id])
            ->with('message', 'Assignment submitted successfully. Your instructor will grade it.');
    }

    private function isDay1OpeningUnit(Unit $unit): bool
    {
        $t = $unit->title;
        return (bool) preg_match('/^Day\s*1\s*:?\s*Opening.*Course\s*Over/i', $t)
            || stripos($t, 'Day 1: Opening') !== false;
    }

    /** Module number from unit title (e.g. "Module 3: Quiz" -> 3), or null. */
    private function unitModuleNumber(Unit $unit): ?int
    {
        return preg_match('/Module\s*(\d+)/i', $unit->title, $m) ? (int) $m[1] : null;
    }

    /** User has passed the quiz that guards access to $moduleNum (Quiz for Module $moduleNum - 1). */
    private function hasPassedModuleQuiz(int $moduleNum, Course $course): bool
    {
        if ($moduleNum <= 1) {
            return true;
        }
        $guardUnit = $course->units()->where('unit_type', 'quiz')
            ->whereIn('title', ['Module ' . ($moduleNum - 1) . ': Quiz', 'Module ' . ($moduleNum - 1) . ': Knowledge Check'])->first();
        if (!$guardUnit || !$guardUnit->quiz_id) {
            return true;
        }
        return QuizAttempt::where('quiz_id', $guardUnit->quiz_id)
            ->where('user_id', Auth::id())
            ->where('status', 'passed')
            ->exists();
    }

    private function isUnitUnlocked(Unit $unit, Course $course): bool
    {
        $mod = $this->unitModuleNumber($unit);
        if ($mod === null) {
            return true;
        }
        return $this->hasPassedModuleQuiz($mod, $course);
    }

    /** Unit IDs that the current user is allowed to access (quiz gates applied). */
    private function unlockedUnitIds(Course $course, \Illuminate\Support\Collection $unitIds): \Illuminate\Support\Collection
    {
        $unlocked = collect();
        foreach ($unitIds as $id) {
            $unit = $course->units->firstWhere('id', $id);
            if (!$unit) {
                continue;
            }
            if ($this->isUnitUnlocked($unit, $course)) {
                $unlocked->push($id);
            }
        }
        return $unlocked;
    }

    /** Unit IDs in curriculum order: DAY 1 → DAY 2 → DAY 3 → trailing (Course Closure). */
    private function curriculumOrderedUnitIds(array $structured): \Illuminate\Support\Collection
    {
        $ids = collect();
        $days = $structured['days'] ?? [];
        foreach ([1, 2, 3] as $d) {
            if (empty($days[$d])) {
                continue;
            }
            foreach ($days[$d]['standalones'] ?? [] as $item) {
                $ids->push($item['id']);
            }
            $mods = $days[$d]['modules'] ?? [];
            ksort($mods);
            foreach ($mods as $items) {
                foreach ($items as $item) {
                    $ids->push($item['id']);
                }
            }
        }
        foreach ($structured['trailing'] ?? [] as $item) {
            $ids->push($item['id']);
        }
        return $ids;
    }

    public function completeUnit(Request $request, Course $course, Unit $unit)
    {
        $enrollment = Enrollment::where('user_id', Auth::id())->where('course_id', $course->id)->first();
        if (!$enrollment) {
            abort(403);
        }
        if ($unit->course_id !== $course->id) {
            abort(404);
        }
        if ($unit->unit_type === 'quiz' || $unit->unit_type === 'assignment') {
            return redirect()->route('learn.show', ['course' => $course, 'unit' => $unit->id])
                ->with('message', $unit->unit_type === 'quiz' ? 'Complete the Knowledge Check above to progress.' : 'Submit the assignment above to progress.');
        }

        $uc = UnitCompletion::firstOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'unit_id' => $unit->id,
            ],
            [
                'user_id' => Auth::id(),
                'course_id' => $course->id,
                'completed_at' => now(),
            ]
        );
        if ($uc->wasRecentlyCreated) {
            app(GamificationService::class)->awardPoints(Auth::user(), 5);
        }

        $this->recalculateProgress($enrollment, $course);
        $unitIds = $this->curriculumOrderedUnitIds($course->structured_curriculum);
        $next = $this->nextUnitIdFromOrder($unitIds, $unit->id);
        if ($next) {
            return redirect()->route('learn.show', ['course' => $course, 'unit' => $next]);
        }
        return redirect()
            ->route('learn.show', ['course' => $course, 'unit' => $unit->id, 'finished' => 1])
            ->with('message', 'You have completed the course. Congratulations!');
    }

    private function nextUnitIdFromOrder(\Illuminate\Support\Collection $unitIds, int $currentUnitId): ?int
    {
        $idx = $unitIds->search($currentUnitId);
        if ($idx === false || $idx >= $unitIds->count() - 1) {
            return null;
        }
        return $unitIds->get($idx + 1);
    }

    public function submitQuiz(Request $request, Course $course, Unit $unit)
    {
        $enrollment = Enrollment::where('user_id', Auth::id())->where('course_id', $course->id)->first();
        if (!$enrollment || $unit->course_id !== $course->id || $unit->unit_type !== 'quiz' || !$unit->quiz_id) {
            abort(403);
        }
        if (!$this->isUnitUnlocked($unit, $course)) {
            return redirect()->route('learn.show', ['course' => $course, 'unit' => $unit->id])
                ->with('message', 'This module is locked. Pass the previous module\'s Knowledge Check first.');
        }

        $quiz = Quiz::with('questions')->findOrFail($unit->quiz_id);
        $questionIds = $quiz->questions->pluck('id')->all();
        $rules = ['answers' => 'required|array'];
        foreach ($questionIds as $qid) {
            $rules['answers.' . $qid] = 'required|string|max:500';
        }
        $valid = $request->validate($rules);

        $score = 0;
        $totalPoints = 0;
        $answers = [];
        foreach ($quiz->questions as $q) {
            $totalPoints += $q->points;
            $submitted = $valid['answers'][$q->id] ?? null;
            $correct = $submitted !== null && $q->isCorrectAnswer($submitted);
            if ($correct) {
                $score += $q->points;
            }
            $answers[$q->id] = ['value' => $submitted, 'correct' => $correct];
        }
        $pct = $totalPoints > 0 ? (float) round(100 * $score / $totalPoints, 2) : 0.0;
        $passed = $pct >= (float) $quiz->pass_percentage;
        $status = $passed ? 'passed' : 'failed';

        $attemptNum = (int) QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', Auth::id())->max('attempt_number') + 1;

        DB::transaction(function () use ($quiz, $course, $attemptNum, $answers, $score, $totalPoints, $pct, $status, $enrollment, $unit) {
            QuizAttempt::create([
                'user_id' => Auth::id(),
                'quiz_id' => $quiz->id,
                'course_id' => $course->id,
                'attempt_number' => $attemptNum,
                'started_at' => now(),
                'completed_at' => now(),
                'time_taken' => 0,
                'answers' => $answers,
                'score' => $score,
                'total_points' => $totalPoints,
                'percentage' => $pct,
                'status' => $status,
                'grading_status' => 'graded',
            ]);
            if ($status === 'passed') {
                UnitCompletion::firstOrCreate(
                    [
                        'enrollment_id' => $enrollment->id,
                        'unit_id' => $unit->id,
                    ],
                    [
                        'user_id' => Auth::id(),
                        'course_id' => $course->id,
                        'completed_at' => now(),
                    ]
                );
                $this->recalculateProgress($enrollment, $course);
                $svc = app(GamificationService::class);
                $svc->awardPoints(Auth::user(), 15);
                $svc->ensureBadge(Auth::user(), 'quiz-master');
            }
        });

        return redirect()
            ->route('learn.show', ['course' => $course, 'unit' => $unit->id, 'quiz_results' => 1])
            ->with('message', $passed
                ? "Knowledge Check passed! You scored {$score}/{$totalPoints} ({$pct}%). The next module is now unlocked."
                : "Knowledge Check not passed. You scored {$score}/{$totalPoints} ({$pct}%). You need {$quiz->pass_percentage}% to unlock the next module. You may try again.")
            ->with('quiz_result', ['score' => $pct, 'passed' => $passed]);
    }

    private function recalculateProgress(Enrollment $enrollment, Course $course): void
    {
        $course->load('units');
        $total = $course->units->count();
        if ($total === 0) {
            return;
        }
        $unitIds = $course->units->pluck('id');
        $done = $enrollment->unitCompletions()->whereIn('unit_id', $unitIds)->count();
        $progress = (int) round(100 * $done / $total);

        $enrollment->update([
            'progress_percentage' => $progress,
            'progress_status' => $progress >= 100 ? 'completed' : 'in_progress',
            'started_at' => $enrollment->started_at ?? now(),
            'completed_at' => $progress >= 100 ? now() : null,
        ]);

        if ($progress >= 100) {
            $cert = Certificate::ensureForUserAndCourse($enrollment->user_id, $course->id);
            if ($cert && $cert->wasRecentlyCreated) {
                $u = \App\Models\User::find($enrollment->user_id);
                if ($u) {
                    $svc = app(GamificationService::class);
                    $svc->awardPoints($u, 50);
                    $svc->ensureBadge($u, 'course-complete');
                }
            }
        }

        $cp = CourseProgress::firstOrCreate(
            ['user_id' => $enrollment->user_id, 'course_id' => $course->id],
            [
                'units_completed' => 0,
                'total_units' => $total,
                'quizzes_completed' => 0,
                'total_quizzes' => 0,
                'assignments_completed' => 0,
                'total_assignments' => 0,
                'overall_progress' => 0,
            ]
        );
        $cp->update([
            'units_completed' => $done,
            'total_units' => $total,
            'overall_progress' => $progress,
            'last_activity_at' => now(),
        ]);
    }
}
