<?php

namespace Database\Seeders;

use App\Models\AmendmentClauseRelation;
use App\Models\Chapter;
use App\Models\Section;
use App\Models\SectionVersion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AmendmentBill2026Seeder extends Seeder
{
    public function run(): void
    {
        $slug = 'amendment3';
        $chapterTitle = (string) config('constitution.amendment3_chapter_title');
        $lawReference = (string) config('constitution.amendment3_law_reference');

        $chapter = Chapter::updateOrCreate(
            [
                'constitution_slug' => $slug,
                'number' => '0',
                'part_id' => null,
            ],
            [
                'title' => $chapterTitle,
                'order' => 0,
            ]
        );

        $zwSections = Section::whereHas('chapter', fn ($q) => $q->where('constitution_slug', 'zimbabwe'))
            ->get()
            ->keyBy('logical_number');

        $sections = AmendmentBill2026SectionDefinitions::all();
        foreach ($sections as $i => $sec) {
            $section = Section::firstOrCreate(
                ['chapter_id' => $chapter->id, 'logical_number' => $sec['num']],
                [
                    'slug' => 'am3-' . $sec['num'] . '-' . Str::slug($sec['title']),
                    'title' => $sec['title'],
                    'order' => $i,
                    'is_active' => true,
                ]
            );
            $existing = $section->versions()->where('version_number', 1)->first();
            if ($existing) {
                $existing->update(['body' => $sec['body'], 'law_reference' => $lawReference]);
            } else {
                SectionVersion::create([
                    'section_id' => $section->id,
                    'version_number' => 1,
                    'law_reference' => $lawReference,
                    'body' => $sec['body'],
                    'status' => 'published',
                ]);
            }

            if (! empty($sec['amends'] ?? [])) {
                AmendmentClauseRelation::where('amendment_section_id', $section->id)->delete();
                foreach ($sec['amends'] as $ref) {
                    $zwSection = $zwSections->get($ref['num'] ?? null);
                    AmendmentClauseRelation::create([
                        'amendment_section_id' => $section->id,
                        'zimbabwe_section_id' => $zwSection?->id,
                        'ref_label' => $ref['label'] ?? ('Section ' . ($ref['num'] ?? '')),
                        'relation_type' => $ref['type'] ?? 'amends',
                    ]);
                }
            }
        }
    }
}
