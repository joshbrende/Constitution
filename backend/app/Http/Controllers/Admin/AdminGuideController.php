<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LibraryDocument;
use App\Models\Section;
use App\Models\User;
use App\Services\AdminAccessService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminGuideController extends Controller
{
    /**
     * Admin modules for in-app documentation (order matches typical workflow).
     *
     * @var list<array{section: string|null, label: string, route: string|null, summary: string}>
     */
    private const ADMIN_MODULES = [
        ['section' => null, 'label' => 'Admin & Oversight', 'route' => 'admin.home', 'summary' => 'Entry point to administration: quick links into each governed area.'],
        ['section' => 'constitution', 'label' => 'Manage Constitution', 'route' => 'admin.constitution.index', 'summary' => 'Structure: Parts → Chapters → Sections. Edit content via section versions, submit for approval; Presidium approves or rejects. Upload official Amendment Bill PDF for the mobile API.'],
        ['section' => 'academy', 'label' => 'Manage Academy', 'route' => 'admin.academy.index', 'summary' => 'Courses, modules, lessons, assessments, questions, and per-course achievement badges. Control publish state and mandatory flags.'],
        ['section' => 'library', 'label' => 'Manage Digital Library', 'route' => 'admin.library.index', 'summary' => 'Categories and documents with visibility (public, member, leadership). Files and metadata shown in web and mobile library.'],
        ['section' => 'party', 'label' => 'Party profile', 'route' => 'admin.party.index', 'summary' => 'Party-facing narrative content and links to related constitution sections.'],
        ['section' => 'party_organs', 'label' => 'Party organs', 'route' => 'admin.party-organs.index', 'summary' => 'Organisations and structures surfaced on the Party Organs area of the site.'],
        ['section' => 'party_leagues', 'label' => 'Party leagues', 'route' => 'admin.party-leagues.index', 'summary' => 'League profiles and ordering for public party pages.'],
        ['section' => 'presidium', 'label' => 'Presidium', 'route' => 'admin.presidium.index', 'summary' => 'Presidium roster used in party content; pairs with approval powers on constitution versions.'],
        ['section' => 'priority_projects', 'label' => 'Priority projects', 'route' => 'admin.priority-projects.index', 'summary' => 'Featured projects; members can like published items per policy.'],
        ['section' => 'home_banners', 'label' => 'Home banners', 'route' => 'admin.home-banners.index', 'summary' => 'Rotating or static banners on the home experience.'],
        ['section' => 'static_pages', 'label' => 'Help & legal pages', 'route' => 'admin.static-pages.index', 'summary' => 'Static HTML pages (help, legal, policies) editable by authorised roles.'],
        ['section' => 'dialogue', 'label' => 'Dialogue', 'route' => 'admin.dialogue.index', 'summary' => 'Channels, threads, messages: moderate discussion, pin or remove content, lock threads.'],
        ['section' => 'certificates', 'label' => 'Certificates', 'route' => 'admin.certificates.index', 'summary' => 'Issued academy certificates; revoke or reinstate with audit trail. Public verification uses /verify-certificate.'],
        ['section' => 'users', 'label' => 'Users', 'route' => 'admin.users.index', 'summary' => 'Accounts, roles, and profile fields for back-office and app users.'],
        ['section' => 'members', 'label' => 'Members', 'route' => 'admin.members.index', 'summary' => 'Membership-oriented views and workflows alongside user records.'],
        ['section' => 'analytics', 'label' => 'Analytics & reports', 'route' => 'admin.analytics.index', 'summary' => 'Enrolments and assessment activity; CSV exports where enabled.'],
        ['section' => 'audit_logs', 'label' => 'Audit logs', 'route' => 'admin.audit-logs.index', 'summary' => 'Immutable-style log of sensitive actions (e.g. certificate changes) for oversight.'],
        ['section' => 'roles', 'label' => 'Roles', 'route' => 'admin.roles.index', 'summary' => 'Role definitions and permissions mapping (system administrator only).'],
    ];

    public function documentation(Request $request, AdminAccessService $adminAccess): View
    {
        $user = $request->user();
        $user?->loadMissing('roles');

        $accessible = [];
        foreach (config('admin.sections', []) as $key => $_) {
            if ($adminAccess->canAccessSection($user, $key)) {
                $accessible[$key] = true;
            }
        }

        $stats = [
            'sections' => Section::query()->count(),
            'courses' => Course::query()->count(),
            'library_docs' => LibraryDocument::query()->count(),
            'users' => User::query()->count(),
        ];

        return view('admin.guide.documentation', [
            'docVersion' => config('admin_guide.doc_version', '1.0.0'),
            'accessibleSections' => $accessible,
            'modules' => self::ADMIN_MODULES,
            'stats' => $stats,
        ]);
    }

    public function help(Request $request, AdminAccessService $adminAccess): View
    {
        $request->user()?->loadMissing('roles');

        return view('admin.guide.help', [
            'accessibleSections' => $adminAccess->getAccessibleSections($request->user()),
        ]);
    }

    public function settings(Request $request): View
    {
        $user = $request->user();
        $user?->loadMissing('roles');

        return view('admin.guide.settings', [
            'user' => $user,
        ]);
    }
}
