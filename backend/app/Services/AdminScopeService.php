<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminScopeService
{
    /**
     * True when the admin must only see users in their assigned province.
     */
    public function isProvinciallyScoped(User $admin): bool
    {
        $provincialRole = (string) config('scoping.provincial_role', 'provincial_admin');

        if (! $admin->hasRole($provincialRole)) {
            return false;
        }

        $overrides = config('scoping.global_override_roles', []);

        foreach ($overrides as $slug) {
            if ($admin->hasRole((string) $slug)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Province filter for provincial admins.
     *
     * - null: no province filter (global admin)
     * - positive int: filter to that province
     * - 0: admin has no province assigned — empty result set
     */
    public function scopedProvinceId(User $admin): ?int
    {
        if (! $this->isProvinciallyScoped($admin)) {
            return null;
        }

        return $admin->province_id ? (int) $admin->province_id : 0;
    }

    /**
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    public function applyToUserQuery(Builder $query, User $admin): Builder
    {
        $provinceId = $this->scopedProvinceId($admin);

        if ($provinceId === null) {
            return $query;
        }

        if ($provinceId === 0) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('province_id', $provinceId);
    }

    public function canAccessUser(User $admin, User $target): bool
    {
        $provinceId = $this->scopedProvinceId($admin);

        if ($provinceId === null) {
            return true;
        }

        if ($provinceId === 0) {
            return false;
        }

        return (int) $target->province_id === $provinceId;
    }

    public function assertCanAccessUser(User $admin, User $target): void
    {
        if (! $this->canAccessUser($admin, $target)) {
            throw new NotFoundHttpException('User not found.');
        }
    }
}
