<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRegister;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only admins can access the dashboard.');
        }

        $stats = [
            'courses' => Course::count(),
            'users' => User::count(),
            'enrollments' => Enrollment::count(),
            'attendance_records' => AttendanceRegister::count(),
        ];

        $recentEnrollments = Enrollment::with(['user', 'course'])
            ->latest('enrolled_at')
            ->take(10)
            ->get();

        $attendanceByCourse = Course::query()
            ->selectRaw('courses.id, courses.title, courses.slug, COUNT(attendance_registers.id) as attendance_count')
            ->leftJoin('attendance_registers', 'attendance_registers.course_id', '=', 'courses.id')
            ->groupBy('courses.id', 'courses.title', 'courses.slug')
            ->havingRaw('COUNT(attendance_registers.id) > 0')
            ->orderByDesc('attendance_count')
            ->get();

        return view('admin.dashboard', compact('stats', 'recentEnrollments', 'attendanceByCourse'));
    }
}
