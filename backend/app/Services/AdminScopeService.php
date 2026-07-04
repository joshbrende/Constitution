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
     * Optional district filter for provincial admins assigned to a district.
     *
     * - null: no district filter (province-wide)
     * - positive int: filter to that district
     */
    public function scopedDistrictId(User $admin): ?int
    {
        if (! config('scoping.enable_district_scoping', true)) {
            return null;
        }

        if ($this->scopedProvinceId($admin) === null) {
            return null;
        }

        if (! $admin->district_id) {
            return null;
        }

        return (int) $admin->district_id;
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

        $query = $query->where('province_id', $provinceId);

        $districtId = $this->scopedDistrictId($admin);
        if ($districtId !== null) {
            $query = $query->where('district_id', $districtId);
        }

        return $query;
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

        if ((int) $target->province_id !== $provinceId) {
            return false;
        }

        $districtId = $this->scopedDistrictId($admin);
        if ($districtId !== null) {
            return (int) $target->district_id === $districtId;
        }

        return true;
    }

    /**
     * Scope certificate applications to applicants in the admin's province.
     *
     * @param  Builder<\App\Models\CertificateApplication>  $query
     * @return Builder<\App\Models\CertificateApplication>
     */
    public function applyToCertificateApplicationQuery(Builder $query, User $admin): Builder
    {
        $provinceId = $this->scopedProvinceId($admin);

        if ($provinceId === null) {
            return $query;
        }

        if ($provinceId === 0) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('user', function (Builder $userQuery) use ($provinceId, $admin) {
            $userQuery->where('province_id', $provinceId);
            $districtId = $this->scopedDistrictId($admin);
            if ($districtId !== null) {
                $userQuery->where('district_id', $districtId);
            }
        });
    }

    public function assertCanAccessUser(User $admin, User $target): void
    {
        if (! $this->canAccessUser($admin, $target)) {
            throw new NotFoundHttpException('User not found.');
        }
    }
}
