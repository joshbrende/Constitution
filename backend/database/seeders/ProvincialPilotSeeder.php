<?php

namespace Database\Seeders;

use App\Models\HomeBanner;
use App\Models\Province;
use App\Models\Role;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Phase 1 pilot: Harare + Bulawayo provincial administrator accounts.
 *
 * Run after platform install:
 *   php artisan db:seed --class=ProvincialPilotSeeder
 *
 * Requires PILOT_ADMIN_PASSWORD in .env (plain text; hashed on save).
 */
class ProvincialPilotSeeder extends Seeder
{
    /** @var array<string, array{email: string, name: string, surname: string, code: string}> */
    private const PILOT_PROVINCES = [
        'harare' => [
            'code' => 'harare',
            'name' => 'Harare',
            'surname' => 'Pilot',
            'email' => 'harare.pilot@zanupf.org.zw',
        ],
        'bulawayo' => [
            'code' => 'bulawayo',
            'name' => 'Bulawayo',
            'surname' => 'Pilot',
            'email' => 'bulawayo.pilot@zanupf.org.zw',
        ],
    ];

    public function run(): void
    {
        $password = (string) config('pilot.admin_password', '');
        if ($password === '') {
            if ($this->command !== null) {
                $this->command->warn('ProvincialPilotSeeder skipped: set PILOT_ADMIN_PASSWORD in .env.');
            }

            return;
        }

        $provincialRole = Role::firstOrCreate(
            ['slug' => 'provincial_admin'],
            ['name' => 'Provincial Admin', 'description' => 'Province-scoped administration.']
        );

        $pilotMeta = [];

        foreach (self::PILOT_PROVINCES as $key => $config) {
            $province = Province::query()->where('code', $config['code'])->first();
            if (! $province) {
                if ($this->command !== null) {
                    $this->command->error("Province not found: {$config['code']}");
                }

                continue;
            }

            $user = User::updateOrCreate(
                ['email' => $config['email']],
                [
                    'name' => $config['name'],
                    'surname' => $config['surname'],
                    'password' => $password,
                    'province_id' => $province->id,
                    'accepted_terms_at' => now(),
                ]
            );

            if (! $user->hasRole('provincial_admin')) {
                $user->roles()->syncWithoutDetaching([$provincialRole->id]);
            }

            $pilotMeta[] = [
                'code' => $province->code,
                'name' => $province->name,
                'admin_email' => $config['email'],
                'admin_user_id' => $user->id,
            ];

            if ($this->command !== null) {
                $this->command->info("Pilot provincial admin: {$config['email']} ({$province->name})");
            }
        }

        SiteSetting::set('pilot_phase', 1);
        SiteSetting::set('pilot_started_at', now()->toIso8601String());
        SiteSetting::set('pilot_provinces', $pilotMeta);

        HomeBanner::updateOrCreate(
            ['title' => 'Phase 1 pilot — Harare & Bulawayo'],
            [
                'subtitle' => 'Register, complete your profile (province + national ID), and enrol in the Academy. Pilot live in Harare and Bulawayo metropolitan provinces.',
                'image_url' => '/campaign.jpg',
                'cta_label' => 'Open Academy',
                'cta_type' => 'internal',
                'cta_tab' => 'HomeTab',
                'cta_screen' => 'AcademyHome',
                'cta_url' => null,
                'sort_order' => 1,
                'is_published' => true,
            ]
        );

        if ($this->command !== null) {
            $this->command->newLine();
            $this->command->line('Pilot phase 1 configured: Harare + Bulawayo');
            $this->command->line('Password: (see PILOT_ADMIN_PASSWORD in .env)');
            $this->command->line('National ICT: distribute credentials to provincial chairs securely.');
        }
    }
}
