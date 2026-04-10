<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
            ConstitutionSeeder::class,
            ZimbabweConstitutionSeeder::class,
            AmendmentBill2026Seeder::class,
            AmendmentBill2026MetaSyncSeeder::class,
            MembershipCourseSeeder::class,
            DialogueSeeder::class,
            PartyOrgansSeeder::class,
            PresidiumSeeder::class,
            PriorityProjectsSeeder::class,
            \Database\Seeders\AcademyBadgesSeeder::class,
            HomeBannersSeeder::class,
            StaticPagesSeeder::class,
        ]);

        if (file_exists(storage_path('app/zimbabwe-constitution-source.txt'))) {
            Artisan::call('constitution:import-zimbabwe');
        }
    }
}
