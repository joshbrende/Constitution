<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Finalize Phase 1 pilot go-live after ProvincialPilotSeeder:
 * - Platform site settings (if missing)
 * - Presidium certificate approver account
 * - installed_at (marks setup wizard complete)
 *
 * Run: php artisan db:seed --class=PilotGoLiveSeeder --force
 *
 * Requires PILOT_ADMIN_PASSWORD in .env (shared with provincial pilot admins).
 */
class PilotGoLiveSeeder extends Seeder
{
    public function run(): void
    {
        $password = (string) config('pilot.admin_password', '');
        if ($password === '') {
            if ($this->command !== null) {
                $this->command->warn('PilotGoLiveSeeder skipped: set PILOT_ADMIN_PASSWORD in .env.');
            }

            return;
        }

        $baseUrl = rtrim((string) config('pilot.public_site_url', 'http://localhost:8081'), '/');

        $this->ensurePlatformSettings($baseUrl);
        $this->ensurePresidiumApprover($password);
        $this->ensureInstalled();

        if ($this->command !== null) {
            $this->command->info('Pilot go-live complete.');
            $this->command->line("Public site URL: {$baseUrl}");
            $this->command->line('Presidium approver: '.config('pilot.presidium_email'));
            $this->command->line('Password: (see PILOT_ADMIN_PASSWORD in .env)');
        }
    }

    private function ensurePlatformSettings(string $baseUrl): void
    {
        $defaults = [
            'org_name' => 'ZANU PF',
            'support_email' => 'support@zanupf.org.zw',
            'public_site_url' => $baseUrl,
            'legal_privacy_url' => $baseUrl.'/privacy-policy',
            'legal_terms_url' => $baseUrl.'/terms-of-use',
            'legal_cookies_url' => $baseUrl.'/cookies',
            'enable_dialogue' => true,
            'require_national_id' => true,
        ];

        foreach ($defaults as $key => $value) {
            $current = SiteSetting::get($key);
            if ($current === null || $current === '') {
                SiteSetting::set($key, $value);
            }
        }
    }

    private function ensurePresidiumApprover(string $password): void
    {
        $email = (string) config('pilot.presidium_email', 'presidium.pilot@zanupf.org.zw');

        $presidiumRole = Role::firstOrCreate(
            ['slug' => 'presidium'],
            ['name' => 'Presidium', 'description' => 'Presidium member; approves constitutional amendments and certificates.']
        );

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Presidium',
                'surname' => 'Pilot',
                'password' => $password,
                'accepted_terms_at' => now(),
            ]
        );

        if (! $user->hasRole('presidium')) {
            $user->roles()->syncWithoutDetaching([$presidiumRole->id]);
        }

        if ($this->command !== null) {
            $this->command->info("Presidium approver: {$email}");
        }
    }

    private function ensureInstalled(): void
    {
        if (empty(SiteSetting::get('installed_at'))) {
            SiteSetting::set('installed_at', now()->toIso8601String());

            if ($this->command !== null) {
                $this->command->info('Platform install marked complete (installed_at set).');
            }
        }
    }
}
