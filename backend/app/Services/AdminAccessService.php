<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class AdminAccessService
{
    public function permissionsEnabled(): bool
    {
        return Permission::query()->where('domain', Permission::DOMAIN_ADMIN)->exists();
    }

    /**
     * All roles that can access at least one admin section.
     */
    public function getAllAdminRoleSlugs(): array
    {
        $slugs = [];
        foreach (config('admin.sections', []) as $roles) {
            $slugs = array_merge($slugs, $roles);
        }

        return array_values(array_unique($slugs));
    }

    /**
     * Check if user can access the given admin section.
     */
    public function canAccessSection(?Authenticatable $user, string $section): bool
    {
        if (! $user instanceof User) {
            return false;
        }

        if ($this->permissionsEnabled()) {
            return $user->hasPermission("admin.section.{$section}");
        }

        $allowedRoles = config("admin.sections.{$section}", []);
        if (empty($allowedRoles)) {
            return false;
        }

        $userRoles = $user->roles->pluck('slug')->map(fn ($s) => (string) $s)->all();

        return count(array_intersect($allowedRoles, $userRoles)) > 0;
    }

    public function canPerformAdminAction(User $user, string $action): bool
    {
        if ($this->permissionsEnabled()) {
            return $user->hasPermission("admin.action.{$action}");
        }

        return match ($action) {
            'presidium_publish', 'academy_certificate_presidium_approve' => $user->hasRole('presidium') || $user->hasRole('system_admin'),
            'platform_settings', 'roles_manage' => $user->hasRole('system_admin'),
            'academy_payment_confirm' => $user->hasRole('academy_manager') || $user->hasRole('system_admin') || $user->hasRole('provincial_admin'),
            'academy_certificate_print' => $user->hasRole('academy_manager') || $user->hasRole('system_admin'),
            'academy_certificate_collection' => $user->hasRole('academy_manager') || $user->hasRole('system_admin') || $user->hasRole('provincial_admin'),
            default => false,
        };
    }

    /**
     * Check if user has any admin access (can see admin home).
     */
    public function hasAnyAdminAccess(?Authenticatable $user): bool
    {
        if (! $user instanceof User) {
            return false;
        }

        if ($this->permissionsEnabled()) {
            return $user->hasAnyPermissionWithPrefix('admin.section.')
                || $user->hasAnyPermissionWithPrefix('admin.action.');
        }

        $userRoles = $user->roles->pluck('slug')->map(fn ($s) => (string) $s)->all();
        $adminRoles = $this->getAllAdminRoleSlugs();

        return count(array_intersect($adminRoles, $userRoles)) > 0;
    }

    /**
     * Get sections the user can access.
     *
     * @return array<string>
     */
    public function getAccessibleSections(?Authenticatable $user): array
    {
        if (! $user instanceof User) {
            return [];
        }

        $sections = array_keys(config('admin.sections', []));

        return array_values(array_filter($sections, fn ($s) => $this->canAccessSection($user, $s)));
    }
}
