<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Course;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademyController extends Controller
{
    public function index(): View
    {
        return $this->indexWithSearch();
    }

    private function indexWithSearch(): View
    {
        $query = Course::withCount(['modules', 'assessments'])->orderBy('title');

        $request = request();
        if ($request instanceof \Illuminate\Http\Request && $request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('code', 'like', "%{$q}%")
                    ->orWhere('title', 'like', "%{$q}%");
            });
        }

        $courses = $query->paginate(20)->withQueryString();

        $membershipCourse = Course::where('grants_membership', true)->where('status', 'published')->first();

        return view('admin.academy.index', compact('courses', 'membershipCourse'));
    }

    public function courseCreate(): View
    {
        $this->authorize('admin.section', 'academy');
        return view('admin.academy.course-form', ['course' => null]);
    }

    public function courseStore(Request $request): RedirectResponse
    {
        $this->authorize('admin.section', 'academy');
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:courses,code', 'regex:/^[A-Z0-9\-_]+$/i'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'level' => ['required', 'in:basic,intermediate,advanced'],
            'is_mandatory' => ['boolean'],
            'grants_membership' => ['boolean'],
            'certificate_title' => ['nullable', 'string', 'max:120'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);
        $data['is_mandatory'] = (bool) ($data['is_mandatory'] ?? false);
        $data['grants_membership'] = (bool) ($data['grants_membership'] ?? false);
        $data['created_by'] = auth()->id();
        $course = Course::create($data);
        return redirect()->route('admin.academy.courses.edit', $course)
            ->with('success', 'Course created. Add modules and an assessment to complete setup.')
            ->with('show_next_steps', true);
    }

    public function courseEdit(Course $course): View
    {
        $this->authorize('admin.section', 'academy');
        return view('admin.academy.course-form', ['course' => $course]);
    }

    public function courseUpdate(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('admin.section', 'academy');
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:courses,code,' . $course->id, 'regex:/^[A-Z0-9\-_]+$/i'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'level' => ['required', 'in:basic,intermediate,advanced'],
            'is_mandatory' => ['boolean'],
            'grants_membership' => ['boolean'],
            'certificate_title' => ['nullable', 'string', 'max:120'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);
        $data['is_mandatory'] = (bool) ($data['is_mandatory'] ?? false);
        $data['grants_membership'] = (bool) ($data['grants_membership'] ?? false);
        $course->update($data);
        return redirect()->route('admin.academy.index')->with('success', 'Course updated.');
    }

    public function courseDestroy(Course $course): RedirectResponse
    {
        $this->authorize('admin.section', 'academy');
        if ($course->modules()->exists()) {
            return redirect()->route('admin.academy.index')
                ->with('error', 'Cannot delete course with modules.');
        }
        $course->delete();
        return redirect()->route('admin.academy.index')->with('success', 'Course deleted.');
    }

    // Assessments CRUD
    public function assessmentsIndex(Course $course): View
    {
        $this->authorize('admin.section', 'academy');
        $query = $course->assessments()->withCount('questions')->orderBy('title');
        $request = request();
        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $query->where('title', 'like', "%{$q}%");
        }
        $assessments = $query->paginate(20)->withQueryString();
        return view('admin.academy.assessments.index', compact('course', 'assessments'));
    }

    public function assessmentCreate(Course $course): View
    {
        $this->authorize('admin.section', 'academy');
        return view('admin.academy.assessments.form', ['course' => $course, 'assessment' => null]);
    }

    public function assessmentStore(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('admin.section', 'academy');
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],
            'pass_mark' => ['required', 'integer', 'min:0', 'max:100'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);
        $data['course_id'] = $course->id;
        Assessment::create($data);
        return redirect()->route('admin.academy.assessments.index', $course)
            ->with('success', 'Assessment created.');
    }

    public function assessmentShow(Assessment $assessment): View
    {
        $this->authorize('admin.section', 'academy');
        $assessment->load([
            'questions' => fn ($q) => $q->with(['options', 'module'])->orderBy('order'),
            'course.modules' => fn ($q) => $q->orderBy('order'),
        ]);
        return view('admin.academy.assessments.show', compact('assessment'));
    }

    public function assessmentEdit(Assessment $assessment): View
    {
        $this->authorize('admin.section', 'academy');
        return view('admin.academy.assessments.form', ['course' => $assessment->course, 'assessment' => $assessment]);
    }

    public function assessmentUpdate(Request $request, Assessment $assessment): RedirectResponse
    {
        $this->authorize('admin.section', 'academy');
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],
            'pass_mark' => ['required', 'integer', 'min:0', 'max:100'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);
        $assessment->update($data);
        return redirect()->route('admin.academy.assessments.show', $assessment)
            ->with('success', 'Assessment updated.');
    }

    public function assessmentDestroy(Assessment $assessment): RedirectResponse
    {
        $this->authorize('admin.section', 'academy');
        $course = $assessment->course;
        $assessment->delete();
        return redirect()->route('admin.academy.assessments.index', $course)
            ->with('success', 'Assessment deleted.');
    }

    // Questions CRUD
    public function questionCreate(Assessment $assessment): View
    {
        $this->authorize('admin.section', 'academy');
        $assessment->load('course.modules');
        return view('admin.academy.questions.form', ['assessment' => $assessment, 'question' => null]);
    }

    public function questionStore(Request $request, Assessment $assessment): RedirectResponse
    {
        $this->authorize('admin.section', 'academy');
        $data = $request->validate([
            'body' => ['required', 'string'],
            'module_id' => ['nullable', 'integer', 'exists:modules,id'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'order' => ['nullable', 'integer', 'min:0'],
            'marks' => ['required', 'integer', 'min:1'],
            'options' => ['required', 'array', 'min:2'],
            'options.*.body' => ['required', 'string'],
            'correct_index' => ['required', 'integer', 'min:0', 'lt:' . count($request->input('options', []))],
        ]);
        $data['order'] = $data['order'] ?? $assessment->questions()->max('order') + 1;
        $data['module_id'] = $data['module_id'] ?? null;
        $question = $assessment->questions()->create([
            'body' => $data['body'],
            'module_id' => $data['module_id'],
            'difficulty' => $data['difficulty'],
            'order' => $data['order'],
            'marks' => $data['marks'],
        ]);
        $options = array_values($data['options']);
        $correctIdx = (int) $data['correct_index'];
        foreach ($options as $i => $opt) {
            $question->options()->create([
                'body' => $opt['body'],
                'is_correct' => $i === $correctIdx,
            ]);
        }
        return redirect()->route('admin.academy.assessments.show', $assessment)
            ->with('success', 'Question added.');
    }

    public function questionEdit(Question $question): View
    {
        $this->authorize('admin.section', 'academy');
        $question->load('assessment.course.modules', 'options');
        return view('admin.academy.questions.form', ['assessment' => $question->assessment, 'question' => $question]);
    }

    public function questionUpdate(Request $request, Question $question): RedirectResponse
    {
        $this->authorize('admin.section', 'academy');
        $data = $request->validate([
            'body' => ['required', 'string'],
            'module_id' => ['nullable', 'integer', 'exists:modules,id'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'order' => ['nullable', 'integer', 'min:0'],
            'marks' => ['required', 'integer', 'min:1'],
            'options' => ['required', 'array', 'min:2'],
            'options.*.id' => ['nullable', 'integer', 'exists:options,id'],
            'options.*.body' => ['required', 'string'],
            'correct_index' => ['required', 'integer', 'min:0', 'lt:' . count($request->input('options', []))],
        ]);
        $question->update([
            'body' => $data['body'],
            'module_id' => $data['module_id'] ?? null,
            'difficulty' => $data['difficulty'],
            'order' => $data['order'] ?? $question->order,
            'marks' => $data['marks'],
        ]);
        $options = array_values($data['options']);
        $correctIdx = (int) $data['correct_index'];
        $keepingIds = collect($options)->pluck('id')->filter()->map(fn ($v) => (int) $v)->all();
        foreach ($options as $i => $opt) {
            $payload = ['body' => $opt['body'], 'is_correct' => $i === $correctIdx];
            if (! empty($opt['id']) && in_array((int) $opt['id'], $keepingIds, true)) {
                $question->options()->where('id', $opt['id'])->update($payload);
            } else {
                $question->options()->create($payload);
            }
        }
        $question->options()->whereNotIn('id', $keepingIds)->delete();
        return redirect()->route('admin.academy.assessments.show', $question->assessment)
            ->with('success', 'Question updated.');
    }

    public function questionDestroy(Question $question): RedirectResponse
    {
        $this->authorize('admin.section', 'academy');
        $assessment = $question->assessment;
        $question->delete();
        return redirect()->route('admin.academy.assessments.show', $assessment)
            ->with('success', 'Question deleted.');
    }
}
