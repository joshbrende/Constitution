<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Collection;

class BackendRoleDutiesService
{
    /**
     * Roles that may be assigned when provisioning backend dashboard staff.
     *
     * @return Collection<int, Role>
     */
    public function provisionableRoles(?User $actor): Collection
    {
        $adminSlugs = app(AdminAccessService::class)->getAllAdminRoleSlugs();

        return app(RoleAssignmentService::class)
            ->assignableRoles($actor)
            ->filter(fn (Role $role) => in_array($role->slug, $adminSlugs, true))
            ->values();
    }

    /**
     * @return list<int>
     */
    public function provisionableRoleIds(?User $actor): array
    {
        return $this->provisionableRoles($actor)->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    public function canProvisionBackendUsers(?User $actor): bool
    {
        return $actor instanceof User && $actor->hasRole('system_admin');
    }

    /**
     * @param  list<int>|Collection<int, int>  $roleIds
     * @return list<string>
     */
    public function accessibleSectionSlugsForRoleIds(array|Collection $roleIds): array
    {
        $slugs = Role::query()->whereIn('id', collect($roleIds)->unique()->values())->pluck('slug')->all();

        return $this->accessibleSectionSlugsForRoleSlugs($slugs);
    }

    /**
     * @param  list<string>  $roleSlugs
     * @return list<string>
     */
    public function accessibleSectionSlugsForRoleSlugs(array $roleSlugs): array
    {
        $sections = config('admin.sections', []);
        $accessible = [];

        foreach ($sections as $section => $allowedRoles) {
            if (count(array_intersect($roleSlugs, $allowedRoles)) > 0) {
                $accessible[] = $section;
            }
        }

        sort($accessible);

        return array_values($accessible);
    }

    /**
     * @param  list<int>  $roleIds
     * @return list<array{slug: string, name: string, title: string, summary: string, steps: list<string>, sections: list<string>}>
     */
    public function dutyBriefsForRoleIds(array $roleIds): array
    {
        $roles = Role::query()->whereIn('id', $roleIds)->orderBy('name')->get();
        $workflows = config('role_workflows', []);
        $labels = config('admin.section_labels', []);

        $briefs = [];
        foreach ($roles as $role) {
            $slug = (string) $role->slug;
            $workflow = $workflows[$slug] ?? [];
            $sectionSlugs = $this->accessibleSectionSlugsForRoleSlugs([$slug]);
            $sections = array_map(fn ($s) => $labels[$s] ?? str_replace('_', ' ', ucfirst($s)), $sectionSlugs);

            $briefs[] = [
                'slug' => $slug,
                'name' => (string) $role->name,
                'title' => (string) ($workflow['title'] ?? $role->name),
                'summary' => (string) ($workflow['summary'] ?? ''),
                'steps' => array_values($workflow['steps'] ?? []),
                'sections' => $sections,
            ];
        }

        return $briefs;
    }

    /**
     * @param  list<array{slug: string, name: string, title: string, summary: string, steps: list<string>, sections: list<string>}>  $briefs
     */
    public function formatDutyBriefsForEmail(array $briefs): string
    {
        if ($briefs === []) {
            return '';
        }

        $lines = [];
        foreach ($briefs as $brief) {
            $lines[] = '• '.$brief['title'].' ('.$brief['name'].')';
            if ($brief['summary'] !== '') {
                $lines[] = '  '.$brief['summary'];
            }
            if ($brief['sections'] !== []) {
                $lines[] = '  Admin areas: '.implode(', ', $brief['sections']);
            }
            foreach (array_slice($brief['steps'], 0, 3) as $step) {
                $lines[] = '  - '.$step;
            }
        }

        return implode("\n", $lines);
    }
}
