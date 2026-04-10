<?php

namespace App\Http\Middleware;

use App\Services\AdminAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminSection
{
    /** Route name prefix to section mapping (admin.home has no section - allowed for any admin). Most specific first. */
    private const ROUTE_SECTION_MAP = [
        'admin.party-leagues' => 'party_leagues',
        'admin.party-organs' => 'party_organs',
        'admin.priority-projects' => 'priority_projects',
        'admin.home-banners' => 'home_banners',
        'admin.static-pages' => 'static_pages',
        'admin.audit-logs' => 'audit_logs',
        'admin.constitution' => 'constitution',
        'admin.academy' => 'academy',
        'admin.library' => 'library',
        'admin.party' => 'party',
        'admin.presidium' => 'presidium',
        'admin.certificates' => 'certificates',
        'admin.users' => 'users',
        'admin.members' => 'members',
        'admin.analytics' => 'analytics',
        'admin.dialogue' => 'dialogue',
        'admin.roles' => 'roles',
    ];

    public function __construct(
        protected AdminAccessService $adminAccess
    ) {}

    public function handle(Request $request, Closure $next, ?string $section = null): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $section = $section ?? $this->inferSection($request);

        if ($section !== null && ! $this->adminAccess->canAccessSection($request->user(), $section)) {
            abort(403, 'You do not have access to this section.');
        }

        return $next($request);
    }

    private function inferSection(Request $request): ?string
    {
        $routeName = $request->route()?->getName();
        if (! $routeName || $routeName === 'admin.home') {
            return null; // admin.home allows any admin
        }

        foreach (self::ROUTE_SECTION_MAP as $prefix => $section) {
            if (str_starts_with($routeName, $prefix)) {
                return $section;
            }
        }

        return null; // unknown routes fall through
    }
}
