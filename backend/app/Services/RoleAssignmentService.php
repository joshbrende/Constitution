<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Services\AdminAccessService;
use App\Services\AdminScopeService;
use Illuminate\Support\Collection;

class RoleAssignmentService
{
    private const RESTRICTED_SLUGS = ['system_admin', 'presidium'];

    public function __construct(
        protected AdminAccessService $adminAccess,
        protected AdminScopeService $adminScope
    ) {}

    /**
     * Roles an actor may assign when inviting users or editing role checkboxes.
     *
     * @return Collection<int, Role>
     */
    public function assignableRoles(?User $actor): Collection
    {
        $roles = Role::query()->orderBy('name')->get();

        if (! $actor || $actor->hasRole('system_admin')) {
            return $roles;
        }

        $roles = $roles->reject(fn (Role $r) => in_array($r->slug, self::RESTRICTED_SLUGS, true))->values();

        if ($actor && $this->adminScope->isProvinciallyScoped($actor)) {
            $adminRoleSlugs = $this->adminAccess->getAllAdminRoleSlugs();

            return $roles->reject(fn (Role $r) => in_array($r->slug, $adminRoleSlugs, true))->values();
        }

        return $roles;
    }

    /**
     * @return list<int>
     */
    public function assignableRoleIds(?User $actor): array
    {
        return $this->assignableRoles($actor)->pluck('id')->map(fn ($id) => (int) $id)->all();
    }
}
