<?php

namespace App\Console\Commands;

use App\Models\Chapter;
use App\Models\Section;
use App\Models\SectionVersion;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ImportZimbabweConstitution extends Command
{
    protected $signature = 'constitution:import-zimbabwe
                            {--file= : Path to extracted text file}
                            {--dry-run : Parse and report without writing to database}';

    protected $description = 'Import full Constitution of Zimbabwe from extracted PDF text';

    /** @var array<int, array{num: string, title: string}> */
    private array $chapterMap = [
        0 => ['num' => '0', 'title' => 'Preamble'],
        1 => ['num' => '1', 'title' => 'Founding Provisions'],       // 1-7
        2 => ['num' => '2', 'title' => 'National Objectives'],       // 8-34
        3 => ['num' => '3', 'title' => 'Citizenship'],               // 35-43
        4 => ['num' => '4', 'title' => 'Declaration of Rights'],     // 44-87
        5 => ['num' => '5', 'title' => 'The Executive'],             // 88-114
        6 => ['num' => '6', 'title' => 'The Legislature'],           // 115-163
        7 => ['num' => '7', 'title' => 'Elections'],                 // 164-171
        8 => ['num' => '8', 'title' => 'The Judiciary and Courts'],  // 172-193
        9 => ['num' => '9', 'title' => 'Principles of Public Administration and Leadership'], // 194-198
        10 => ['num' => '10', 'title' => 'Civil Service'],           // 199-205
        11 => ['num' => '11', 'title' => 'Security Services'],       // 206-234
        12 => ['num' => '12', 'title' => 'Independent Commissions Supporting Democracy'],     // 235-260
        13 => ['num' => '13', 'title' => 'Institutions to Combat Corruption and Crime'],     // 261-267
        14 => ['num' => '14', 'title' => 'Provincial and Local Government'], // 268-279
        15 => ['num' => '15', 'title' => 'Traditional Leaders'],     // 280-287
        16 => ['num' => '16', 'title' => 'Agricultural Land'],       // 288-297
        17 => ['num' => '17', 'title' => 'Finance'],                 // 298-327
        18 => ['num' => '18', 'title' => 'General and Supplementary Provisions'], // 328-332
    ];

    /** @var array<int, int> Section number -> chapter index */
    private array $sectionToChapter;

    public function __construct()
    {
        parent::__construct();
        $this->sectionToChapter = $this->buildSectionToChapterMap();
    }

    private function buildSectionToChapterMap(): array
    {
        $map = [];
        $ranges = [
            [0, 0], [1, 7], [8, 34], [35, 43], [44, 87], [88, 114], [115, 163],
            [164, 171], [172, 193], [194, 198], [199, 205], [206, 234], [235, 260],
            [261, 267], [268, 279], [280, 287], [288, 297], [298, 327], [328, 332],
        ];
        $chIndex = 0;
        foreach ($ranges as [$start, $end]) {
            for ($n = $start; $n <= $end; $n++) {
                $map[$n] = $chIndex;
            }
            $chIndex++;
        }
        return $map;
    }

    public function handle(): int
    {
        $path = $this->option('file')
            ?? Storage::path('zimbabwe-constitution-source.txt');

        if (!is_readable($path)) {
            $default = base_path('storage/app/zimbabwe-constitution-source.txt');
            $path = realpath($path) ?: $default;
            if (!is_readable($path)) {
                $this->error('Source file not found. Use --file=/path/to/extracted.txt or place zimbabwe-constitution-source.txt in storage/app/');
                return 1;
            }
        }

        $raw = file_get_contents($path);
        $sections = $this->parseSections($raw);
        $this->info('Parsed ' . count($sections) . ' sections.');

        if ($this->option('dry-run')) {
            foreach (array_slice($sections, 0, 5) as $s) {
                $this->line("Section {$s['num']}: {$s['title']} (" . strlen($s['body']) . ' chars)');
            }
            return 0;
        }

        $slug = 'zimbabwe';
        $created = 0;
        $updated = 0;

        foreach ($sections as $sec) {
            $num = (int) $sec['num'];
            $chIndex = $this->sectionToChapter[$num] ?? null;
            if ($chIndex === null) {
                continue;
            }
            $ch = $this->chapterMap[$chIndex];
            $chapter = Chapter::firstOrCreate(
                ['constitution_slug' => $slug, 'number' => $ch['num'], 'title' => $ch['title']],
                ['part_id' => null, 'order' => $chIndex]
            );
            $section = Section::firstOrCreate(
                ['chapter_id' => $chapter->id, 'logical_number' => (string) $num],
                [
                    'slug' => 'zw-' . $num . '-' . Str::slug($sec['title']),
                    'title' => $sec['title'],
                    'order' => $num,
                    'is_active' => true,
                ]
            );
            $existing = $section->versions()->where('version_number', 1)->first();
            if ($existing) {
                $existing->update(['body' => $sec['body']]);
                $updated++;
            } else {
                SectionVersion::create([
                    'section_id' => $section->id,
                    'version_number' => 1,
                    'law_reference' => 'Constitution of Zimbabwe (2013)',
                    'body' => $sec['body'],
                    'status' => 'published',
                ]);
                $created++;
            }
        }

        $this->info("Created {$created} section versions, updated {$updated}.");
        return 0;
    }

    /**
     * @return array<int, array{num: string, title: string, body: string}>
     */
    private function parseSections(string $raw): array
    {
        $sections = [];

        $preamble = $this->extractPreamble($raw);
        if ($preamble) {
            $sections[] = [
                'num' => '0',
                'title' => 'Preamble',
                'body' => $this->cleanBody($preamble),
            ];
        }

        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $raw));
        $current = null;
        $bodyBuffer = [];

        $flush = function () use (&$current, &$bodyBuffer, &$sections) {
            if ($current !== null) {
                $body = $this->cleanBody(implode("\n", $bodyBuffer));
                if (strlen($body) > 5) {
                    $current['body'] = $body;
                    $sections[] = $current;
                }
            }
        };

        $inContent = false;
        $pastPreamble = false;
        $lastSectionNum = 0;
        foreach ($lines as $line) {
            $line = rtrim($line);
            if (!$pastPreamble && stripos($line, 'fundamental law of our beloved land') !== false) {
                $pastPreamble = true;
            }
            if (!$inContent && $pastPreamble && (stripos($line, 'CHAPTER 1') !== false || preg_match('/^1\.\s+The Republic/', $line))) {
                $inContent = true;
            }
            if (!$inContent) {
                continue;
            }
            if (preg_match('/^Notes\s*$/i', $line)) {
                break;
            }
            if (preg_match('/^(\d+)\.\s+(.+)$/', $line, $m)) {
                $num = (int) $m[1];
                if ($lastSectionNum >= 332) {
                    $flush();
                    break;
                }
                $nextExpected = $lastSectionNum + 1;
                if ($num <= 332 && $num >= $nextExpected) {
                    $flush();
                    $current = ['num' => (string) $num, 'title' => trim($m[2])];
                    $bodyBuffer = [];
                    $lastSectionNum = $num;
                    continue;
                }
            }
            if ($current !== null && !preg_match('/^CHAPTER\s+\d+/i', $line) && !preg_match('/^PART\s+\d+\./i', $line)) {
                $bodyBuffer[] = $line;
            }
        }
        $flush();

        return $sections;
    }

    private function extractPreamble(string $raw): ?string
    {
        if (!preg_match('/We the people of Zimbabwe[\s\S]*?fundamental law of our beloved land\./i', $raw, $m)) {
            return null;
        }
        return $m[0];
    }

    private function cleanBody(string $text): string
    {
        $patterns = [
            '/constituteproject\.org[^\n]*/i',
            '/Zimbabwe 2013 Page \d+[^\n]*/i',
            '/PDF generated: [^\n]*/i',
        ];
        $text = preg_replace($patterns, '', $text);

        $lines = explode("\n", $text);
        $keep = [];
        $annotationStarts = [
            'Right to ', 'Duty to ', 'Protection of ', 'Motives for ', 'Source of ', 'Reference to ',
            'Human dignity', 'General guarantee', 'Binding effect', 'Type of government', 'God or other',
            'National flag', 'National anthem', 'National vs ', 'Official or national', 'Structure of the',
            'Selection of ', 'Designation of ', 'International ', 'Joint meetings', 'Legislative committees',
        ];
        foreach ($lines as $line) {
            $t = trim($line);
            if ($t === '') {
                $keep[] = $line;
                continue;
            }
            $isAnnotation = false;
            foreach ($annotationStarts as $a) {
                if (stripos($t, $a) === 0 && strlen($t) < 80 && !preg_match('/^\d+\./', $t)) {
                    $isAnnotation = true;
                    break;
                }
            }
            if (preg_match('/^[A-Z][a-z]+ [a-z]+( [a-z]+)*\.?$/u', $t) && strlen($t) < 50) {
                $isAnnotation = true;
            }
            if (!$isAnnotation) {
                $keep[] = $line;
            }
        }
        return trim(implode("\n", $keep));
    }
}
