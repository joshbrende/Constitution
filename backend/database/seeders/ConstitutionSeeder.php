<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Section;
use Database\Seeders\Constitution\ZanuPfChapter1Sections;
use Database\Seeders\Constitution\ZanuPfChapter2Sections;
use Database\Seeders\Constitution\ZanuPfChapter3Sections;
use Database\Seeders\Constitution\ZanuPfChapter4Sections;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConstitutionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed ZANU PF constitution chapters and sections (original PDF-aligned content).
     * Section bodies are loaded from database/data/zanupf-constitution/bodies/{slug}.txt.
     */
    public function run(): void
    {
        if (Section::where('slug', 'chapter-1-preamble')->exists()) {
            return;
        }

        $chapterOne = Chapter::create([
            'part_id' => null,
            'number' => '1',
            'title' => 'Preamble',
            'order' => 1,
        ]);

        $chapterTwo = Chapter::create([
            'part_id' => null,
            'number' => '2',
            'title' => 'The Women\'s League',
            'order' => 2,
        ]);

        $chapterThree = Chapter::create([
            'part_id' => null,
            'number' => '3',
            'title' => 'The Youth League',
            'order' => 3,
        ]);

        $chapterFour = Chapter::create([
            'part_id' => null,
            'number' => '4',
            'title' => 'General Provisions',
            'order' => 4,
        ]);

        ZanuPfChapter1Sections::seed($chapterOne);
        ZanuPfChapter2Sections::seed($chapterTwo);
        ZanuPfChapter3Sections::seed($chapterThree);
        ZanuPfChapter4Sections::seed($chapterFour);
    }
}
