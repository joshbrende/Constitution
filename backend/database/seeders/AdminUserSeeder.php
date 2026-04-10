<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Requires ADMIN_SEED_PASSWORD in .env (plain text; stored hashed via User cast).
     * Never commit real passwords; omit the variable to skip seeding the admin user.
     */
    public function run(): void
    {
        $adminPlainSecret = env('ADMIN_SEED_PASSWORD');

        if (! is_string($adminPlainSecret) || $adminPlainSecret === '') {
            if ($this->command !== null) {
                $this->command->warn('AdminUserSeeder skipped: set ADMIN_SEED_PASSWORD in .env to create or update the seed admin user.');
            }

            return;
        }

        $admin = User::updateOrCreate(
            ['email' => 'admin@zanupf.org'],
            [
                'name' => 'System',
                'surname' => 'Administrator',
                'password' => $adminPlainSecret,
            ]
        );

        $systemAdminRole = Role::where('slug', 'system_admin')->first();
        if ($systemAdminRole && ! $admin->hasRole('system_admin')) {
            $admin->roles()->attach($systemAdminRole->id);
        }
    }
}
