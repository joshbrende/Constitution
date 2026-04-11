<?php

namespace Database\Seeders\Constitution;

use App\Models\Chapter;
use App\Models\Section;
use App\Models\SectionVersion;

final class ZanuPfChapter4Sections
{
    public static function seed(Chapter $chapter): void
    {
        // Chapter 4 – Article 29: GENERAL PROVISIONS
        $article29 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '29',
            'slug' => 'article-29-general-provisions',
            'title' => 'General Provisions',
            'order' => 1,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article29->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-29-general-provisions'),
            'status' => 'published',
        ]);

        // Chapter 4 – Article 30: INTERPRETATION AND AMENDMENT OF THE CONSTITUTION
        $article30 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '30',
            'slug' => 'article-30-interpretation-and-amendment-of-the-constitution',
            'title' => 'Interpretation and Amendment of the Constitution',
            'order' => 2,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article30->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-30-interpretation-and-amendment-of-the-constitution'),
            'status' => 'published',
        ]);
    }
}