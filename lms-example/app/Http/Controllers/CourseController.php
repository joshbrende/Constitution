<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AttendanceRegister;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\CourseEvaluation;
use App\Models\Enrollment;
use App\Models\FacilitatorRating;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\CertificateTemplate;
use App\Models\Tag;
use App\Models\Unit;
use App\Services\GamificationService;
use App\Helpers\CacheHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class CourseController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::check() && Auth::user()->isFacilitator()) {
            return redirect()->route('instructor.dashboard');
        }

        $query = Course::query()->where('status', 'published')->with(['instructor', 'tags']);

        $search = trim((string) $request->get('q', ''));
        if ($search !== '') {
            $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search) . '%';
            $query->where(function ($q) use ($like) {
                $q->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('short_description', 'like', $like);
            });
        }

        $tagSlug = trim((string) $request->get('tag', ''));
        if ($tagSlug !== '') {
            $query->whereHas('tags', fn ($q) => $q->where('tags.slug', $tagSlug));
        }

        $order = $request->get('order', 'newest');
        match ($order) {
            'alphabetical' => $query->orderBy('title'),
            'popular' => $query->orderByDesc('enrollment_count'),
            'newest' => $query->orderByDesc('created_at'),
            default => $query->orderByDesc('created_at'),
        };

        $courses = $query->paginate(12)->withQueryString();

        $enrolledCourseIds = collect();
        if (Auth::check()) {
            $enrolledCourseIds = Enrollment::where('user_id', Auth::id())
                ->pluck('course_id');
        }

        $tags = CacheHelper::getTags();

        return view('courses.index', compact('courses', 'enrolledCourseIds', 'tags'));
    }

    public function myCourses()
    {
        $user = Auth::user();
        $enrollments = Enrollment::where('user_id', $user->id)
            ->with('course.instructor')
            ->latest('enrolled_at')
            ->paginate(12);
        return view('courses.my-courses', compact('enrollments'));
    }

    public function instructorCourses()
    {
        $user = Auth::user();
        if (!$user->canEditCourses()) {
            abort(403, 'Only facilitators and admins can access the instructing dashboard.');
        }
        $canEdit = true;
        $courses = $user->isAdmin()
            ? Course::with('instructor')->latest()->paginate(12)
            : Course::where('instructor_id', $user->id)->with('instructor')->latest()->paginate(12);
        return view('courses.instructor-courses', compact('courses', 'canEdit'));
    }

    public function create()
    {
        if (!Auth::user()->canEditCourses()) {
            abort(403, 'Only facilitators and admins can create or edit courses.');
        }
        $tags = CacheHelper::getTags();
        return view('courses.create', compact('tags'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->canEditCourses()) {
            abort(403, 'Only facilitators and admins can create or edit courses.');
        }
        $valid = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:courses,slug',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:tags,id',
        ]);
        $valid['slug'] = $valid['slug'] ?: \Illuminate\Support\Str::slug($valid['title']);
        $valid['enrollment_count'] = 0;
        if (Auth::user()->isAdmin() && $request->has('instructor_id')) {
            $valid['instructor_id'] = $request->input('instructor_id') ?: null;
        } else {
            $valid['instructor_id'] = Auth::id();
        }
        $course = Course::create($valid);
        $course->tags()->sync($request->input('tags', []));
        return redirect()->route('courses.instructor')->with('message', 'Course created.');
    }

    public function edit(Course $course)
    {
        if (!Auth::user()->canEditCourses()) {
            abort(403, 'Only facilitators and admins can create or edit courses.');
        }
        if (!Auth::user()->canEditCourse($course)) {
            abort(403, 'You can only edit courses you are instructing.');
        }
        $course->load('units', 'tags', 'certificateTemplate');
        $tags = CacheHelper::getTags();
        $certificateTemplates = CertificateTemplate::orderBy('name')->get();
        return view('courses.edit', compact('course', 'tags', 'certificateTemplates'));
    }

    public function update(Request $request, Course $course)
    {
        if (!Auth::user()->canEditCourses()) {
            abort(403, 'Only facilitators and admins can create or edit courses.');
        }
        if (!Auth::user()->canEditCourse($course)) {
            abort(403, 'You can only edit courses you are instructing.');
        }
        $valid = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:courses,slug,' . $course->id,
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'certificate_template_id' => 'nullable|integer|exists:certificate_templates,id',
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:tags,id',
        ]);
        $valid['slug'] = $valid['slug'] ?: \Illuminate\Support\Str::slug($valid['title']);
        if (Auth::user()->isAdmin() && $request->has('instructor_id')) {
            $valid['instructor_id'] = $request->input('instructor_id') ?: null;
        }
        $course->update($valid);
        $course->tags()->sync($request->input('tags', []));
        return redirect()->route('courses.show', $course)->with('message', 'Course updated.');
    }

    public function destroy(Course $course)
    {
        if (!Auth::user()->canEditCourses()) {
            abort(403, 'Only facilitators and admins can create or edit courses.');
        }
        if (!Auth::user()->canEditCourse($course)) {
            abort(403, 'You can only edit courses you are instructing.');
        }
        $course->delete();
        return redirect()->route('courses.instructor')->with('message', 'Course deleted.');
    }

    /**
     * Duplicate a course: new draft with same title (Copy), slug, tags; copies units, quizzes (and questions), assignments.
     * New course instructor_id = current user. prerequisite_unit_id on units is cleared.
     */
    public function duplicate(Course $course)
    {
        if (!Auth::user()->canEditCourses()) {
            abort(403, 'Only facilitators and admins can duplicate courses.');
        }
        if (!Auth::user()->isAdmin() && !Auth::user()->canEditCourse($course)) {
            abort(403, 'You can only duplicate courses you instruct.');
        }

        $course->load(['units' => fn ($q) => $q->orderBy('order')]);
        foreach ($course->units as $u) {
            $u->load(['quiz.questions', 'assignment']);
        }

        $new = DB::transaction(function () use ($course) {
            $baseSlug = Str::slug($course->title);
            $slug = $baseSlug . '-copy-' . Str::random(6);
            $n = 0;
            while (Course::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-copy-' . Str::random(6);
                if (++$n > 10) {
                    $slug = $baseSlug . '-copy-' . time();
                    break;
                }
            }

            $newCourse = Course::create([
                'title' => $course->title . ' (Copy)',
                'slug' => $slug,
                'short_description' => $course->short_description,
                'description' => $course->description,
                'status' => 'draft',
                'instructor_id' => Auth::id(),
                'enrollment_count' => 0,
                'rating' => null,
                'rating_count' => 0,
                'featured_image' => $course->featured_image,
                'video_preview' => $course->video_preview,
            ]);
            $newCourse->tags()->sync($course->tags->pluck('id'));

            foreach ($course->units as $u) {
                $newAssignment = null;
                if ($u->assignment_id && $u->assignment) {
                    $a = $u->assignment;
                    $newAssignment = Assignment::create([
                        'course_id' => $newCourse->id,
                        'title' => $a->title,
                        'description' => $a->description,
                        'instructions' => $a->instructions,
                        'duration' => $a->duration,
                        'due_date' => $a->due_date,
                        'max_points' => $a->max_points ?? 100,
                        'allow_file_upload' => $a->allow_file_upload ?? true,
                        'allowed_file_types' => $a->allowed_file_types,
                        'max_file_size' => $a->max_file_size,
                        'assessment_type' => $a->assessment_type,
                    ]);
                }

                $newQuiz = null;
                if ($u->quiz_id && $u->quiz) {
                    $q = $u->quiz;
                    $newQuiz = Quiz::create([
                        'course_id' => $newCourse->id,
                        'title' => $q->title,
                        'slug' => Str::slug($q->title) . '-' . Str::random(6),
                        'description' => $q->description,
                        'instructions' => $q->instructions,
                        'duration' => $q->duration,
                        'pass_percentage' => $q->pass_percentage ?? 70,
                        'max_attempts' => $q->max_attempts ?? 5,
                        'randomize_questions' => $q->randomize_questions ?? false,
                        'show_results' => $q->show_results ?? true,
                        'show_correct_answers' => $q->show_correct_answers ?? true,
                        'total_points' => $q->total_points ?? 0,
                        'grading_type' => $q->grading_type ?? 'auto',
                        'assessment_type' => $q->assessment_type ?? 'summative',
                    ]);
                    foreach ($q->questions ?? [] as $qq) {
                        Question::create([
                            'quiz_id' => $newQuiz->id,
                            'question' => $qq->question,
                            'type' => $qq->type ?? 'multiple_choice',
                            'options' => $qq->options,
                            'correct_answers' => $qq->correct_answers,
                            'points' => $qq->points ?? 1,
                            'order' => $qq->order ?? 0,
                            'explanation' => $qq->explanation ?? null,
                        ]);
                    }
                }

                Unit::create([
                    'course_id' => $newCourse->id,
                    'title' => $u->title,
                    'slug' => Str::slug($u->title) . '-' . Str::random(6),
                    'content' => $u->content,
                    'description' => $u->description,
                    'order' => (int) $u->order,
                    'unit_type' => $u->unit_type,
                    'video_url' => $u->video_url,
                    'audio_url' => $u->audio_url,
                    'document_url' => $u->document_url,
                    'duration' => $u->duration,
                    'is_free' => $u->is_free ?? false,
                    'is_draft' => $u->is_draft ?? false,
                    'prerequisite_unit_id' => null,
                    'quiz_id' => $newQuiz?->id,
                    'assignment_id' => $newAssignment?->id,
                ]);
            }

            return $newCourse;
        });

        return redirect()->route('courses.edit', $new)->with('message', 'Course duplicated. You can now edit the copy.');
    }

    /**
     * Show form to bulk enroll users via CSV (one email per row; optional header "email").
     */
    public function enrollBulkForm(Course $course)
    {
        if (!Auth::user()->canEditCourses() || !Auth::user()->canEditCourse($course)) {
            abort(403, 'Only the facilitator or an admin can bulk-enroll.');
        }
        return view('courses.enroll-bulk', compact('course'));
    }

    /**
     * Process CSV: enroll users by email. Report enrolled, skipped (already enrolled), not_found.
     */
    public function enrollBulkStore(Request $request, Course $course)
    {
        if (!Auth::user()->canEditCourses() || !Auth::user()->canEditCourse($course)) {
            abort(403, 'Only the facilitator or an admin can bulk-enroll.');
        }
        $request->validate([
            'csv' => 'required|file|max:512',
        ]);

        $content = $request->file('csv')->get();
        $lines = preg_split('/\r\n|\r|\n/', $content);
        $emails = [];
        foreach ($lines as $i => $line) {
            $cols = str_getcsv($line);
            $e = trim((string) ($cols[0] ?? ''));
            if ($e === '') {
                continue;
            }
            if ($i === 0 && preg_match('/^email$/i', $e)) {
                continue;
            }
            if (filter_var($e, FILTER_VALIDATE_EMAIL)) {
                $emails[] = strtolower($e);
            }
        }
        $emails = array_values(array_unique($emails));

        $enrolled = 0;
        $skipped = 0;
        $notFound = 0;
        $svc = app(GamificationService::class);

        foreach ($emails as $email) {
            $user = \App\Models\User::where('email', $email)->first();
            if (!$user) {
                $notFound++;
                continue;
            }
            $e = Enrollment::firstOrCreate(
                ['user_id' => $user->id, 'course_id' => $course->id],
                [
                    'status' => 'active',
                    'progress_status' => 'not_started',
                    'progress_percentage' => 0,
                    'enrolled_at' => now(),
                ]
            );
            if ($e->wasRecentlyCreated) {
                $course->increment('enrollment_count');
                $svc->awardPoints($user, 10);
                $svc->ensureBadge($user, 'first-steps');
                $enrolled++;
            } else {
                $skipped++;
            }
        }

        $msg = "Bulk enroll: {$enrolled} enrolled, {$skipped} already enrolled, {$notFound} email(s) not found.";
        return redirect()->route('courses.enroll-bulk', $course)->with('message', $msg);
    }

    public function attendance(Course $course)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only admins can view the attendance register.');
        }
        $rows = AttendanceRegister::where('course_id', $course->id)
            ->with(['user', 'unit'])
            ->orderBy('created_at')
            ->get();
        return view('courses.attendance', compact('course', 'rows'));
    }

    public function exportAttendance(Course $course)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only admins can export the attendance register.');
        }

        $rows = AttendanceRegister::where('course_id', $course->id)
            ->orderBy('created_at')
            ->get();

        $filename = 'attendance-' . \Illuminate\Support\Str::slug($course->title) . '-' . now()->format('Y-m-d') . '.csv';
        $headers = ['#', 'Title', 'Name', 'Surname', 'Designation', 'Organisation', 'Contact number', 'Email', 'Registered at'];

        return response()->streamDownload(
            function () use ($rows, $headers) {
                $out = fopen('php://output', 'w');
                fprintf($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel
                fputcsv($out, $headers);
                foreach ($rows as $i => $r) {
                    fputcsv($out, [
                        $i + 1,
                        $r->title ?? '',
                        $r->name ?? '',
                        $r->surname ?? '',
                        $r->designation ?? '',
                        $r->organisation ?? '',
                        $r->contact_number ?? '',
                        $r->email ?? '',
                        $r->created_at?->format('Y-m-d H:i') ?? '',
                    ]);
                }
                fclose($out);
            },
            $filename,
            ['Content-Type' => 'text/csv; charset=UTF-8']
        );
    }

    public function show(Course $course)
    {
        $canEdit = Auth::check() && Auth::user()->canEditCourses();
        if ($course->status !== 'published' && !$canEdit) {
            abort(404);
        }
        $course->load(['instructor', 'units', 'tags']);
        
        $userId = Auth::id();
        $enrollment = $userId ? Enrollment::where('user_id', $userId)->where('course_id', $course->id)->first() : null;
        $enrolled = (bool) $enrollment;
        
        // Eager load related data in single queries
        $certificate = null;
        $myReview = null;
        $facilitatorRating = null;
        $courseEvaluation = null;
        
        if ($enrolled && $enrollment) {
            if ($enrollment->progress_percentage >= 100) {
                $certificate = Certificate::where('user_id', $userId)->where('course_id', $course->id)->first();
                $courseEvaluation = CourseEvaluation::where('course_id', $course->id)->where('user_id', $userId)->first();
            }
            if ($userId) {
                $myReview = CourseReview::where('course_id', $course->id)->where('user_id', $userId)->first();
            }
            if ($course->instructor) {
                $facilitatorRating = FacilitatorRating::where('enrollment_id', $enrollment->id)->first();
            }
        }
        
        $hasRated = (bool) $facilitatorRating;
        $reviews = $course->reviews()->with('user')->take(20)->get();

        $canEditThisCourse = Auth::check() && Auth::user()->canEditCourse($course);
        return view('courses.show', compact(
            'course',
            'enrolled',
            'enrollment',
            'canEdit',
            'canEditThisCourse',
            'certificate',
            'reviews',
            'myReview',
            'hasRated',
            'facilitatorRating',
            'courseEvaluation'
        ));
    }

    public function storeReview(Request $request, Course $course)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('intended', route('courses.show', $course));
        }
        $enrolled = Enrollment::where('user_id', Auth::id())->where('course_id', $course->id)->exists();
        if (!$enrolled) {
            return redirect()->route('courses.show', $course)->with('message', 'You must be enrolled to review this course.');
        }
        $valid = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:2000',
        ]);
        $r = CourseReview::updateOrCreate(
            ['course_id' => $course->id, 'user_id' => Auth::id()],
            [
                'rating' => $valid['rating'],
                'review' => $valid['review'] ?? null,
                'is_approved' => true,
            ]
        );
        $this->updateCourseRating($course);
        return redirect()->route('courses.show', $course)->with('message', 'Thank you for your review.');
    }

    public function storeFacilitatorRating(Request $request, Course $course)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('intended', route('courses.show', $course));
        }
        $enrollment = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->first();
        if (!$enrollment) {
            return redirect()->route('courses.show', $course)->with('message', 'You must be enrolled to rate the facilitator.');
        }
        if ((int) $enrollment->progress_percentage < 100) {
            return redirect()->route('courses.show', $course)->with('message', 'Complete the course before rating the facilitator.');
        }
        if (!$course->instructor_id) {
            return redirect()->route('courses.show', $course)->with('message', 'This course has no facilitator to rate.');
        }
        if (FacilitatorRating::where('enrollment_id', $enrollment->id)->exists()) {
            return redirect()->route('courses.show', $course)->with('message', 'You have already rated the facilitator for this course.');
        }
        $valid = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:2000',
        ]);
        FacilitatorRating::create([
            'enrollment_id'  => $enrollment->id,
            'instructor_id'  => $course->instructor_id,
            'rating'         => $valid['rating'],
            'review'         => $valid['review'] ?? null,
        ]);
        return redirect()->route('courses.show', $course)->with('message', 'Thank you for rating your facilitator.');
    }

    private function updateCourseRating(Course $course): void
    {
        $agg = CourseReview::where('course_id', $course->id)->where('is_approved', true)
            ->selectRaw('avg(rating) as avg_rating, count(*) as cnt')->first();
        $course->update([
            'rating' => round((float) ($agg->avg_rating ?? 0), 2),
            'rating_count' => (int) ($agg->cnt ?? 0),
        ]);
    }

    public function enroll(Course $course)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('intended', route('courses.show', $course));
        }
        $e = Enrollment::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            [
                'status' => 'active',
                'progress_status' => 'not_started',
                'progress_percentage' => 0,
                'enrolled_at' => now(),
            ]
        );
        if ($e->wasRecentlyCreated) {
            $course->increment('enrollment_count');
            $svc = app(GamificationService::class);
            $svc->awardPoints($user, 10);
            $svc->ensureBadge($user, 'first-steps');
        }
        return redirect()->route('learn.show', [$course, 'start' => 1]);
    }
}
