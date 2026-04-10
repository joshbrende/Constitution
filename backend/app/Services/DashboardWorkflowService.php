<?php

namespace App\Services;

use App\Models\Course;
use App\Models\SectionVersion;
use App\Models\User;

class DashboardWorkflowService
{
    public function __construct(
        protected AdminAccessService $adminAccess
    ) {}

    /**
     * Pending counts and links for dashboard alerts (professional at-a-glance).
     *
     * @return array{pending_presidium_approvals: int, draft_amendments: int, academy_draft_courses: int}
     */
    public function getPendingCounts(): array
    {
        return [
            'pending_presidium_approvals' => SectionVersion::where('status', 'in_review')->count(),
            'draft_amendments' => SectionVersion::where('status', 'draft')->count(),
            'academy_draft_courses' => Course::where('status', 'draft')->count(),
        ];
    }

    /**
     * Workflow panels for each role the user has (from config/role_workflows.php).
     *
     * @return list<array{slug: string, title: string, summary: string, steps: list<string>}>
     */
    public function getWorkflowPanelsForUser(User $user): array
    {
        $config = config('role_workflows', []);
        $panels = [];

        foreach ($user->roles as $role) {
            $slug = (string) $role->slug;
            if (! isset($config[$slug])) {
                continue;
            }
            $panels[] = [
                'slug' => $slug,
                'title' => $config[$slug]['title'] ?? $role->name,
                'summary' => $config[$slug]['summary'] ?? '',
                'steps' => $config[$slug]['steps'] ?? [],
            ];
        }

        return $panels;
    }

    /**
     * Short alert lines for dashboard banner (role-aware).
     *
     * @return list<string>
     */
    public function getAlertLinesForUser(User $user): array
    {
        $counts = $this->getPendingCounts();
        $lines = [];

        if ($this->adminAccess->canAccessSection($user, 'constitution')) {
            if ($counts['pending_presidium_approvals'] > 0) {
                $lines[] = $counts['pending_presidium_approvals'] . ' amendment(s) awaiting Presidium approval — open Admin → Constitution → Amendments.';
            }
            if ($counts['draft_amendments'] > 0) {
                $lines[] = $counts['draft_amendments'] . ' draft amendment version(s) in the system — submit for review when ready.';
            }
        }

        if ($this->adminAccess->canAccessSection($user, 'academy') && $counts['academy_draft_courses'] > 0) {
            $lines[] = $counts['academy_draft_courses'] . ' Academy course(s) in draft — Admin → Manage Academy.';
        }

        return $lines;
    }
}
