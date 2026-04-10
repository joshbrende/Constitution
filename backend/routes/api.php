<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AcademyAssessmentController;
use App\Http\Controllers\Api\AcademyCourseController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\LibraryController;
use App\Http\Controllers\Api\PresidiumController;
use App\Http\Controllers\Api\PartyOrgansController;
use App\Http\Controllers\Api\PartyController as ApiPartyController;
use App\Http\Controllers\Api\DialogueController;
use App\Http\Controllers\Api\PriorityProjectsController;
use App\Http\Controllers\Api\AcademyAchievementsController;
use App\Http\Controllers\Api\HomeBannersController as ApiHomeBannersController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\StaticPagesController as ApiStaticPagesController;
use App\Http\Controllers\Api\ProfileController as ApiProfileController;
use App\Http\Controllers\Api\ProvinceController as ApiProvinceController;
use App\Http\Controllers\Api\ConstitutionOfficialController;
use App\Http\Controllers\Api\AppConfigController;

Route::prefix('v1')->group(function () {
    // Authentication
    // Official constitution documents (e.g. gazetted PDF for Amendment Bill — used by mobile)
    Route::get('constitution/official/amendment3', [ConstitutionOfficialController::class, 'amendment3']);

    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
        Route::post('auth/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:3,60');
    Route::post('auth/refresh', [AuthController::class, 'refresh'])->middleware('throttle:10,60');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);

        // Profile
        Route::get('profile', [ApiProfileController::class, 'show']);
        Route::put('profile', [ApiProfileController::class, 'update']);
        Route::delete('profile', [ApiProfileController::class, 'destroy']);

        // Provinces (for profile picker)
        Route::get('provinces', [ApiProvinceController::class, 'index']);

        // Academy
        Route::get('academy/courses', [AcademyCourseController::class, 'index']);
        Route::get('academy/courses/membership', [AcademyCourseController::class, 'membershipCourse']);
        Route::get('academy/summary', [AcademyCourseController::class, 'summary']);
        Route::get('academy/courses/{course}', [AcademyCourseController::class, 'show']);
        Route::post('academy/courses/{course}/enrol', [AcademyCourseController::class, 'enrol']);
        Route::get('academy/courses/{course}/enrolment', [AcademyCourseController::class, 'enrolment']);
        Route::get('academy/assessments/{assessment}', [AcademyAssessmentController::class, 'assessment']);
        Route::post('academy/assessments/{assessment}/attempts', [AcademyAssessmentController::class, 'startAttempt'])
            ->middleware('throttle:assessments');
        Route::post('academy/attempts/{attempt}/submit', [AcademyAssessmentController::class, 'submitAttempt'])
            ->middleware('throttle:assessments');

        // Academy Achievements (badges + locked/unlocked + progress)
        Route::get('academy/badges', [AcademyAchievementsController::class, 'index']);

        // Certificates (generate is rate-limited; download returns 202 until PDF ready)
        Route::get('certificates/preview', [CertificateController::class, 'preview']);
        Route::get('certificates', [CertificateController::class, 'index']);
        Route::post('certificates/{certificate}/generate', [CertificateController::class, 'generate'])
            ->middleware('throttle:certificates');
        Route::get('certificates/{certificate}/pdf', [CertificateController::class, 'download'])
            ->middleware('throttle:certificates');

        // Dialogue (channels and threads – authenticated)
        Route::get('dialogue/channels', [DialogueController::class, 'channels']);
        Route::get('dialogue/channels/{channel}/threads', [DialogueController::class, 'threads']);
        Route::post('dialogue/channels/{channel}/threads', [DialogueController::class, 'storeThread']);
        Route::get('dialogue/threads/{thread}/messages', [DialogueController::class, 'messages']);
        Route::post('dialogue/threads/{thread}/messages', [DialogueController::class, 'storeMessage']);
        Route::post('dialogue/messages/{message}/report', [DialogueController::class, 'reportMessage']);
        Route::post('dialogue/threads/{thread}/report', [DialogueController::class, 'reportThread']);
        Route::post('users/{userId}/block', [DialogueController::class, 'blockUser']);
        Route::delete('users/{userId}/block', [DialogueController::class, 'unblockUser']);

        // Priority projects (members can view and like)
        Route::get('priority-projects', [PriorityProjectsController::class, 'index']);
        Route::post('priority-projects/{priority_project}/like', [PriorityProjectsController::class, 'like']);
    });

    // Digital Library (categories public; documents filtered by access)
    Route::get('library/categories', [LibraryController::class, 'categories']);
    Route::get('library/documents', [LibraryController::class, 'index']);
    Route::get('library/documents/{document}', [LibraryController::class, 'show']);

    // Party Organs (public, published only)
    Route::get('party-organs', [PartyOrgansController::class, 'index']);
    Route::get('party-organs/{party_organ}', [PartyOrgansController::class, 'show']);

    // Presidium (public, published only)
    Route::get('presidium', [PresidiumController::class, 'index']);

    // Party (public profile)
    Route::get('party/profile', [ApiPartyController::class, 'profile']);

    // Home banners (public, used on mobile overview)
    Route::get('home-banners', [ApiHomeBannersController::class, 'index']);

    // Mobile/web app config (public, DB-backed)
    Route::get('app-config', [AppConfigController::class, 'show']);

    // Health check (used for uptime monitoring)
    Route::get('health', [HealthController::class, 'show']);

    // Static content pages (help, terms, privacy)
    Route::get('pages/{slug}', [ApiStaticPagesController::class, 'show']);

    // Constitution content
    Route::get('parts', [PartController::class, 'index']);
    Route::get('chapters', [ChapterController::class, 'index']);
    Route::get('chapters/{chapter}', [ChapterController::class, 'show']);
    Route::get('sections/search', [SectionController::class, 'search']);
    Route::get('sections/{section}', [SectionController::class, 'show']);
    Route::get('sections/{section}/comments', [CommentController::class, 'index']);
    Route::post('sections/{section}/comments', [CommentController::class, 'store'])
        ->middleware('auth:sanctum');
});

