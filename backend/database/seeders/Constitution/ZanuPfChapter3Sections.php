<?php

namespace Database\Seeders\Constitution;

use App\Models\Chapter;
use App\Models\Section;
use App\Models\SectionVersion;

final class ZanuPfChapter3Sections
{
    public static function seed(Chapter $chapter): void
    {
        $article23 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '23',
            'slug' => 'article-23-youth-league-name-aims-membership',
            'title' => 'Name, Aims and Objects, and Membership of the Youth League',
            'order' => 1,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article23->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-23-youth-league-name-aims-membership'),
            'status' => 'published',
        ]);

        // Chapter 3 – Article 24: YOUTH LEAGUE ORGANS (Principal Organs and National Conference)
        $article24 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '24',
            'slug' => 'article-24-youth-league-organs',
            'title' => 'Organs and Structures of the Youth League',
            'order' => 2,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article24->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-24-youth-league-organs'),
            'status' => 'published',
        ]);

        // Chapter 3 – Article 25: PRINCIPAL OFFICERS OF THE YOUTH LEAGUE
        $article25 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '25',
            'slug' => 'article-25-principal-officers-youth-league',
            'title' => 'Principal Officers of the Youth League',
            'order' => 3,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article25->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-25-principal-officers-youth-league'),
            'status' => 'published',
        ]);

        // Chapter 3 – Article 26: PROVINCE (Youth League)
        $article26 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '26',
            'slug' => 'article-26-youth-league-province',
            'title' => 'Province (Youth League)',
            'order' => 4,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article26->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-26-youth-league-province'),
            'status' => 'published',
        ]);

        // Chapter 3 – Article 28: BRANCH (YOUTH LEAGUE)
        $article28 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '28',
            'slug' => 'article-28-youth-league-branch',
            'title' => 'Branch (Youth League)',
            'order' => 4,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article28->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-28-youth-league-branch'),
            'status' => 'published',
        ]);
    }
}