<?php

namespace Database\Seeders;

use App\Models\DialogueChannel;
use App\Models\Section;
use Illuminate\Database\Seeder;

class DialogueSeeder extends Seeder
{
    public function run(): void
    {
        $presidiumSectionId = Section::where('slug', 'article-4-principal-organs-and-structure')->value('id');
        $zimbabweExecSectionId = Section::where('slug', 'zw-ch5-s89-president')->value('id');
        $amendmentBill3MemorandumId = Section::where('slug', 'am3-0-memorandum')->value('id');

        DialogueChannel::updateOrCreate(
            ['slug' => 'presidium'],
            [
                'name' => 'Presidium',
                'description' => 'Engagement with the Presidium, anchored to the Party and national Constitutions.',
                'is_public' => true,
                'min_role_slug' => null,
                'zanupf_section_id' => $presidiumSectionId,
                'zimbabwe_section_id' => $zimbabweExecSectionId,
            ]
        );

        DialogueChannel::updateOrCreate(
            ['slug' => 'youth-league'],
            [
                'name' => 'Youth League',
                'description' => 'Dialogue with the Youth League.',
                'is_public' => true,
                'min_role_slug' => null,
            ]
        );

        DialogueChannel::updateOrCreate(
            ['slug' => 'womens-league'],
            [
                'name' => "Women’s League",
                'description' => 'Dialogue with the Women’s League.',
                'is_public' => true,
                'min_role_slug' => null,
            ]
        );

        DialogueChannel::updateOrCreate(
            ['slug' => 'war-veterans-league'],
            [
                'name' => 'War Veterans League',
                'description' => 'Dialogue with the War Veterans League.',
                'is_public' => true,
                'min_role_slug' => null,
            ]
        );

        DialogueChannel::updateOrCreate(
            ['slug' => 'amendment-bill-no-3'],
            [
                'name' => 'Amendment Bill No. 3 (2026)',
                'description' => 'Structured dialogue on '.config('constitution.amendment3_chapter_title').'.',
                'is_public' => true,
                'min_role_slug' => null,
                'zimbabwe_section_id' => $amendmentBill3MemorandumId,
            ]
        );
    }
}

