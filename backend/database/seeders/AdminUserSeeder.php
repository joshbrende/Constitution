<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@zanupf.org'],
            [
                'name' => 'System',
                'surname' => 'Administrator',
                'password' => 'Admin@2025!',
            ]
        );

        $admin->password = 'Admin@2025!';
        $admin->save();

        $systemAdminRole = Role::where('slug', 'system_admin')->first();
        if ($systemAdminRole && !$admin->hasRole('system_admin')) {
            $admin->roles()->attach($systemAdminRole->id);
        }
    }
}
