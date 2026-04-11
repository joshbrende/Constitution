<?php

namespace Database\Seeders\Constitution;

use App\Models\Chapter;
use App\Models\Section;
use App\Models\SectionVersion;

final class ZanuPfChapter1Sections
{
    public static function seed(Chapter $chapter): void
    {
        $preamble = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '0',
            'slug' => 'chapter-1-preamble',
            'title' => 'Preamble',
            'order' => 0,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $preamble->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('chapter-1-preamble'),
            'status' => 'published',
        ]);

        // Article 1: THE PARTY
        $article1 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '1',
            'slug' => 'article-1-the-party',
            'title' => 'The Party',
            'order' => 1,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article1->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-1-the-party'),
            'status' => 'published',
        ]);

        // Article 2: AIMS AND OBJECTIVES
        $article2 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '2',
            'slug' => 'article-2-aims-and-objectives',
            'title' => 'Aims and Objectives',
            'order' => 2,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article2->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-2-aims-and-objectives'),
            'status' => 'published',
        ]);

        // Article 3: MEMBERSHIP
        $article3 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '3',
            'slug' => 'article-3-membership',
            'title' => 'Membership',
            'order' => 3,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article3->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-3-membership'),
            'status' => 'published',
        ]);

        // Article 4: PRINCIPAL ORGANS AND STRUCTURE OF THE PARTY
        $article4 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '4',
            'slug' => 'article-4-principal-organs-and-structure',
            'title' => 'Principal Organs and Structure of the Party',
            'order' => 4,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article4->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-4-principal-organs-and-structure'),
            'status' => 'published',
        ]);

        // Article 5: NATIONAL PEOPLE'S CONGRESS
        $article5 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '5',
            'slug' => 'article-5-national-peoples-congress',
            'title' => 'National People\'s Congress',
            'order' => 5,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article5->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-5-national-peoples-congress'),
            'status' => 'published',
        ]);

        // Article 6: NATIONAL PEOPLE'S CONFERENCE
        $article6 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '6',
            'slug' => 'article-6-national-peoples-conference',
            'title' => 'National People\'s Conference',
            'order' => 6,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article6->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-6-national-peoples-conference'),
            'status' => 'published',
        ]);

        // Article 7: CENTRAL COMMITTEE
        $article7 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '7',
            'slug' => 'article-7-central-committee',
            'title' => 'Central Committee',
            'order' => 7,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article7->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-7-central-committee'),
            'status' => 'published',
        ]);

        // Article 8: THE POLITICAL BUREAU AND THE SECRETARIAT OF THE CENTRAL COMMITTEE
        $article8 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '8',
            'slug' => 'article-8-political-bureau-and-secretariat',
            'title' => 'The Political Bureau and the Secretariat of the Central Committee',
            'order' => 8,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article8->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-8-political-bureau-and-secretariat'),
            'status' => 'published',
        ]);

        // Article 10: NATIONAL AND SUBORDINATE DISCIPLINARY COMMITTEES
        $article10 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '10',
            'slug' => 'article-10-national-and-subordinate-disciplinary-committees',
            'title' => 'National and Subordinate Disciplinary Committees',
            'order' => 10,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article10->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-10-national-and-subordinate-disciplinary-committees'),
            'status' => 'published',
        ]);

        // Article 11: NATIONAL CONSULTATIVE ASSEMBLY
        $article11 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '11',
            'slug' => 'article-11-national-consultative-assembly',
            'title' => 'National Consultative Assembly',
            'order' => 11,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article11->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-11-national-consultative-assembly'),
            'status' => 'published',
        ]);

        // Article 12: THE PROVINCE
        $article12 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '12',
            'slug' => 'article-12-the-province',
            'title' => 'The Province',
            'order' => 12,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article12->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-12-the-province'),
            'status' => 'published',
        ]);

        // Article 13: THE DISTRICT CO-ORDINATING COMMITTEE
        $article13 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '13',
            'slug' => 'article-13-district-coordinating-committee',
            'title' => 'The District Coordinating Committee',
            'order' => 13,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article13->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-13-district-coordinating-committee'),
            'status' => 'published',
        ]);

        // Article 14: THE DISTRICT
        $article14 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '14',
            'slug' => 'article-14-the-district',
            'title' => 'The District',
            'order' => 14,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article14->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-14-the-district'),
            'status' => 'published',
        ]);

        // Article 15: THE BRANCH
        $article15 = Section::create([
            'chapter_id' => $chapter->id,
            'logical_number' => '15',
            'slug' => 'article-15-the-branch',
            'title' => 'The Branch',
            'order' => 15,
            'is_active' => true,
        ]);

        SectionVersion::create([
            'section_id' => $article15->id,
            'version_number' => 1,
            'law_reference' => 'Original ZANU PF Constitution',
            'effective_from' => null,
            'effective_to' => null,
            'body' => ZanuPfConstitutionBodyRepository::body('article-15-the-branch'),
            'status' => 'published',
        ]);
    }
}