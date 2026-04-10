<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class UnitController extends Controller
{
    public function edit(Course $course, Unit $unit)
    {
        if (!Auth::user()->canEditCourses()) {
            abort(403, 'Only facilitators and admins can edit units.');
        }
        if (!Auth::user()->canEditCourse($course)) {
            abort(403, 'You can only edit units in courses you are instructing.');
        }
        if ($unit->course_id !== $course->id) {
            abort(404);
        }
        $unit->load('quiz');
        return view('units.edit', compact('course', 'unit'));
    }

    public function update(Request $request, Course $course, Unit $unit)
    {
        if (!Auth::user()->canEditCourses()) {
            abort(403, 'Only facilitators and admins can edit units.');
        }
        if (!Auth::user()->canEditCourse($course)) {
            abort(403, 'You can only edit units in courses you are instructing.');
        }
        if ($unit->course_id !== $course->id) {
            abort(404);
        }

        $valid = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'description' => 'nullable|string|max:500',
            'duration' => 'nullable|integer|min:0|max:999',
            'unit_type' => 'required|in:text,video,audio,document,assignment,quiz',
            'video_url' => 'nullable|string|max:500',
            'audio_url' => 'nullable|string|max:500',
            'document_url' => 'nullable|string|max:500',
            'order' => 'nullable|integer|min:0',
        ]);

        $unit->update([
            'title' => $valid['title'],
            'content' => $valid['content'] ?? '',
            'description' => $valid['description'] ?? null,
            'duration' => $valid['duration'] ?? null,
            'unit_type' => $valid['unit_type'],
            'video_url' => $valid['video_url'] ?? null,
            'audio_url' => $valid['audio_url'] ?? null,
            'document_url' => $valid['document_url'] ?? null,
            'order' => (int) ($valid['order'] ?? $unit->order),
        ]);

        return redirect()
            ->route('courses.edit', $course)
            ->with('message', 'Unit updated.');
    }

    public function refreshFromFile(Request $request, Course $course, Unit $unit)
    {
        if (!Auth::user()->canEditCourses()) {
            abort(403, 'Only facilitators and admins can refresh unit content.');
        }
        if (!Auth::user()->canEditCourse($course)) {
            abort(403, 'You can only edit units in courses you are instructing.');
        }
        if ($unit->course_id !== $course->id) {
            abort(404);
        }
        if ($unit->title !== 'Module 1: Introduction') {
            return redirect()
                ->route('units.edit', [$course, $unit])
                ->with('message', 'Reload from file is only available for the "Module 1: Introduction" unit.');
        }

        $path = base_path('database/seeders/module1_sections/01_introduction.md');
        if (!is_file($path)) {
            return redirect()
                ->route('units.edit', [$course, $unit])
                ->with('message', 'File 01_introduction.md not found. Run the RefreshModule1IntroductionSeeder instead.');
        }

        $md = file_get_contents($path);
        $md = str_replace('**Time Slot:**', 'Time Slot:', $md);
        $html = Str::markdown($md);

        $unit->update(['content' => $html, 'duration' => 6]);

        return redirect()
            ->route('units.edit', [$course, $unit])
            ->with('message', 'Content and duration reloaded from 01_introduction.md.');
    }

    public function editQuiz(Course $course, Unit $unit)
    {
        if (!Auth::user()->canEditCourses()) {
            abort(403, 'Only facilitators and admins can edit knowledge checks.');
        }
        if (!Auth::user()->canEditCourse($course)) {
            abort(403, 'You can only edit knowledge checks in courses you are instructing.');
        }
        if ($unit->course_id !== $course->id) {
            abort(404);
        }
        if ($unit->unit_type !== 'quiz' || !$unit->quiz_id) {
            return redirect()->route('units.edit', [$course, $unit])
                ->with('message', 'This unit is not linked to a Knowledge Check.');
        }
        $unit->load(['quiz' => fn ($q) => $q->with('questions')]);
        $quiz = $unit->quiz;
        return view('units.quiz-edit', compact('course', 'unit', 'quiz'));
    }

    public function updateQuiz(Request $request, Course $course, Unit $unit)
    {
        if (!Auth::user()->canEditCourses()) {
            abort(403, 'Only facilitators and admins can edit knowledge checks.');
        }
        if (!Auth::user()->canEditCourse($course)) {
            abort(403, 'You can only edit knowledge checks in courses you are instructing.');
        }
        if ($unit->course_id !== $course->id) {
            abort(404);
        }
        if ($unit->unit_type !== 'quiz' || !$unit->quiz_id) {
            return redirect()->route('units.edit', [$course, $unit])
                ->with('message', 'This unit is not linked to a Knowledge Check.');
        }

        $valid = $request->validate([
            'quiz_title' => 'required|string|max:255',
            'instructions' => 'nullable|string|max:2000',
            'pass_percentage' => 'required|integer|min:1|max:100',
            'randomize_questions' => 'nullable|boolean',
            'questions' => 'nullable|array',
            'questions.*.question' => 'required|string|max:2000',
            'questions.*.type' => 'nullable|in:multiple_choice,true_false,short_answer',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*' => 'nullable|string|max:1000',
            'questions.*.correct_index' => 'nullable|integer|min:0',
            'questions.*.correct_text' => 'nullable|string|max:1000',
            'questions.*.points' => 'nullable|integer|min:1|max:100',
        ]);

        $quiz = Quiz::findOrFail($unit->quiz_id);
        $quiz->update([
            'title' => $valid['quiz_title'],
            'instructions' => $valid['instructions'] ?? null,
            'pass_percentage' => (int) $valid['pass_percentage'],
            'randomize_questions' => (bool) ($valid['randomize_questions'] ?? false),
        ]);

        $questionsIn = $valid['questions'] ?? [];
        $toCreate = [];
        foreach ($questionsIn as $i => $q) {
            $qType = $q['type'] ?? 'multiple_choice';

            if ($qType === 'true_false') {
                $correctIdx = (int) ($q['correct_index'] ?? 0);
                if ($correctIdx !== 1) {
                    $correctIdx = 0;
                }
                $toCreate[] = [
                    'quiz_id' => $quiz->id,
                    'question' => $q['question'],
                    'type' => 'true_false',
                    'options' => [['text' => 'True', 'value' => '1'], ['text' => 'False', 'value' => '0']],
                    'correct_answers' => [$correctIdx === 0 ? '1' : '0'],
                    'points' => (int) ($q['points'] ?? 1),
                    'order' => $i + 1,
                ];
                continue;
            }

            if ($qType === 'short_answer') {
                $text = trim((string) ($q['correct_text'] ?? ''));
                $answers = $text === '' ? [] : array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $text))));
                $toCreate[] = [
                    'quiz_id' => $quiz->id,
                    'question' => $q['question'],
                    'type' => 'short_answer',
                    'options' => [],
                    'correct_answers' => $answers,
                    'points' => (int) ($q['points'] ?? 1),
                    'order' => $i + 1,
                ];
                continue;
            }

            $opts = is_array($q['options'] ?? null) ? $q['options'] : [];
            $opts = array_values(array_filter(array_map('trim', $opts), fn ($t) => $t !== ''));
            if (count($opts) < 2) {
                continue;
            }
            $correctIdx = (int) ($q['correct_index'] ?? 0);
            if ($correctIdx >= count($opts)) {
                $correctIdx = 0;
            }
            $built = [];
            $correctVal = null;
            foreach ($opts as $idx => $text) {
                $val = (string) $idx;
                $built[] = ['text' => $text, 'value' => $val];
                if ($idx === $correctIdx) {
                    $correctVal = $val;
                }
            }
            $toCreate[] = [
                'quiz_id' => $quiz->id,
                'question' => $q['question'],
                'type' => 'multiple_choice',
                'options' => $built,
                'correct_answers' => $correctVal ? [$correctVal] : [$built[0]['value']],
                'points' => (int) ($q['points'] ?? 1),
                'order' => $i + 1,
            ];
        }

        DB::transaction(function () use ($quiz, $toCreate) {
            $quiz->questions()->delete();
            foreach ($toCreate as $q) {
                Question::create($q);
            }
            $quiz->update(['total_points' => array_sum(array_column($toCreate, 'points'))]);
        });

        return redirect()
            ->route('units.quiz.edit', [$course, $unit])
            ->with('message', 'Knowledge Check updated.');
    }
}
