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
    Route::get('constitution/official/amendment3', [ConstitutionOfficialController::class, 'amendment3']);

    Route::post('auth/register', [AuthController::class, 'register'])->middleware('throttle:auth-register');
    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:auth-login');
    Route::post('auth/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:3,60');
    Route::post('auth/refresh', [AuthController::class, 'refresh'])->middleware('throttle:10,60');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);

        Route::middleware('abilities:profile:read')->group(function () {
            Route::get('profile', [ApiProfileController::class, 'show']);
            Route::get('provinces', [ApiProvinceController::class, 'index']);
        });

        Route::middleware('abilities:profile:write')->group(function () {
            Route::put('profile', [ApiProfileController::class, 'update']);
            Route::delete('profile', [ApiProfileController::class, 'destroy']);
        });

        Route::middleware('abilities:academy:read')->group(function () {
            Route::get('academy/courses', [AcademyCourseController::class, 'index']);
            Route::get('academy/courses/membership', [AcademyCourseController::class, 'membershipCourse']);
            Route::get('academy/summary', [AcademyCourseController::class, 'summary']);
            Route::get('academy/courses/{course}', [AcademyCourseController::class, 'show']);
            Route::get('academy/courses/{course}/enrolment', [AcademyCourseController::class, 'enrolment']);
            Route::get('academy/assessments/{assessment}', [AcademyAssessmentController::class, 'assessment']);
            Route::get('academy/badges', [AcademyAchievementsController::class, 'index']);
        });

        Route::middleware('abilities:academy:write')->group(function () {
            Route::post('academy/courses/{course}/enrol', [AcademyCourseController::class, 'enrol']);
            Route::post('academy/assessments/{assessment}/attempts', [AcademyAssessmentController::class, 'startAttempt'])
                ->middleware('throttle:assessments');
            Route::post('academy/attempts/{attempt}/submit', [AcademyAssessmentController::class, 'submitAttempt'])
                ->middleware('throttle:assessments');
        });

        Route::middleware('abilities:certificates:read')->group(function () {
            Route::get('certificates/preview', [CertificateController::class, 'preview']);
            Route::get('certificates', [CertificateController::class, 'index']);
            Route::get('certificates/{certificate}/pdf', [CertificateController::class, 'download'])
                ->middleware('throttle:certificates');
        });

        Route::middleware('abilities:certificates:write')->group(function () {
            Route::post('certificates/{certificate}/generate', [CertificateController::class, 'generate'])
                ->middleware('throttle:certificates');
        });

        Route::middleware('abilities:dialogue:read')->group(function () {
            Route::get('dialogue/channels', [DialogueController::class, 'channels']);
            Route::get('dialogue/channels/{channel}/threads', [DialogueController::class, 'threads']);
            Route::get('dialogue/threads/{thread}/messages', [DialogueController::class, 'messages']);
        });

        Route::middleware('abilities:dialogue:write')->group(function () {
            Route::post('dialogue/channels/{channel}/threads', [DialogueController::class, 'storeThread']);
            Route::post('dialogue/threads/{thread}/messages', [DialogueController::class, 'storeMessage']);
            Route::post('dialogue/messages/{message}/report', [DialogueController::class, 'reportMessage']);
            Route::post('dialogue/threads/{thread}/report', [DialogueController::class, 'reportThread']);
            Route::post('users/{userId}/block', [DialogueController::class, 'blockUser']);
            Route::delete('users/{userId}/block', [DialogueController::class, 'unblockUser']);
        });

        Route::middleware('abilities:projects:read')->group(function () {
            Route::get('priority-projects', [PriorityProjectsController::class, 'index']);
        });

        Route::middleware('abilities:projects:write')->group(function () {
            Route::post('priority-projects/{priority_project}/like', [PriorityProjectsController::class, 'like']);
        });
    });

    Route::get('library/categories', [LibraryController::class, 'categories']);
    Route::get('library/documents', [LibraryController::class, 'index']);
    Route::get('library/documents/{document}', [LibraryController::class, 'show']);

    Route::get('party-organs', [PartyOrgansController::class, 'index']);
    Route::get('party-organs/{party_organ}', [PartyOrgansController::class, 'show']);

    Route::get('presidium', [PresidiumController::class, 'index']);

    Route::get('party/profile', [ApiPartyController::class, 'profile']);

    Route::get('home-banners', [ApiHomeBannersController::class, 'index']);

    Route::get('app-config', [AppConfigController::class, 'show']);

    Route::get('health', [HealthController::class, 'show']);

    Route::get('pages/{slug}', [ApiStaticPagesController::class, 'show']);

    Route::get('parts', [PartController::class, 'index']);
    Route::get('chapters', [ChapterController::class, 'index']);
    Route::get('chapters/{chapter}', [ChapterController::class, 'show']);
    Route::get('sections/search', [SectionController::class, 'search']);
    Route::get('sections/{section}', [SectionController::class, 'show']);
    Route::get('sections/{section}/comments', [CommentController::class, 'index']);

    Route::post('sections/{section}/comments', [CommentController::class, 'store'])
        ->middleware(['auth:sanctum', 'abilities:comments:write']);
});
