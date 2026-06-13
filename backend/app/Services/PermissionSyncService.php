<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class PermissionSyncService
{
    /**
     * Sync admin section permissions from config/admin.php and admin_actions from config/permissions.php.
     */
    public function syncAdminPermissions(): void
    {
        DB::transaction(function () {
            $this->syncAdminSections();
            $this->syncAdminActions();
        });
    }

    /**
     * Sync API ability permission records and role assignments from config/permissions.php.
     */
    public function syncApiPermissions(): void
    {
        DB::transaction(function () {
            $labels = config('permissions.api_ability_labels', []);
            foreach ($labels as $slug => $name) {
                Permission::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $name,
                        'description' => null,
                        'domain' => Permission::DOMAIN_API,
                        'resource_type' => 'route_group',
                    ]
                );
            }

            $roleMap = config('permissions.role_api_abilities', []);
            foreach ($roleMap as $roleSlug => $abilities) {
                $role = Role::query()->where('slug', $roleSlug)->first();
                if (! $role) {
                    continue;
                }

                $permissionIds = Permission::query()
                    ->where('domain', Permission::DOMAIN_API)
                    ->whereIn('slug', $abilities)
                    ->pluck('id')
                    ->all();

                $existingAdminIds = $role->permissions()
                    ->where('domain', Permission::DOMAIN_ADMIN)
                    ->pluck('permissions.id')
                    ->all();

                $role->permissions()->sync(array_values(array_unique(array_merge($existingAdminIds, $permissionIds))));
            }
        });
    }

    public function syncAll(): void
    {
        $this->syncAdminPermissions();
        $this->syncApiPermissions();
    }

    private function syncAdminSections(): void
    {
        foreach (config('admin.sections', []) as $section => $roleSlugs) {
            $permission = Permission::updateOrCreate(
                ['slug' => "admin.section.{$section}"],
                [
                    'name' => 'Admin: '.str_replace('_', ' ', $section),
                    'description' => "Access admin section {$section}",
                    'domain' => Permission::DOMAIN_ADMIN,
                    'resource_type' => 'section',
                ]
            );

            $roleIds = Role::query()->whereIn('slug', $roleSlugs)->pluck('id')->all();
            $this->attachAdminPermissionToRoles($permission, $roleIds);
        }
    }

    private function syncAdminActions(): void
    {
        foreach (config('permissions.admin_actions', []) as $action => $meta) {
            $permission = Permission::updateOrCreate(
                ['slug' => "admin.action.{$action}"],
                [
                    'name' => $meta['name'] ?? $action,
                    'description' => null,
                    'domain' => Permission::DOMAIN_ADMIN,
                    'resource_type' => 'action',
                ]
            );

            $roleIds = Role::query()->whereIn('slug', $meta['roles'] ?? [])->pluck('id')->all();
            $this->attachAdminPermissionToRoles($permission, $roleIds);
        }
    }

    /**
     * @param  list<int>  $roleIds
     */
    private function attachAdminPermissionToRoles(Permission $permission, array $roleIds): void
    {
        foreach ($roleIds as $roleId) {
            $role = Role::query()->find($roleId);
            if (! $role) {
                continue;
            }

            $currentIds = $role->permissions()->pluck('permissions.id')->all();
            if (! in_array($permission->id, $currentIds, true)) {
                $role->permissions()->attach($permission->id);
            }
        }
    }
}
