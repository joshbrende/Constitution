<?php

namespace Database\Seeders;

use App\Models\PresidiumMember;
use App\Models\PresidiumPublication;
use App\Models\Section;
use Illuminate\Database\Seeder;

class PresidiumSeeder extends Seeder
{
    public function run(): void
    {
        // Helper: look up a ZANU PF constitution section by slug; return null if missing
        $zanupf = function (string $slug): ?int {
            return Section::where('slug', $slug)->value('id');
        };

        // Helper: look up a Zimbabwe constitution section by slug; return null if missing
        $zimbabwe = function (string $slug): ?int {
            return Section::where('slug', $slug)->value('id');
        };

        // Where possible, tie Presidium roles to both constitutions.
        // Slugs below rely on existing seeder conventions:
        // - ZANU PF: article describing principal organs and the Presidium.
        // - Zimbabwe: sections under Chapter 5 (The Executive), if present.

        $members = [
            [
                'role_slug' => 'president',
                'name' => 'CDE Dr Emmerson Dambudzo Mnangagwa',
                'title' => 'President & ZANU PF First Secretary',
                'photo_url' => '/icon-1.png',
                'bio' => 'President of the Republic of Zimbabwe and First Secretary of ZANU PF.',
                'order' => 1,
                'zanupf_section_slug' => 'article-4-principal-organs-and-structure',
                'zimbabwe_section_slug' => 'zw-ch5-s89-president',
            ],
            [
                'role_slug' => 'vice_president_1',
                'name' => 'CDE Dr Constantino Guveya Dominic Nyikadzino Chiwenga',
                'title' => 'Vice President & ZANU PF Second Secretary',
                'photo_url' => null,
                'bio' => 'Vice President of the Republic of Zimbabwe and Second Secretary of ZANU PF.',
                'order' => 2,
                'zanupf_section_slug' => 'article-4-principal-organs-and-structure',
                'zimbabwe_section_slug' => 'zw-ch5-s90-vice-presidents',
            ],
            [
                'role_slug' => 'vice_president_2',
                'name' => 'CDE Col (RTD) Kembo Dugish Campbell Muleya Mohadi',
                'title' => 'Vice President & ZANU PF Second Secretary',
                'photo_url' => null,
                'bio' => 'Vice President of the Republic of Zimbabwe and Second Secretary of ZANU PF.',
                'order' => 3,
                'zanupf_section_slug' => 'article-4-principal-organs-and-structure',
                'zimbabwe_section_slug' => 'zw-ch5-s90-vice-presidents',
            ],
            [
                'role_slug' => 'national_chairperson',
                'name' => 'CDE Oppah Chamu Zvipange Muchinguri',
                'title' => 'National Chairperson Of ZANU PF',
                'photo_url' => null,
                'bio' => 'National Chairperson of ZANU PF, presiding over Party organs as provided in the Constitution.',
                'order' => 4,
                'zanupf_section_slug' => 'article-4-principal-organs-and-structure',
                'zimbabwe_section_slug' => null,
            ],
            [
                'role_slug' => 'secretary_general',
                'name' => 'CDE Advocate Jacob Mudenda',
                'title' => 'Secretary-General',
                'photo_url' => null,
                'bio' => 'Secretary-General of ZANU PF, responsible for the administration of the Party.',
                'order' => 5,
                'zanupf_section_slug' => 'article-4-principal-organs-and-structure',
                'zimbabwe_section_slug' => null,
            ],
        ];

        foreach ($members as $m) {
            PresidiumMember::updateOrCreate(
                ['role_slug' => $m['role_slug']],
                [
                    'name' => $m['name'],
                    'title' => $m['title'],
                    'photo_url' => $m['photo_url'],
                    'bio' => $m['bio'],
                    'order' => $m['order'],
                    'is_published' => true,
                    'zanupf_section_id' => $m['zanupf_section_slug']
                        ? $zanupf($m['zanupf_section_slug'])
                        : null,
                    'zimbabwe_section_id' => $m['zimbabwe_section_slug']
                        ? $zimbabwe($m['zimbabwe_section_slug'])
                        : null,
                ]
            );
        }

        PresidiumPublication::updateOrCreate(
            ['slug' => 'mnangagwa-a-life-of-sacrifice'],
            [
                'title' => "Mnangagwa’s ‘A Life of Sacrifice",
                'author' => 'Eddie Graham Cross',
                'summary' => 'An authorised biography covering the life history of President Emmerson Mnangagwa, from childhood to presidency.',
                'cover_url' => '/icon-1.png',
                'article_url' => 'https://www.thezimbabwean.co/2021/08/mnangagwas-a-life-of-sacrifice/',
                'purchase_url' => null,
                'online_copy_url' => null,
                'is_featured' => true,
                'order' => 1,
                'is_published' => true,
            ]
        );
    }
}

