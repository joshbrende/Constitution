<?php

namespace Database\Seeders;

use App\Models\PartyLeague;
use App\Models\PartyProfile;
use Illuminate\Database\Seeder;

class PartyLeaguesSeeder extends Seeder
{
    /**
     * Populate party_leagues from existing party_profile data (Veterans, Women's, Youth).
     * Safe to run multiple times (updateOrCreate by slug).
     */
    public function run(): void
    {
        $profile = PartyProfile::first();
        if (!$profile) {
            return;
        }

        $leagues = [
            [
                'name' => 'Veterans League',
                'slug' => 'veterans-league',
                'leader_name' => $profile->veterans_league_leader_name,
                'leader_title' => $profile->veterans_league_leader_title,
                'body' => $profile->veterans_league_body,
                'sort_order' => 10,
            ],
            [
                'name' => "Women's League",
                'slug' => 'womens-league',
                'leader_name' => $profile->womens_league_leader_name,
                'leader_title' => $profile->womens_league_leader_title,
                'body' => $profile->womens_league_body,
                'sort_order' => 20,
            ],
            [
                'name' => 'Youth League',
                'slug' => 'youth-league',
                'leader_name' => $profile->youth_league_leader_name,
                'leader_title' => $profile->youth_league_leader_title,
                'body' => $profile->youth_league_body,
                'sort_order' => 30,
            ],
        ];

        foreach ($leagues as $data) {
            PartyLeague::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
