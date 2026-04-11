<?php

namespace Database\Seeders\Constitution;

use App\Models\Chapter;
use App\Models\Section;
use App\Models\SectionVersion;

final class ZanuPfChapter2Sections
{
    public static function seed(Chapter $chapter): void
    {
        // Chapter 2 – Article 17: NAME / AIMS AND OBJECTS / MEMBERSHIP
        $article17 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '17',
            'slug' => 'article-17-womens-league-name-aims-membership',
            'title' => 'Name, Aims and Objects, and Membership of the Women\'s League',
            'order' => 1,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article17->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-17-womens-league-name-aims-membership'),
            'status' => 'published',
        ]);

        // Chapter 2 – Article 18: ORGANS AND STRUCTURES OF THE WOMEN'S LEAGUE
        $article18 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '18',
            'slug' => 'article-18-organs-and-structures-of-the-womens-league',
            'title' => 'Organs and Structures of the Women\'s League',
            'order' => 2,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article18->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-18-organs-and-structures-of-the-womens-league'),
            'status' => 'published',
        ]);

        // Chapter 2 – Article 19: PROVINCE (WOMEN'S LEAGUE)
        $article19 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '19',
            'slug' => 'article-19-womens-league-province',
            'title' => 'Province (Women\'s League)',
            'order' => 3,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article19->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-19-womens-league-province'),
            'status' => 'published',
        ]);

        // Chapter 2 – Article 20: PRINCIPAL OFFICERS OF THE PROVINCIAL EXECUTIVE COMMITTEE OF WOMEN'S LEAGUE
        $article20 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '20',
            'slug' => 'article-20-womens-league-provincial-executive-committee',
            'title' => 'Principal Officers of the Provincial Executive Committee of the Women\'s League',
            'order' => 4,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article20->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-20-womens-league-provincial-executive-committee'),
            'status' => 'published',
        ]);

        // Chapter 2 – Article 21: DISTRICTS (WOMEN'S LEAGUE)
        $article21 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '21',
            'slug' => 'article-21-womens-league-districts',
            'title' => 'Districts (Women\'s League)',
            'order' => 5,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article21->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-21-womens-league-districts'),
            'status' => 'published',
        ]);

        // Chapter 2 – Article 22: BRANCH (WOMEN'S LEAGUE)
        $article22 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '22',
            'slug' => 'article-22-womens-league-branch',
            'title' => 'Branch (Women\'s League)',
            'order' => 6,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article22->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-22-womens-league-branch'),
            'status' => 'published',
        ]);
    }
}