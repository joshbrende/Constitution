<?php

namespace Database\Seeders;

use App\Models\HomeBanner;
use Illuminate\Database\Seeder;

class HomeBannersSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            [
                'title' => 'Constitution Amendment No. 3',
                'subtitle' => 'Stay informed on the proposed constitutional reforms. Read the Constitution of Zimbabwe and track amendments.',
                'image_url' => 'https://zanupf.org.zw/assets/banners/constitution.jpg',
                'cta_label' => 'Read Constitution',
                'cta_type' => 'internal',
                'cta_tab' => 'ConstitutionTab',
                'cta_screen' => null,
                'cta_params' => ['doc' => 'zimbabwe'],
                'cta_url' => null,
                'sort_order' => 5,
            ],
            [
                'title' => 'Vision 2030 in action',
                'subtitle' => 'Track strategic projects that move Zimbabwe towards an upper middle-income economy.',
                'image_url' => 'https://zanupf.org.zw/assets/banners/vision-2030.jpg',
                'cta_label' => 'View priority projects',
                'cta_type' => 'internal',
                'cta_tab' => 'HomeTab',
                'cta_screen' => 'PriorityProjects',
                'cta_url' => null,
                'sort_order' => 10,
            ],
            [
                'title' => 'Become a ZANU PF member',
                'subtitle' => 'Complete the membership course and assessment in the Academy to earn your certificate.',
                'image_url' => 'https://zanupf.org.zw/assets/banners/membership.jpg',
                'cta_label' => 'Open Academy',
                'cta_type' => 'internal',
                'cta_tab' => 'HomeTab',
                'cta_screen' => 'AcademyHome',
                'cta_url' => null,
                'sort_order' => 20,
            ],
            [
                'title' => 'Know the Constitution',
                'subtitle' => 'Study the ZANU PF and Zimbabwe Constitutions article by article, anywhere, anytime.',
                'image_url' => 'https://zanupf.org.zw/assets/banners/constitution.jpg',
                'cta_label' => 'Read now',
                'cta_type' => 'internal',
                'cta_tab' => 'ConstitutionTab',
                'cta_screen' => null,
                'cta_url' => null,
                'sort_order' => 30,
            ],
        ];

        foreach ($defaults as $data) {
            $ctaParams = $data['cta_params'] ?? null;
            unset($data['cta_params']);
            HomeBanner::updateOrCreate(
                ['title' => $data['title']],
                [
                    'subtitle' => $data['subtitle'],
                    'image_url' => $data['image_url'],
                    'cta_label' => $data['cta_label'],
                    'cta_url' => $data['cta_url'] ?? null,
                    'cta_type' => $data['cta_type'] ?? null,
                    'cta_tab' => $data['cta_tab'] ?? null,
                    'cta_screen' => $data['cta_screen'] ?? null,
                    'cta_params' => $ctaParams,
                    'is_published' => true,
                    'sort_order' => $data['sort_order'],
                ]
            );
        }
    }
}

