<?php

namespace Database\Seeders;

use App\Models\LibraryCategory;
use App\Models\LibraryDocument;
use Illuminate\Database\Seeder;

class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'slug' => 'party-policy',
                'name' => 'Party policy',
                'description' => 'Official policy papers and resolutions.',
                'order' => 10,
            ],
            [
                'slug' => 'constitutional-education',
                'name' => 'Constitutional education',
                'description' => 'Guides for studying the ZANU PF and Zimbabwe Constitutions.',
                'order' => 20,
            ],
            [
                'slug' => 'speeches-statements',
                'name' => 'Speeches & statements',
                'description' => 'Selected addresses and public statements.',
                'order' => 30,
            ],
        ];

        $categoryIds = [];
        foreach ($categories as $cat) {
            $model = LibraryCategory::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                    'order' => $cat['order'],
                ]
            );
            $categoryIds[$cat['slug']] = $model->id;
        }

        $documents = [
            [
                'slug' => 'zanupf-constitution-overview',
                'title' => 'ZANU PF Constitution — study guide',
                'library_category_id' => $categoryIds['constitutional-education'],
                'abstract' => 'An introductory guide to the principal organs, membership, and discipline framework of the Party Constitution.',
                'body' => '<p>Use this guide alongside the in-app Constitution reader. Focus on Part I (The Party), membership obligations, and the role of Congress and the Presidium.</p>',
                'document_type' => 'manual',
                'access_rule' => 'member',
            ],
            [
                'slug' => 'zimbabwe-constitution-amendment-no-3-brief',
                'title' => 'Constitution Amendment No. 3 — briefing note',
                'library_category_id' => $categoryIds['constitutional-education'],
                'abstract' => 'Summary of proposed reforms and how members can follow debate through official channels.',
                'body' => '<p>This briefing note supports constitutional literacy on Amendment No. 3. Read the full text in the Constitutions tab and discuss through approved Party structures.</p>',
                'document_type' => 'policy',
                'access_rule' => 'public',
            ],
            [
                'slug' => 'vision-2030-member-handbook',
                'title' => 'Vision 2030 — member handbook',
                'library_category_id' => $categoryIds['party-policy'],
                'abstract' => 'How Vision 2030 priority projects connect to provincial and branch mobilisation.',
                'body' => '<p>Members are encouraged to study priority projects in the app, share feedback with local leadership, and participate in community programmes aligned to national development goals.</p>',
                'document_type' => 'manual',
                'access_rule' => 'member',
            ],
            [
                'slug' => 'academy-membership-pathway',
                'title' => 'Academy membership pathway',
                'library_category_id' => $categoryIds['party-policy'],
                'abstract' => 'Steps from enrolment to assessment, certificate payment, and collection.',
                'body' => '<p>Complete the membership course, pass the assessment, pay at the designated government office, and collect your physical certificate after Presidium approval.</p>',
                'document_type' => 'manual',
                'access_rule' => 'member',
            ],
            [
                'slug' => 'presidium-address-on-constitutional-education',
                'title' => 'Presidium address on constitutional education',
                'library_category_id' => $categoryIds['speeches-statements'],
                'abstract' => 'Excerpt on the importance of constitutional literacy for every cadre.',
                'body' => '<p>Constitutional education strengthens internal democracy, discipline, and national unity. Every member should know both the Party Constitution and the Constitution of Zimbabwe.</p>',
                'document_type' => 'speech',
                'access_rule' => 'member',
            ],
        ];

        foreach ($documents as $doc) {
            LibraryDocument::updateOrCreate(
                ['slug' => $doc['slug']],
                [
                    'library_category_id' => $doc['library_category_id'],
                    'title' => $doc['title'],
                    'abstract' => $doc['abstract'],
                    'body' => $doc['body'],
                    'document_type' => $doc['document_type'],
                    'language' => 'en',
                    'published_at' => now(),
                    'access_rule' => $doc['access_rule'],
                ]
            );
        }
    }
}
