<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Ensure roles exist and demo users have correct model_has_roles.
     * Run: php artisan db:seed --class=RolesSeeder
     */
    public function run(): void
    {
        $roles = [];
        foreach (['student', 'facilitator', 'admin'] as $name) {
            $roles[$name] = Role::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                ['guard_name' => 'web']
            );
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'role')) {
            $this->syncFromLegacyRoleColumn($roles);
        }
    }

    private function syncFromLegacyRoleColumn(array $roles): void
    {
        $map = [
            'admin' => 'admin',
            'instructor' => 'facilitator',
            'facilitator' => 'facilitator',
            'Facilitator' => 'facilitator',
            'student' => 'student',
        ];

        foreach (User::all() as $user) {
            $legacy = $user->getAttribute('role');
            if ($legacy === null || $legacy === '') {
                continue;
            }
            $name = $map[strtolower(trim((string) $legacy))] ?? 'student';
            $user->roles()->sync([$roles[$name]->id]);
        }
    }
}
