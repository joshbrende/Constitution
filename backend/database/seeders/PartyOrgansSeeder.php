<?php

namespace Database\Seeders;

use App\Models\PartyOrgan;
use Illuminate\Database\Seeder;

class PartyOrgansSeeder extends Seeder
{
    public function run(): void
    {
        $organs = [
            [
                'name' => 'National People\'s Congress',
                'slug' => 'congress',
                'short_description' => 'The supreme organ of the Party; convenes every five years.',
                'body' => '<p>The National People\'s Congress (Congress) is the supreme organ of the Party. It is composed of delegates from branches, districts, provinces, the Women\'s League, the Youth League, and other structures.</p><p>Congress is the supreme policy-making organ. It elects the President and the Central Committee, receives and considers reports, and formulates directives, rules and regulations for all organs of the Party. Congress convenes every five years.</p>',
                'order' => 10,
                'is_published' => true,
            ],
            [
                'name' => 'Central Committee',
                'slug' => 'central-committee',
                'short_description' => 'The highest organ of the Party between Congress.',
                'body' => '<p>The Central Committee is the principal organ of the Party between Congress. It consists of three hundred (300) members and is the highest organ when Congress is not in session.</p><p>Acting on behalf of Congress when Congress is not in session, the Central Committee has full plenary powers to direct the affairs of the Party, implement Congress decisions, and oversee all subordinate organs including the Politburo, Women\'s League, Youth League, and provincial structures.</p>',
                'order' => 20,
                'is_published' => true,
            ],
            [
                'name' => 'Politburo',
                'slug' => 'politburo',
                'short_description' => 'The administrative organ of the Central Committee.',
                'body' => '<p>The Politburo is the executive committee of the Central Committee. It acts as the administrative organ of the Central Committee and meets regularly to implement decisions of Congress and the Central Committee.</p><p>The Politburo oversees the day-to-day running of the Party and coordinates between the Central Committee and lower organs.</p>',
                'order' => 30,
                'is_published' => true,
            ],
            [
                'name' => 'Women\'s League',
                'slug' => 'womens-league',
                'short_description' => 'The Women\'s Wing of ZANU PF.',
                'body' => '<p>The Women\'s League is the Women\'s Wing of the Party. Its aims include mobilising women, promoting their rights, education and equality, and advancing the Party\'s objectives.</p><p>Every woman member of the Party aged eighteen years and above is entitled to join the Women\'s League through her Branch. The League has its own organs and structures at national, provincial, district and branch levels, subject to the overriding authority of the Central Committee.</p>',
                'order' => 40,
                'is_published' => true,
            ],
            [
                'name' => 'Youth League',
                'slug' => 'youth-league',
                'short_description' => 'The Youth Wing of ZANU PF.',
                'body' => '<p>The Youth League is the Youth Wing of the Party. Its aims align with Article 2 of the Party Constitution and include promoting youth participation, development, and patriotism.</p><p>The Youth League has its own organs and structures at national, provincial, district and branch levels. It works in close consultation with the Central Committee and implements Party policies among the youth.</p>',
                'order' => 50,
                'is_published' => true,
            ],
            [
                'name' => 'Provincial Structures',
                'slug' => 'provincial-structures',
                'short_description' => 'Provincial Executive Councils and Coordinating Committees.',
                'body' => '<p>Provincial structures implement Party decisions at provincial level. The Provincial Executive Council (PEC) and Provincial Coordinating Committee (PCC) oversee branches and districts within the province.</p><p>Women shall constitute at least one-third of the total membership of the principal organs at provincial level and below, in line with the Party Constitution.</p>',
                'order' => 60,
                'is_published' => true,
            ],
            [
                'name' => 'National Disciplinary Committee',
                'slug' => 'national-disciplinary-committee',
                'short_description' => 'The organ with power to hear and determine disciplinary matters.',
                'body' => '<p>Only the National Disciplinary Committee may expel a member from the Party. Members against whom disciplinary action is intended must first be issued with a prohibition order and notice of charges in writing.</p><p>The notice shall state the charges and the date and venue of the hearing. The member has the right to be represented by a member of their choice. Appeals lie to the appropriate superior organ.</p>',
                'order' => 70,
                'is_published' => true,
            ],
        ];

        foreach ($organs as $data) {
            PartyOrgan::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
