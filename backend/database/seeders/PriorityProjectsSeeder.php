<?php

namespace Database\Seeders;

use App\Models\PriorityProject;
use App\Models\Section;
use Illuminate\Database\Seeder;

class PriorityProjectsSeeder extends Seeder
{
    public function run(): void
    {
        // If projects already exist, don't duplicate.
        if (PriorityProject::count() > 0) {
            return;
        }

        $zanupf = function (?string $slug): ?int {
            return $slug ? Section::where('slug', $slug)->value('id') : null;
        };

        $projects = [
            [
                'title' => 'Rural Development and Township Upgrading',
                'summary' => 'Accelerating infrastructure, services, and livelihoods in rural communities and townships towards Vision 2030.',
                'body' => 'A multi-year programme to upgrade rural growth points, townships, and service centres with roads, clinics, schools, markets, and digital infrastructure, aligned to national development strategies and Party resolutions.',
                'image_url' => null,
                'zanupf_section_slug' => 'article-4-principal-organs-and-structure',
            ],
            [
                'title' => 'Youth Empowerment and Skills',
                'summary' => 'Structured youth skills, entrepreneurship, and employment programmes anchored to Party and national policy.',
                'body' => 'Focus on training, apprenticeships, and startup support for young people, with special emphasis on agriculture, mining, manufacturing, ICT, and creative industries.',
                'image_url' => null,
                'zanupf_section_slug' => 'article-3-objectives',
            ],
            [
                'title' => 'Modernisation of Party Structures',
                'summary' => 'Digitising Party records, communication, and mobilisation to strengthen internal democracy and efficiency.',
                'body' => 'Includes digital membership systems, data-backed mobilisation, and better feedback loops from branches and cells up to the Presidium.',
                'image_url' => null,
                'zanupf_section_slug' => 'article-2-name-and-headquarters',
            ],
        ];

        foreach ($projects as $index => $p) {
            PriorityProject::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($p['title'])],
                [
                    'title' => $p['title'],
                    'summary' => $p['summary'],
                    'body' => $p['body'],
                    'image_url' => $p['image_url'],
                    'zanupf_section_id' => $zanupf($p['zanupf_section_slug'] ?? null),
                    'zimbabwe_section_id' => null,
                    'is_published' => true,
                    'published_at' => now()->subDays(max(0, 10 - $index * 3)),
                ]
            );
        }
    }
}

