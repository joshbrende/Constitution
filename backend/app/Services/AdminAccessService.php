<?php

namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;

class AdminAccessService
{
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
        if (! $user) {
            return false;
        }

        $allowedRoles = config("admin.sections.{$section}", []);
        if (empty($allowedRoles)) {
            return false;
        }

        $userRoles = $user->roles->pluck('slug')->map(fn ($s) => (string) $s)->all();

        return count(array_intersect($allowedRoles, $userRoles)) > 0;
    }

    /**
     * Check if user has any admin access (can see admin home).
     */
    public function hasAnyAdminAccess(?Authenticatable $user): bool
    {
        if (! $user) {
            return false;
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
        if (! $user) {
            return [];
        }

        $sections = array_keys(config('admin.sections', []));

        return array_values(array_filter($sections, fn ($s) => $this->canAccessSection($user, $s)));
    }
}
