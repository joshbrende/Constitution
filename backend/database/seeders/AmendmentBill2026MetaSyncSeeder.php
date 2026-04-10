<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Section;
use App\Models\SectionVersion;
use Illuminate\Database\Seeder;

/**
 * Keeps amendment bill chapter title and law_reference in sync with config for
 * databases seeded before config-driven titles. Does not overwrite clause bodies
 * so content edited in admin is preserved.
 */
class AmendmentBill2026MetaSyncSeeder extends Seeder
{
    public function run(): void
    {
        $chapterTitle = (string) config('constitution.amendment3_chapter_title');
        $lawReference = (string) config('constitution.amendment3_law_reference');

        Chapter::query()
            ->where('constitution_slug', 'amendment3')
            ->whereNull('part_id')
            ->update(['title' => $chapterTitle]);

        $sectionIds = Section::query()
            ->whereHas('chapter', fn ($q) => $q->where('constitution_slug', 'amendment3'))
            ->pluck('id');

        if ($sectionIds->isEmpty()) {
            return;
        }

        SectionVersion::query()
            ->whereIn('section_id', $sectionIds)
            ->update(['law_reference' => $lawReference]);
    }
}
