<?php

namespace App\Services;

use App\Enums\MembershipStanding;
use App\Models\Permission;
use App\Models\User;

class TokenAbilityService
{
    /**
     * Resolve Sanctum abilities for a user's access token (union of role API permissions).
     *
     * @return list<string>
     */
    public function abilitiesForUser(User $user): array
    {
        if ($this->isSuspended($user)) {
            return config('permissions.suspended_api_abilities', ['profile:read']);
        }

        $user->loadMissing('roles.permissions');

        $fromRoles = [];
        foreach ($user->roles as $role) {
            foreach ($role->permissions as $permission) {
                if ($permission->domain === Permission::DOMAIN_API) {
                    $fromRoles[] = $permission->slug;
                }
            }
        }

        if ($fromRoles !== []) {
            return array_values(array_unique($fromRoles));
        }

        $fromConfig = $this->abilitiesFromConfig($user);
        if ($fromConfig !== []) {
            return $fromConfig;
        }

        return config('permissions.default_api_abilities', ['profile:read', 'profile:write']);
    }

    /**
     * @return list<string>
     */
    private function abilitiesFromConfig(User $user): array
    {
        $map = config('permissions.role_api_abilities', []);
        $abilities = [];

        foreach ($user->roles as $role) {
            $slug = (string) $role->slug;
            if (isset($map[$slug])) {
                $abilities = array_merge($abilities, $map[$slug]);
            }
        }

        return array_values(array_unique($abilities));
    }

    private function isSuspended(User $user): bool
    {
        $standing = $user->membership_standing;

        if ($standing instanceof MembershipStanding) {
            return $standing === MembershipStanding::Suspended;
        }

        return (string) $standing === MembershipStanding::Suspended->value;
    }
}
