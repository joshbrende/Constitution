<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\InstructorRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class InstructorRequestController extends Controller
{
    /**
     * Facilitator: request to instruct a course (instructor_id must be null).
     */
    public function store(Request $request, Course $course)
    {
        $user = Auth::user();
        if (!$user->canEditCourses() || $user->isAdmin()) {
            abort(403, 'Only facilitators can request to instruct. Admins assign directly.');
        }
        if ($course->instructor_id !== null) {
            return redirect()->route('instructor.dashboard')
                ->with('message', 'That course already has a facilitator assigned.');
        }
        $exists = InstructorRequest::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();
        if ($exists) {
            return redirect()->route('instructor.dashboard')
                ->with('message', 'You already have a pending request for this course.');
        }
        InstructorRequest::create([
            'course_id' => $course->id,
            'user_id'   => $user->id,
            'status'    => 'pending',
        ]);
        return redirect()->route('instructor.dashboard')
            ->with('message', 'Request to facilitate "' . $course->title . '" sent. The admin will review it.');
    }

    /**
     * Admin: list instructor requests (pending first).
     */
    public function index()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        $requests = InstructorRequest::with(['course', 'user', 'decidedBy'])
            ->latest()
            ->paginate(20);
        $pendingCount = InstructorRequest::where('status', 'pending')->count();
        return view('admin.instructor-requests', compact('requests', 'pendingCount'));
    }

    /**
     * Admin: approve a request → set course.instructor_id and reject other pendings for same course.
     */
    public function approve(InstructorRequest $instructorRequest)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        if (!$instructorRequest->isPending()) {
            return redirect()->route('admin.instructor-requests.index')
                ->with('message', 'That request has already been processed.');
        }
        $course = $instructorRequest->course;
        if ($course->instructor_id !== null) {
            $instructorRequest->update([
                'status'     => 'rejected',
                'decided_at' => now(),
                'decided_by' => Auth::id(),
                'admin_notes'=> 'Course already has a facilitator assigned.',
            ]);
            return redirect()->route('admin.instructor-requests.index')
                ->with('message', 'The course already has a facilitator. This request was marked rejected.');
        }
        DB::transaction(function () use ($instructorRequest, $course) {
            $course->update(['instructor_id' => $instructorRequest->user_id]);
            $instructorRequest->update([
                'status'     => 'approved',
                'decided_at' => now(),
                'decided_by' => Auth::id(),
            ]);
            InstructorRequest::where('course_id', $course->id)
                ->where('id', '!=', $instructorRequest->id)
                ->where('status', 'pending')
                ->update([
                    'status'     => 'rejected',
                    'decided_at' => now(),
                    'decided_by' => Auth::id(),
                    'admin_notes'=> 'Another facilitator was approved for this course.',
                ]);
        });
        return redirect()->route('admin.instructor-requests.index')
            ->with('message', 'Request approved. ' . $instructorRequest->user->name . ' can now access the course.');
    }

    /**
     * Admin: reject a request.
     */
    public function reject(Request $request, InstructorRequest $instructorRequest)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        if (!$instructorRequest->isPending()) {
            return redirect()->route('admin.instructor-requests.index')
                ->with('message', 'That request has already been processed.');
        }
        $instructorRequest->update([
            'status'     => 'rejected',
            'decided_at' => now(),
            'decided_by' => Auth::id(),
            'admin_notes'=> $request->input('admin_notes'),
        ]);
        return redirect()->route('admin.instructor-requests.index')
            ->with('message', 'Request rejected.');
    }
}
