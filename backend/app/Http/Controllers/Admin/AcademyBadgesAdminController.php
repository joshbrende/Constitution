<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademyBadge;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademyBadgesAdminController extends Controller
{
    public function index(Course $course): View
    {
        $query = AcademyBadge::where('course_id', $course->id)->orderBy('id');
        $request = request();
        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('rule_type', 'like', "%{$q}%");
            });
        }

        $badges = $query->paginate(20)->withQueryString();

        return view('admin.academy.badges.index', compact('course', 'badges'));
    }

    public function create(Course $course): View
    {
        return view('admin.academy.badges.form', [
            'course' => $course,
            'badge' => null,
        ]);
    }

    public function store(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('admin.section', 'academy');
        $data = $this->validateBadge($request);

        AcademyBadge::create([
            ...$data,
            'course_id' => $course->id,
        ]);

        return redirect()->route('admin.academy.badges.index', $course)
            ->with('success', 'Badge criteria saved.');
    }

    public function edit(Course $course, AcademyBadge $badge): View
    {
        // Ensure badge belongs to this course
        if ((int) $badge->course_id !== (int) $course->id) {
            abort(404);
        }

        return view('admin.academy.badges.form', [
            'course' => $course,
            'badge' => $badge,
        ]);
    }

    public function update(Request $request, Course $course, AcademyBadge $badge): RedirectResponse
    {
        if ((int) $badge->course_id !== (int) $course->id) {
            abort(404);
        }

        $data = $this->validateBadge($request, $badge);
        $badge->update($data);

        return redirect()->route('admin.academy.badges.index', $course)
            ->with('success', 'Badge updated.');
    }

    public function destroy(Course $course, AcademyBadge $badge): RedirectResponse
    {
        $this->authorize('admin.section', 'academy');
        if ((int) $badge->course_id !== (int) $course->id) {
            abort(404);
        }

        $badge->delete();

        return redirect()->route('admin.academy.badges.index', $course)
            ->with('success', 'Badge deleted.');
    }

    private function validateBadge(Request $request, ?AcademyBadge $existing = null): array
    {
        $ruleType = (string) $request->input('rule_type');
        $targetRequiredTypes = [
            'enrolled_n',
            'completed_n',
            'pass_score_at_least',
            'assessment_started_n',
            'assessment_submitted_n',
        ];
        $targetValueRules = in_array($ruleType, $targetRequiredTypes, true)
            ? ['required', 'integer', 'min:0', 'max:10000']
            : ['nullable', 'integer', 'min:0', 'max:10000'];

        $slugRule = ['required', 'string', 'max:100'];
        if ($existing) {
            $slugRule[] = 'unique:academy_badges,slug,' . $existing->id;
        } else {
            $slugRule[] = 'unique:academy_badges,slug';
        }

        return $request->validate([
            'slug' => $slugRule,
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:50'],
            'rule_type' => ['required', 'in:enrolled_n,completed_n,pass_score_at_least,assessment_started_n,assessment_submitted_n,membership_granted,certificate_issued,perfect_attempt'],
            'target_value' => $targetValueRules,
        ]);
    }
}

