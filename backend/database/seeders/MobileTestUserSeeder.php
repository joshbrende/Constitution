<?php

namespace Database\Seeders;

use App\Models\Province;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Creates a mobile app test account with province + verified national ID
 * so Academy assessments and certificate flows can be exercised end-to-end.
 */
class MobileTestUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('MOBILE_TEST_PASSWORD');
        if (! is_string($password) || $password === '') {
            $password = 'MobileTest123!';
            if ($this->command !== null) {
                $this->command->warn(
                    'MOBILE_TEST_PASSWORD not set in .env — using default password MobileTest123! (dev only).'
                );
            }
        }

        $province = Province::query()->where('code', 'harare')->first();
        if (! $province) {
            if ($this->command !== null) {
                $this->command->error('MobileTestUserSeeder: Harare province not found. Run migrations first.');
            }

            return;
        }

        $user = User::updateOrCreate(
            ['email' => 'mobile.test@zanupf.org.zw'],
            [
                'name' => 'Mobile',
                'surname' => 'Tester',
                'password' => $password,
                'province_id' => $province->id,
                'national_id' => '63-1234567-A12',
                'national_id_verified_at' => now(),
                'national_id_verification_source' => 'seed',
                'accepted_terms_at' => now(),
            ]
        );

        $studentRole = Role::where('slug', 'student')->first();
        if ($studentRole && ! $user->hasRole('student')) {
            $user->roles()->attach($studentRole->id);
        }

        if ($this->command !== null) {
            $this->command->info('Mobile test user ready:');
            $this->command->line('  Email:    mobile.test@zanupf.org.zw');
            $this->command->line('  Password: (see MOBILE_TEST_PASSWORD in .env, or default MobileTest123!)');
            $this->command->line('  Province: Harare');
            $this->command->line('  National ID: 63-1234567-A12 (verified)');
        }
    }
}
