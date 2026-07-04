<?php

namespace Database\Seeders;

use App\Models\PartyProfile;
use Illuminate\Database\Seeder;

class PartyProfileSeeder extends Seeder
{
    public function run(): void
    {
        PartyProfile::updateOrCreate(
            ['id' => 1],
            [
                'history' => implode("\n\n", [
                    'ZANU PF is the party that led Zimbabwe to independence in 1980 and continues to champion national unity, constitutional order, and development.',
                    'This app connects members to the Party Constitution, national constitutional literacy, Academy programmes, and structured dialogue with leadership.',
                ]),
                'vision' => 'An empowered, united, and prosperous Zimbabwe anchored in constitutional democracy and Vision 2030.',
                'mission' => 'To mobilise, educate, and organise members to deliver people-centred development, defend constitutional values, and strengthen Party structures at every level.',
                'veterans_league_leader_name' => 'Leader, Veterans League',
                'veterans_league_leader_title' => 'National Chairperson',
                'veterans_league_body' => '<p>The Veterans League honours those who sacrificed for liberation and supports their welfare, mentorship, and continued service to the Party and nation.</p>',
                'womens_league_leader_name' => 'Leader, Women\'s League',
                'womens_league_leader_title' => 'National Chairperson',
                'womens_league_body' => '<p>The Women\'s League advances gender equality, economic empowerment, and leadership development for women across provinces, districts, and branches.</p>',
                'youth_league_leader_name' => 'Leader, Youth League',
                'youth_league_leader_title' => 'National Chairperson',
                'youth_league_body' => '<p>The Youth League develops young cadres through education, skills, entrepreneurship, and disciplined participation in Party organs and national programmes.</p>',
            ]
        );
    }
}
