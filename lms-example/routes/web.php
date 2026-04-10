<?php

use App\Http\Controllers\AdminAnalyticsController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseEvaluationController;
use App\Http\Controllers\FacilitatorDashboardController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\LearnController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if (auth()->user()->canEditCourses()) {
            return redirect()->route('instructor.dashboard');
        }
    }
    return redirect()->route('courses.index');
});
Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard')->middleware('auth');
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [\App\Http\Controllers\UserController::class, 'show'])->name('users.show');
    Route::put('/users/{user}/role', [\App\Http\Controllers\UserController::class, 'updateRole'])->name('users.update-role');
    Route::get('/badges', [\App\Http\Controllers\BadgeController::class, 'index'])->name('badges.index');
    Route::get('/badges/create', [\App\Http\Controllers\BadgeController::class, 'create'])->name('badges.create');
    Route::post('/badges', [\App\Http\Controllers\BadgeController::class, 'store'])->name('badges.store');
    Route::get('/badges/{badge}/edit', [\App\Http\Controllers\BadgeController::class, 'edit'])->name('badges.edit');
    Route::put('/badges/{badge}', [\App\Http\Controllers\BadgeController::class, 'update'])->name('badges.update');
    Route::get('/tags', [\App\Http\Controllers\TagController::class, 'index'])->name('tags.index');
    Route::get('/tags/create', [\App\Http\Controllers\TagController::class, 'create'])->name('tags.create');
    Route::post('/tags', [\App\Http\Controllers\TagController::class, 'store'])->name('tags.store');
    Route::get('/tags/{tag}/edit', [\App\Http\Controllers\TagController::class, 'edit'])->name('tags.edit');
    Route::put('/tags/{tag}', [\App\Http\Controllers\TagController::class, 'update'])->name('tags.update');
    Route::get('/instructor-requests', [\App\Http\Controllers\InstructorRequestController::class, 'index'])->name('instructor-requests.index');
    Route::post('/instructor-requests/{instructorRequest}/approve', [\App\Http\Controllers\InstructorRequestController::class, 'approve'])->name('instructor-requests.approve');
    Route::post('/instructor-requests/{instructorRequest}/reject', [\App\Http\Controllers\InstructorRequestController::class, 'reject'])->name('instructor-requests.reject');
    Route::get('/facilitator-ratings', [\App\Http\Controllers\FacilitatorRatingController::class, 'adminIndex'])->name('facilitator-ratings.index');
    Route::get('/certificate-signatures', [\App\Http\Controllers\CertificateSignaturesController::class, 'index'])->name('certificate-signatures.index');
    Route::get('/certificate-signatures/preview/{type}', [\App\Http\Controllers\CertificateSignaturesController::class, 'preview'])->name('certificate-signatures.preview')->where('type', 'board_of_faculty|supervisor');
    Route::post('/certificate-signatures', [\App\Http\Controllers\CertificateSignaturesController::class, 'store'])->name('certificate-signatures.store');
    Route::get('/certificate-templates', [\App\Http\Controllers\CertificateTemplatesController::class, 'index'])->name('certificate-templates.index');
    Route::post('/certificate-templates', [\App\Http\Controllers\CertificateTemplatesController::class, 'store'])->name('certificate-templates.store');
    Route::delete('/certificate-templates/{certificateTemplate}', [\App\Http\Controllers\CertificateTemplatesController::class, 'destroy'])->name('certificate-templates.destroy');
    Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/instructor', [FacilitatorDashboardController::class, 'index'])->name('instructor.dashboard');
    Route::get('/instructor/stats', [FacilitatorDashboardController::class, 'stats'])->name('instructor.stats');
    Route::get('/instructor/quiz-stats', [FacilitatorDashboardController::class, 'quizStats'])->name('instructor.quiz-stats');
    Route::get('/instructor/results', [FacilitatorDashboardController::class, 'results'])->name('instructor.results');
    Route::get('/instructor/results/export', [FacilitatorDashboardController::class, 'exportResults'])->name('instructor.results.export');
    Route::get('/instructor/courses/{course}/learners', [FacilitatorDashboardController::class, 'learners'])->name('instructor.course-learners');
    Route::get('/instructor/ratings', [\App\Http\Controllers\FacilitatorRatingController::class, 'index'])->name('instructor.ratings');
    Route::post('/instructor/request-course/{course}', [\App\Http\Controllers\InstructorRequestController::class, 'store'])->name('instructor.request-course');
    Route::get('/instructor/submissions', [\App\Http\Controllers\SubmissionsController::class, 'index'])->name('instructor.submissions.index');
    Route::get('/instructor/submissions/{submission}/grade', [\App\Http\Controllers\SubmissionsController::class, 'grade'])->name('instructor.submissions.grade');
    Route::put('/instructor/submissions/{submission}', [\App\Http\Controllers\SubmissionsController::class, 'update'])->name('instructor.submissions.update');
    Route::get('/instructor/courses/{course}/facilitator-chat', [\App\Http\Controllers\FacilitatorChatController::class, 'instructorPage'])->name('instructor.facilitator-chat');
    Route::get('/instructor/certificate-signature', [\App\Http\Controllers\CertificateSignaturesController::class, 'facilitatorForm'])->name('instructor.certificate-signature');
    Route::post('/instructor/certificate-signature', [\App\Http\Controllers\CertificateSignaturesController::class, 'storeFacilitator'])->name('instructor.certificate-signature.store');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::get('/email/verify', [AuthController::class, 'verifyNotice'])->name('verification.notice')->middleware('auth');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify')->middleware(['throttle:6,1']);
Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])->name('verification.send')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read-and-go', [\App\Http\Controllers\NotificationController::class, 'readAndGo'])->name('notifications.read-and-go');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
});

Route::get('/my-learning', [\App\Http\Controllers\LearnerController::class, 'dashboard'])->name('learner.dashboard')->middleware('auth');
Route::get('/help', [\App\Http\Controllers\HelpController::class, 'index'])->name('help.index');
Route::get('/help/admin', [\App\Http\Controllers\HelpController::class, 'admin'])->name('help.admin')->middleware('auth');
Route::get('/help/facilitator', [\App\Http\Controllers\HelpController::class, 'facilitator'])->name('help.facilitator')->middleware('auth');
Route::get('/leaderboard', [\App\Http\Controllers\LeaderboardController::class, 'index'])->name('leaderboard.index');
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/my-courses', [CourseController::class, 'myCourses'])->name('courses.my')->middleware('auth');
Route::get('/courses/instructor', [CourseController::class, 'instructorCourses'])->name('courses.instructor')->middleware('auth');
Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create')->middleware('auth');
Route::post('/courses', [CourseController::class, 'store'])->name('courses.store')->middleware('auth');
Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit')->middleware('auth');
Route::get('/courses/{course}/attendance', [CourseController::class, 'attendance'])->name('courses.attendance')->middleware('auth');
Route::get('/courses/{course}/attendance/export', [CourseController::class, 'exportAttendance'])->name('courses.attendance.export')->middleware('auth');
Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update')->middleware('auth');
Route::post('/courses/{course}/duplicate', [CourseController::class, 'duplicate'])->name('courses.duplicate')->middleware('auth');
Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy')->middleware('auth');
Route::get('/courses/{course}/units/{unit}/edit', [UnitController::class, 'edit'])->name('units.edit')->middleware('auth');
Route::put('/courses/{course}/units/{unit}', [UnitController::class, 'update'])->name('units.update')->middleware('auth');
Route::post('/courses/{course}/units/{unit}/refresh-from-file', [UnitController::class, 'refreshFromFile'])->name('units.refresh-from-file')->middleware('auth');
Route::get('/courses/{course}/units/{unit}/quiz', [UnitController::class, 'editQuiz'])->name('units.quiz.edit')->middleware('auth');
Route::put('/courses/{course}/units/{unit}/quiz', [UnitController::class, 'updateQuiz'])->name('units.quiz.update')->middleware('auth');
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
Route::post('/courses/{course}/enroll', [CourseController::class, 'enroll'])->name('courses.enroll')->middleware('auth');
Route::get('/courses/{course}/enroll-bulk', [CourseController::class, 'enrollBulkForm'])->name('courses.enroll-bulk')->middleware('auth');
Route::post('/courses/{course}/enroll-bulk', [CourseController::class, 'enrollBulkStore'])->name('courses.enroll-bulk.store')->middleware('auth');
Route::post('/courses/{course}/reviews', [CourseController::class, 'storeReview'])->name('courses.reviews.store')->middleware('auth');
Route::post('/courses/{course}/rate-facilitator', [CourseController::class, 'storeFacilitatorRating'])->name('courses.rate-facilitator')->middleware('auth');
Route::post('/courses/{course}/evaluate', [CourseEvaluationController::class, 'store'])->name('courses.evaluate')->middleware('auth');

Route::get('/certificates/preview-pdf', [\App\Http\Controllers\CertificateController::class, 'previewPdf'])->name('certificates.preview-pdf')->middleware('auth');
Route::get('/certificates/{certificate}', [\App\Http\Controllers\CertificateController::class, 'show'])->name('certificates.show')->middleware('auth');
Route::get('/certificates/{certificate}/pdf', [\App\Http\Controllers\CertificateController::class, 'downloadPdf'])->name('certificates.download-pdf')->middleware('auth');
Route::get('/learn/{course}', [LearnController::class, 'show'])->name('learn.show')->middleware('auth');
Route::post('/learn/{course}/attendance', [LearnController::class, 'storeAttendance'])->name('learn.attendance.store')->middleware('auth');
Route::post('/learn/{course}/unit/{unit}/complete', [LearnController::class, 'completeUnit'])->name('learn.unit.complete')->middleware('auth');
Route::post('/learn/{course}/quiz/{unit}', [LearnController::class, 'submitQuiz'])->name('learn.quiz.submit')->middleware('auth');
Route::post('/learn/{course}/assignment/{unit}/submit', [LearnController::class, 'submitAssignment'])->name('learn.assignment.submit')->middleware('auth');
Route::post('/learn/{course}/unit/{unit}/notes', [NoteController::class, 'store'])->name('learn.notes.store')->middleware('auth');

Route::get('/my-notes', [NoteController::class, 'index'])->name('notes.index')->middleware('auth');

Route::get('/learn/{course}/facilitator-chat', [\App\Http\Controllers\FacilitatorChatController::class, 'index'])->name('learn.facilitator-chat.index')->middleware('auth');
Route::post('/learn/{course}/facilitator-chat', [\App\Http\Controllers\FacilitatorChatController::class, 'store'])->name('learn.facilitator-chat.store')->middleware('auth');
Route::patch('/learn/{course}/facilitator-chat/{message}', [\App\Http\Controllers\FacilitatorChatController::class, 'update'])->name('learn.facilitator-chat.update')->middleware('auth');
