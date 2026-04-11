<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\WebConstitutionController;
use App\Http\Controllers\WebPartyController;
use App\Http\Controllers\Admin\ConstitutionController as AdminConstitutionController;
use App\Http\Controllers\Admin\AcademyController as AdminAcademyController;
use App\Http\Controllers\Admin\LibraryController as AdminLibraryController;
use App\Http\Controllers\Admin\PartyController as AdminPartyController;
use App\Http\Controllers\Admin\MembersController as AdminMembersController;
use App\Http\Controllers\Admin\UsersController as AdminUsersController;
use App\Http\Controllers\Admin\CertificatesController as AdminCertificatesController;
use App\Http\Controllers\Admin\PartyOrgansController as AdminPartyOrgansController;
use App\Http\Controllers\Admin\PartyLeaguesController as AdminPartyLeaguesController;
use App\Http\Controllers\Admin\DialogueController as AdminDialogueController;
use App\Http\Controllers\Admin\DialogueReportsController;
use App\Http\Controllers\Admin\PriorityProjectsController as AdminPriorityProjectsController;
use App\Http\Controllers\Admin\PresidiumAdminController;
use App\Http\Controllers\Admin\AcademyBadgesAdminController;
use App\Http\Controllers\Admin\AdminAnalyticsController;
use App\Http\Controllers\Admin\HomeBannersController;
use App\Http\Controllers\Admin\StaticPagesController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AuditLogsController;
use App\Http\Controllers\Admin\AdminGuideController;
use App\Http\Controllers\Admin\PresidiumPublicationsController;
use App\Http\Controllers\Admin\AdminActivityController;
use App\Http\Controllers\Admin\AdminQuickSearchController;
use App\Http\Controllers\Admin\AdminFaqController;
use App\Http\Controllers\Admin\AdminPlatformSettingsController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\CertificatePreviewController;
use App\Http\Controllers\CertificateVerificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LegalPagesController;
use App\Http\Controllers\SetupWizardController;
use App\Http\Controllers\WebAcademyController;
use App\Http\Controllers\WebLibraryController;
use App\Http\Controllers\WebPartyOrgansController;

Route::get('/', function () {
    return view('welcome');
});

// Public certificate verification (no auth)
Route::get('/verify-certificate', [CertificateVerificationController::class, 'show'])
    ->middleware('throttle:certificate-verify')
    ->name('certificate.verify');

// Public health endpoint for load balancers / uptime checks
Route::get('/health', [HealthController::class, 'show'])->name('health');

// Public legal pages (for web + mobile linking if needed)
Route::get('/privacy-policy', fn () => app(LegalPagesController::class)->show('privacy'))->name('legal.privacy');
Route::get('/terms-of-use', fn () => app(LegalPagesController::class)->show('terms'))->name('legal.terms');
Route::get('/cookies', fn () => app(LegalPagesController::class)->show('cookies'))->name('legal.cookies');

// Web authentication (Blade-based) for admins and senior members
Route::middleware('guest')->group(function () {
    Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [WebAuthController::class, 'login']);

    Route::get('/register', [WebAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [WebAuthController::class, 'register']);

    Route::get('/password/forgot', [WebAuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/password/email', [WebAuthController::class, 'sendResetLinkEmail'])->name('password.email');
});

Route::post('/logout', [WebAuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    // One-time setup wizard (system_admin only)
    Route::middleware('setup.pending')->group(function () {
        Route::get('/setup', [SetupWizardController::class, 'show'])->name('setup');
        Route::post('/setup', [SetupWizardController::class, 'store'])->name('setup.store');
    });

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Constitution reader (doc: zanupf | zimbabwe | amendment3)
    Route::get('/constitution/{doc?}/{section?}', [WebConstitutionController::class, 'index'])
        ->where('doc', 'zanupf|zimbabwe|amendment3')
        ->name('constitution.home')
        ->defaults('doc', 'zanupf');

    Route::get('/academy', [WebAcademyController::class, 'home'])->name('academy.home');
    Route::get('/library', [WebLibraryController::class, 'index'])->name('library.home');
    Route::get('/library/documents/{document}', [WebLibraryController::class, 'show'])->name('library.document');

    Route::get('/party', [WebPartyController::class, 'index'])->name('party.home');

    Route::get('/party-organs', [WebPartyOrgansController::class, 'index'])->name('party-organs.home');
    Route::get('/party-organs/{party_organ}', [WebPartyOrgansController::class, 'show'])->name('party-organs.show');

    Route::get('/certificate-preview', [CertificatePreviewController::class, 'show'])->name('certificate.preview');
    Route::view('/dialogue', 'sections.dialogue')->name('dialogue.home');
    Route::middleware(['admin.content', 'admin.section'])->group(function () {
        Route::view('/admin', 'sections.admin')->name('admin.home');

        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/guide/documentation', [AdminGuideController::class, 'documentation'])->name('guide.documentation');
            Route::get('/guide/help', [AdminGuideController::class, 'help'])->name('guide.help');
            Route::get('/guide/settings', [AdminGuideController::class, 'settings'])->name('guide.settings');
            Route::get('/platform-settings', [AdminPlatformSettingsController::class, 'edit'])->name('platform-settings.edit');
            Route::put('/platform-settings', [AdminPlatformSettingsController::class, 'update'])->name('platform-settings.update');
            Route::get('/guide/faq', [AdminFaqController::class, 'index'])->name('guide.faq');
            Route::post('/guide/faq/questions', [AdminFaqController::class, 'storeQuestion'])->name('guide.faq.questions.store');

            Route::post('/activity/seen', [AdminActivityController::class, 'markSeen'])->name('activity.seen');
            Route::get('/quick-search', AdminQuickSearchController::class)->name('quick-search');

            Route::get('/dialogue', [AdminDialogueController::class, 'index'])->name('dialogue.index');
            Route::get('/dialogue/channels/{channel}/threads', [AdminDialogueController::class, 'threads'])->name('dialogue.threads.index');
            Route::post('/dialogue/channels/{channel}/threads', [AdminDialogueController::class, 'storeThread'])->name('dialogue.threads.store');
            Route::get('/dialogue/threads/{thread}', [AdminDialogueController::class, 'showThread'])->name('dialogue.threads.show');
            Route::post('/dialogue/threads/{thread}/lock', [AdminDialogueController::class, 'lockThread'])->name('dialogue.threads.lock');
            Route::post('/dialogue/threads/{thread}/unlock', [AdminDialogueController::class, 'unlockThread'])->name('dialogue.threads.unlock');
            Route::post('/dialogue/threads/{thread}/messages', [AdminDialogueController::class, 'storeMessage'])->name('dialogue.messages.store');
            Route::post('/dialogue/messages/{message}/pin', [AdminDialogueController::class, 'pinMessage'])->name('dialogue.messages.pin');
            Route::post('/dialogue/messages/{message}/unpin', [AdminDialogueController::class, 'unpinMessage'])->name('dialogue.messages.unpin');
            Route::delete('/dialogue/messages/{message}', [AdminDialogueController::class, 'destroyMessage'])->name('dialogue.messages.destroy');

            Route::get('/dialogue/reports', [DialogueReportsController::class, 'index'])->name('dialogue.reports.index');
            Route::post('/dialogue/reports/{report}/resolve', [DialogueReportsController::class, 'resolve'])->name('dialogue.reports.resolve');
            Route::post('/dialogue/reports/{report}/remove-message', [DialogueReportsController::class, 'removeMessage'])->name('dialogue.reports.remove-message');
            Route::post('/dialogue/reports/{report}/lock-thread', [DialogueReportsController::class, 'lockThread'])->name('dialogue.reports.lock-thread');
            Route::get('/constitution', [AdminConstitutionController::class, 'index'])->name('constitution.index');
            Route::post('/constitution/amendment-official-pdf', [AdminConstitutionController::class, 'uploadAmendmentOfficialPdf'])
                ->name('constitution.amendment-official-pdf');
            Route::get('/constitution/parts', [AdminConstitutionController::class, 'partsIndex'])->name('constitution.parts');
            Route::get('/constitution/parts/{part}/edit', [AdminConstitutionController::class, 'partEdit'])->name('constitution.parts.edit');
            Route::post('/constitution/parts', [AdminConstitutionController::class, 'partStore'])->name('constitution.parts.store');
            Route::put('/constitution/parts/{part}', [AdminConstitutionController::class, 'partUpdate'])->name('constitution.parts.update');
            Route::delete('/constitution/parts/{part}', [AdminConstitutionController::class, 'partDestroy'])->name('constitution.parts.destroy');

            Route::get('/constitution/parts/{part}/chapters', [AdminConstitutionController::class, 'chaptersIndex'])->name('constitution.chapters');
            Route::get('/constitution/chapters/{chapter}/edit', [AdminConstitutionController::class, 'chapterEdit'])->name('constitution.chapters.edit');
            Route::post('/constitution/parts/{part}/chapters', [AdminConstitutionController::class, 'chapterStore'])->name('constitution.chapters.store');
            Route::put('/constitution/chapters/{chapter}', [AdminConstitutionController::class, 'chapterUpdate'])->name('constitution.chapters.update');
            Route::delete('/constitution/chapters/{chapter}', [AdminConstitutionController::class, 'chapterDestroy'])->name('constitution.chapters.destroy');

            Route::get('/constitution/chapters/{chapter}/sections', [AdminConstitutionController::class, 'sectionsIndex'])->name('constitution.sections');
            Route::get('/constitution/sections/{section}/edit', [AdminConstitutionController::class, 'sectionEdit'])->name('constitution.sections.edit');
            Route::post('/constitution/chapters/{chapter}/sections', [AdminConstitutionController::class, 'sectionStore'])->name('constitution.sections.store');
            Route::put('/constitution/sections/{section}', [AdminConstitutionController::class, 'sectionUpdate'])->name('constitution.sections.update');
            Route::delete('/constitution/sections/{section}', [AdminConstitutionController::class, 'sectionDestroy'])->name('constitution.sections.destroy');

            Route::get('/constitution/sections/{section}/versions', [AdminConstitutionController::class, 'versionsIndex'])->name('constitution.sections.versions');
            Route::get('/constitution/sections/{section}/versions/create', [AdminConstitutionController::class, 'versionCreate'])->name('constitution.versions.create');
            Route::post('/constitution/sections/{section}/versions', [AdminConstitutionController::class, 'versionStore'])->name('constitution.versions.store');
            Route::get('/constitution/versions/{sectionVersion}/edit', [AdminConstitutionController::class, 'versionEdit'])->name('constitution.versions.edit');
            Route::put('/constitution/versions/{sectionVersion}', [AdminConstitutionController::class, 'versionUpdate'])->name('constitution.versions.update');
            Route::post('/constitution/versions/{sectionVersion}/submit', [AdminConstitutionController::class, 'versionSubmitForApproval'])->name('constitution.versions.submit');
            Route::post('/constitution/versions/{sectionVersion}/approve', [AdminConstitutionController::class, 'versionApprove'])->middleware('presidium')->name('constitution.versions.approve');
            Route::post('/constitution/versions/{sectionVersion}/reject', [AdminConstitutionController::class, 'versionReject'])->middleware('presidium')->name('constitution.versions.reject');

            Route::get('/academy', [AdminAcademyController::class, 'index'])->name('academy.index');
            Route::get('/academy/courses/create', [AdminAcademyController::class, 'courseCreate'])->name('academy.courses.create');
            Route::post('/academy/courses', [AdminAcademyController::class, 'courseStore'])->name('academy.courses.store');
            Route::get('/academy/courses/{course}/edit', [AdminAcademyController::class, 'courseEdit'])->name('academy.courses.edit');
            Route::put('/academy/courses/{course}', [AdminAcademyController::class, 'courseUpdate'])->name('academy.courses.update');
            Route::delete('/academy/courses/{course}', [AdminAcademyController::class, 'courseDestroy'])->name('academy.courses.destroy');

            Route::get('/academy/courses/{course}/assessments', [AdminAcademyController::class, 'assessmentsIndex'])->name('academy.assessments.index');
            Route::get('/academy/courses/{course}/assessments/create', [AdminAcademyController::class, 'assessmentCreate'])->name('academy.assessments.create');
            Route::post('/academy/courses/{course}/assessments', [AdminAcademyController::class, 'assessmentStore'])->name('academy.assessments.store');
            Route::get('/academy/assessments/{assessment}', [AdminAcademyController::class, 'assessmentShow'])->name('academy.assessments.show');
            Route::get('/academy/assessments/{assessment}/edit', [AdminAcademyController::class, 'assessmentEdit'])->name('academy.assessments.edit');
            Route::put('/academy/assessments/{assessment}', [AdminAcademyController::class, 'assessmentUpdate'])->name('academy.assessments.update');
            Route::delete('/academy/assessments/{assessment}', [AdminAcademyController::class, 'assessmentDestroy'])->name('academy.assessments.destroy');

            Route::get('/academy/assessments/{assessment}/questions/create', [AdminAcademyController::class, 'questionCreate'])->name('academy.questions.create');
            Route::post('/academy/assessments/{assessment}/questions', [AdminAcademyController::class, 'questionStore'])->name('academy.questions.store');
            Route::get('/academy/questions/{question}/edit', [AdminAcademyController::class, 'questionEdit'])->name('academy.questions.edit');
            Route::put('/academy/questions/{question}', [AdminAcademyController::class, 'questionUpdate'])->name('academy.questions.update');
            Route::delete('/academy/questions/{question}', [AdminAcademyController::class, 'questionDestroy'])->name('academy.questions.destroy');

            // Academy badges (achievement criteria) per course
            Route::get('/academy/courses/{course}/badges', [AcademyBadgesAdminController::class, 'index'])->name('academy.badges.index');
            Route::get('/academy/courses/{course}/badges/create', [AcademyBadgesAdminController::class, 'create'])->name('academy.badges.create');
            Route::post('/academy/courses/{course}/badges', [AcademyBadgesAdminController::class, 'store'])->name('academy.badges.store');
            Route::get('/academy/courses/{course}/badges/{badge}/edit', [AcademyBadgesAdminController::class, 'edit'])->name('academy.badges.edit');
            Route::put('/academy/courses/{course}/badges/{badge}', [AcademyBadgesAdminController::class, 'update'])->name('academy.badges.update');
            Route::delete('/academy/courses/{course}/badges/{badge}', [AcademyBadgesAdminController::class, 'destroy'])->name('academy.badges.destroy');

            Route::get('/library', [AdminLibraryController::class, 'index'])->name('library.index');
            Route::get('/library/categories', [AdminLibraryController::class, 'categoriesIndex'])->name('library.categories.index');
            Route::get('/library/categories/create', [AdminLibraryController::class, 'categoryCreate'])->name('library.categories.create');
            Route::post('/library/categories', [AdminLibraryController::class, 'categoryStore'])->name('library.categories.store');
            Route::get('/library/categories/{category}/edit', [AdminLibraryController::class, 'categoryEdit'])->name('library.categories.edit');
            Route::put('/library/categories/{category}', [AdminLibraryController::class, 'categoryUpdate'])->name('library.categories.update');
            Route::delete('/library/categories/{category}', [AdminLibraryController::class, 'categoryDestroy'])->name('library.categories.destroy');

            Route::get('/library/documents', [AdminLibraryController::class, 'documentsIndex'])->name('library.documents.index');
            Route::get('/library/documents/create', [AdminLibraryController::class, 'documentCreate'])->name('library.documents.create');
            Route::post('/library/documents', [AdminLibraryController::class, 'documentStore'])->name('library.documents.store');
            Route::get('/library/documents/{document}/edit', [AdminLibraryController::class, 'documentEdit'])->name('library.documents.edit');
            Route::put('/library/documents/{document}', [AdminLibraryController::class, 'documentUpdate'])->name('library.documents.update');
            Route::delete('/library/documents/{document}', [AdminLibraryController::class, 'documentDestroy'])->name('library.documents.destroy');

            Route::get('/party', [AdminPartyController::class, 'index'])->name('party.index');
            Route::put('/party', [AdminPartyController::class, 'update'])->name('party.update');
            Route::post('/party/related-sections', [AdminPartyController::class, 'attachSection'])->name('party.related-sections.attach');
            Route::delete('/party/related-sections/{id}', [AdminPartyController::class, 'detachSection'])->name('party.related-sections.detach');
            Route::put('/party/related-sections/order', [AdminPartyController::class, 'updateRelatedSectionOrder'])->name('party.related-sections.order');

            Route::get('/party-leagues', [AdminPartyLeaguesController::class, 'index'])->name('party-leagues.index');
            Route::get('/party-leagues/create', [AdminPartyLeaguesController::class, 'create'])->name('party-leagues.create');
            Route::post('/party-leagues', [AdminPartyLeaguesController::class, 'store'])->name('party-leagues.store');
            Route::get('/party-leagues/{party_league}/edit', [AdminPartyLeaguesController::class, 'edit'])->name('party-leagues.edit');
            Route::put('/party-leagues/{party_league}', [AdminPartyLeaguesController::class, 'update'])->name('party-leagues.update');
            Route::delete('/party-leagues/{party_league}', [AdminPartyLeaguesController::class, 'destroy'])->name('party-leagues.destroy');

            Route::get('/users', [AdminUsersController::class, 'index'])->name('users.index');
            Route::get('/users/{user}/edit', [AdminUsersController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [AdminUsersController::class, 'update'])->name('users.update');
            Route::get('/members', [AdminMembersController::class, 'index'])->name('members.index');
            Route::get('/certificates', [AdminCertificatesController::class, 'index'])->name('certificates.index');
            Route::post('/certificates/{certificate}/revoke', [AdminCertificatesController::class, 'revoke'])->name('certificates.revoke');
            Route::post('/certificates/{certificate}/unrevoke', [AdminCertificatesController::class, 'unrevoke'])->name('certificates.unrevoke');

            Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics.index');
            Route::get('/analytics/export/enrolments', [AdminAnalyticsController::class, 'exportEnrolments'])->name('analytics.export.enrolments');
            Route::get('/analytics/export/attempts', [AdminAnalyticsController::class, 'exportAttempts'])->name('analytics.export.attempts');

            Route::get('/home-banners', [HomeBannersController::class, 'index'])->name('home-banners.index');
            Route::get('/home-banners/create', [HomeBannersController::class, 'create'])->name('home-banners.create');
            Route::post('/home-banners', [HomeBannersController::class, 'store'])->name('home-banners.store');
            Route::get('/home-banners/{home_banner}/edit', [HomeBannersController::class, 'edit'])->name('home-banners.edit');
            Route::put('/home-banners/{home_banner}', [HomeBannersController::class, 'update'])->name('home-banners.update');
            Route::delete('/home-banners/{home_banner}', [HomeBannersController::class, 'destroy'])->name('home-banners.destroy');

            Route::get('/static-pages', [StaticPagesController::class, 'index'])->name('static-pages.index');
            Route::get('/static-pages/{page}/edit', [StaticPagesController::class, 'edit'])->name('static-pages.edit');
            Route::put('/static-pages/{page}', [StaticPagesController::class, 'update'])->name('static-pages.update');

            Route::get('/priority-projects', [AdminPriorityProjectsController::class, 'index'])->name('priority-projects.index');
            Route::get('/priority-projects/create', [AdminPriorityProjectsController::class, 'create'])->name('priority-projects.create');
            Route::post('/priority-projects', [AdminPriorityProjectsController::class, 'store'])->name('priority-projects.store');
            Route::get('/priority-projects/{priority_project}/edit', [AdminPriorityProjectsController::class, 'edit'])->name('priority-projects.edit');
            Route::put('/priority-projects/{priority_project}', [AdminPriorityProjectsController::class, 'update'])->name('priority-projects.update');
            Route::delete('/priority-projects/{priority_project}', [AdminPriorityProjectsController::class, 'destroy'])->name('priority-projects.destroy');

            Route::get('/presidium', [PresidiumAdminController::class, 'index'])->name('presidium.index');
            Route::get('/presidium/create', [PresidiumAdminController::class, 'create'])->name('presidium.create');
            Route::post('/presidium', [PresidiumAdminController::class, 'store'])->name('presidium.store');
            Route::get('/presidium/{presidium}/edit', [PresidiumAdminController::class, 'edit'])->name('presidium.edit');
            Route::put('/presidium/{presidium}', [PresidiumAdminController::class, 'update'])->name('presidium.update');
            Route::delete('/presidium/{presidium}', [PresidiumAdminController::class, 'destroy'])->name('presidium.destroy');

            Route::get('/presidium-publications', [PresidiumPublicationsController::class, 'index'])->name('presidium-publications.index');
            Route::get('/presidium-publications/create', [PresidiumPublicationsController::class, 'create'])->name('presidium-publications.create');
            Route::post('/presidium-publications', [PresidiumPublicationsController::class, 'store'])->name('presidium-publications.store');
            Route::get('/presidium-publications/{publication}/edit', [PresidiumPublicationsController::class, 'edit'])->name('presidium-publications.edit');
            Route::put('/presidium-publications/{publication}', [PresidiumPublicationsController::class, 'update'])->name('presidium-publications.update');
            Route::delete('/presidium-publications/{publication}', [PresidiumPublicationsController::class, 'destroy'])->name('presidium-publications.destroy');

            Route::get('/party-organs', [AdminPartyOrgansController::class, 'index'])->name('party-organs.index');
            Route::get('/party-organs/create', [AdminPartyOrgansController::class, 'create'])->name('party-organs.create');
            Route::post('/party-organs', [AdminPartyOrgansController::class, 'store'])->name('party-organs.store');
            Route::get('/party-organs/{party_organ}/edit', [AdminPartyOrgansController::class, 'edit'])->name('party-organs.edit');
            Route::put('/party-organs/{party_organ}', [AdminPartyOrgansController::class, 'update'])->name('party-organs.update');
            Route::delete('/party-organs/{party_organ}', [AdminPartyOrgansController::class, 'destroy'])->name('party-organs.destroy');

            Route::get('/audit-logs', [AuditLogsController::class, 'index'])->name('audit-logs.index');
            Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
            Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
            Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
            Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
            Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
            Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        });
    });
});
