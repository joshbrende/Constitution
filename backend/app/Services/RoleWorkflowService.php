<?php

namespace App\Services;

use App\Models\SectionVersion;
use App\Models\User;

class RoleWorkflowService
{
    public function __construct(
        protected AdminAccessService $adminAccess
    ) {}

    /**
     * Metrics that explain cross-role execution (amendment pipeline).
     *
     * @return array{pending_presidium:int, draft_amendments:int}
     */
    public function getAmendmentPipelineCounts(): array
    {
        return [
            'pending_presidium' => SectionVersion::where('status', 'in_review')->count(),
            'draft_amendments' => SectionVersion::where('status', 'draft')->count(),
        ];
    }

    /**
     * Workflow panels for the dashboard: one entry per role the user has that is defined in config.
     *
     * @return list<array{slug: string, title: string, summary: string, steps: list<string>, links: list<array{route: string, label: string}>}>
     */
    public function getWorkflowPanelsForUser(?User $user): array
    {
        if (! $user) {
            return [];
        }

        $definitions = config('role_workflows', []);
        $panels = [];

        foreach ($user->roles as $role) {
            $slug = (string) $role->slug;
            if (! isset($definitions[$slug])) {
                continue;
            }
            $def = $definitions[$slug];
            $panels[] = [
                'slug' => $slug,
                'title' => $def['title'] ?? $role->name,
                'summary' => $def['summary'] ?? '',
                'steps' => $def['steps'] ?? [],
                'links' => $def['links'] ?? [],
            ];
        }

        return $panels;
    }

    /**
     * Short status line for users involved in the amendment workflow.
     */
    public function getAmendmentStatusHintsForUser(?User $user): array
    {
        if (! $user) {
            return [];
        }

        $counts = $this->getAmendmentPipelineCounts();
        $hints = [];

        $canEdit = $this->adminAccess->canAccessSection($user, 'constitution');
        $canPresidium = $user->hasRole('presidium') || $user->hasRole('system_admin');

        if ($canEdit && $counts['draft_amendments'] > 0) {
            $hints[] = "{$counts['draft_amendments']} constitutional amendment draft(s) — complete and submit for Presidium when ready.";
        }

        if ($canPresidium && $counts['pending_presidium'] > 0) {
            $hints[] = "{$counts['pending_presidium']} amendment version(s) awaiting Presidium approval.";
        }

        if ($canEdit && ! $canPresidium && $counts['pending_presidium'] > 0) {
            $hints[] = "{$counts['pending_presidium']} version(s) are with Presidium for approval.";
        }

        return $hints;
    }
}
