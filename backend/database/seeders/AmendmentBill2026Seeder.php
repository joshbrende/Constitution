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

        $sections = $this->getSections();
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

    private function getSections(): array
    {
        return [
            [
                'num' => '0',
                'title' => 'Memorandum',
                'body' => <<<'TEXT'
This Bill introduces a set of constructive reforms that, taken together, reinforce constitutional governance, strengthen democratic structures, clarify institutional mandates, and harmonise Zimbabwe's constitutional order with tested and successful practices in other progressive jurisdictions. The Bill modernises and streamlines various aspects of the constitutional architecture while upholding the values of the 2013 Constitution. The amendments form part of a broader constitutional evolution, one that is grounded in the deliberate refinement of governance frameworks and an increased focus on institutional efficiency, political inclusivity, and long-term national stability.

It must be reiterated that many of the reforms incorporated into this Bill align Zimbabwe with contemporary African constitutional standards that have proven to be effective, resilient, and widely respected.

In detail, the Bill provides as follows—
TEXT,
                'amends' => [],
            ],
            [
                'num' => '1',
                'title' => 'Short title',
                'body' => (string) config('constitution.amendment3_short_title_clause'),
                'amends' => [],
            ],
            [
                'num' => '2',
                'title' => 'Insertion of Section 43A — Registration of Voters',
                'body' => <<<'TEXT'
The Constitution is amended by the insertion after section 43 of the following section—

"43A Registration of Voters, Voters' Rolls and Registers

The Registrar-General shall—

(a) register voters;
(b) compile voters' rolls and registers;
(c) ensure proper custody and maintenance of voters' rolls and registers."
TEXT,
                'amends' => [['num' => '43', 'label' => 'Voter Registration (new Section 43A)', 'type' => 'inserts']],
            ],
            [
                'num' => '3',
                'title' => 'Amendment of Section 92 — Election of President',
                'body' => <<<'TEXT'
Section 92 of the Constitution is amended to replace the direct election of the President with a parliamentary system.

The President shall be elected by Members of Parliament at a joint sitting of the Senate and the National Assembly. Key provisions include:

(1) A candidate must secure more than half of the valid votes cast.

(2) If no candidate obtains an absolute majority, a run-off ballot is held between the two leading candidates.

(3) The election is to be presided over by the Zimbabwe Electoral Commission or a designated judge, in accordance with Parliament's Standing Orders.

(4) Vacancy in the office of President to be filled within 30 days.

(5) No substantive policy amendments may be made during the vacancy period.
TEXT,
                'amends' => [['num' => '92', 'label' => 'Section 92 – Election of President', 'type' => 'amends']],
            ],
            [
                'num' => '4',
                'title' => 'Amendment of Section 95 — Presidential tenure',
                'body' => <<<'TEXT'
Section 95 of the Constitution is amended by substituting "five years" with "seven years" for the Presidential term of office.

The amendment introduces subsection (2a): "Notwithstanding section 328(7), subsection (2)(b) shall apply to the continuation in office of the President." The revised seven-year tenure applies to the continuation in office of incumbents.
TEXT,
                'amends' => [['num' => '95', 'label' => 'Section 95 – Presidential Term', 'type' => 'amends']],
            ],
            [
                'num' => '5',
                'title' => 'Amendment of Section 100',
                'body' => "Section 100 of the Constitution is amended by deleting the word \"first\" before \"Vice President\".",
                'amends' => [['num' => '100', 'label' => 'Section 100 – Acting President', 'type' => 'amends']],
            ],
            [
                'num' => '6',
                'title' => 'Amendment of Section 101 — Succession',
                'body' => <<<'TEXT'
Section 101 of the Constitution is amended to provide that where the President-elect dies, resigns or is removed from office before assuming office, Section 92 (parliamentary election of the President) shall apply. The vacancy is to be filled by a joint sitting of Parliament in accordance with the amended Section 92.
TEXT,
                'amends' => [['num' => '101', 'label' => 'Section 101 – Succession', 'type' => 'amends']],
            ],
            [
                'num' => '7',
                'title' => 'Amendment of Section 114 — Attorney-General',
                'body' => "Section 114 of the Constitution is amended by replacing \"High Court\" with \"Supreme Court\" in the provisions regarding qualifications for appointment as Attorney-General.",
                'amends' => [['num' => '114', 'label' => 'Section 114 – Attorney-General Qualifications', 'type' => 'amends']],
            ],
            [
                'num' => '8',
                'title' => 'Amendment of Section 120 — Senate composition',
                'body' => <<<'TEXT'
Section 120 of the Constitution is amended to increase the total number of Senators from 80 to 90.

The amendment provides for ten Senators to be appointed by the President, chosen for their professional skills and competencies.
TEXT,
                'amends' => [['num' => '120', 'label' => 'Section 120 – Composition of Senate', 'type' => 'amends']],
            ],
            [
                'num' => '9',
                'title' => 'Amendment of Section 143 — Duration of Parliament',
                'body' => <<<'TEXT'
Section 143 of the Constitution is amended by substituting the five-year term of Parliament with a seven-year term.

The amendment applies to the continuation in office of the current Parliament, notwithstanding section 328(7).
TEXT,
                'amends' => [['num' => '143', 'label' => 'Section 143 – Duration of Parliament', 'type' => 'amends']],
            ],
            [
                'num' => '10',
                'title' => 'Amendment of Section 158 — Timing of elections',
                'body' => <<<'TEXT'
Section 158 of the Constitution is amended to align the timing of elections with the seven-year term for President and Parliament. Elections are to be held 30 days before the expiry of the seven-year term. The amendment also removes the reference to "President and" in subsection (2).
TEXT,
                'amends' => [['num' => '158', 'label' => 'Section 158 – Timing of Elections', 'type' => 'amends']],
            ],
            [
                'num' => '11',
                'title' => 'Insertion of Section 159A — Zimbabwe Electoral Delimitation Commission',
                'body' => <<<'TEXT'
The Constitution is amended by the insertion of a new section 159A establishing the Zimbabwe Electoral Delimitation Commission, responsible for drawing electoral boundaries. The Commission shall consist of:

(1) A Chairperson who is qualified for appointment as or eligible to be appointed as a judge of the Supreme Court; and

(2) Four other members with expertise in law, governance, demography or electoral matters.

The Commission is appointed by the President and assumes responsibility for boundary delimitation previously held by the Zimbabwe Electoral Commission.
TEXT,
                'amends' => [['num' => null, 'label' => 'New Section 159A – Delimitation', 'type' => 'inserts']],
            ],
            [
                'num' => '12',
                'title' => 'Amendment of Sections 160 and 161 — Boundary delimitation',
                'body' => <<<'TEXT'
Sections 160 and 161 of the Constitution are amended by substituting references to the Zimbabwe Electoral Commission with the Zimbabwe Electoral Delimitation Commission in all provisions relating to boundary delimitation. The timeframe for delimitation is extended from 6 months to 18 months.
TEXT,
                'amends' => [['num' => '160', 'label' => 'Sections 160 & 161 – Delimitation', 'type' => 'amends'], ['num' => '161', 'label' => 'Sections 160 & 161 – Delimitation', 'type' => 'amends']],
            ],
            [
                'num' => '13',
                'title' => 'Boundary delimitation — further amendments',
                'body' => "Further provisions of the Constitution are amended to substitute references to the Zimbabwe Electoral Commission with the Zimbabwe Electoral Delimitation Commission in all remaining boundary delimitation provisions.",
                'amends' => [['num' => '160', 'label' => 'Delimitation provisions', 'type' => 'amends']],
            ],
            [
                'num' => '14',
                'title' => 'Amendment of Section 167 — Constitutional Court jurisdiction',
                'body' => "Section 167 of the Constitution is amended to expand the jurisdiction of the Constitutional Court to hear any matter involving an arguable point of law of general public importance, in addition to its existing constitutional jurisdiction.",
                'amends' => [['num' => '167', 'label' => 'Section 167 – Jurisdiction of Constitutional Court', 'type' => 'amends']],
            ],
            [
                'num' => '15',
                'title' => 'Amendment of Section 180 — Judicial appointments',
                'body' => <<<'TEXT'
Section 180 of the Constitution is amended by repealing subsections (3), (4), (4a) and (5).

Judges are to be appointed by the President after consultation with the Judicial Service Commission. The requirement for public interviews and JSC recommendation is removed.
TEXT,
                'amends' => [['num' => '180', 'label' => 'Section 180 – Appointment of Judges', 'type' => 'amends']],
            ],
            [
                'num' => '16',
                'title' => 'Amendment of Section 212 — Defence Forces',
                'body' => "Section 212 of the Constitution (Functions of Defence Forces) is amended by replacing the phrase \"and to uphold this Constitution\" with \"in accordance with the Constitution.\"",
                'amends' => [['num' => '212', 'label' => 'Section 212 – Defence Forces', 'type' => 'amends']],
            ],
            [
                'num' => '17',
                'title' => 'Amendment of Section 239 — ZEC functions',
                'body' => <<<'TEXT'
Section 239 of the Constitution is amended by repealing several functions of the Zimbabwe Electoral Commission. The following functions are reassigned:

(a) Voter registration, compilation and maintenance of voters' rolls and registers — transferred to the Registrar-General;

(b) Boundary delimitation and related electoral geography — transferred to the Zimbabwe Electoral Delimitation Commission.
TEXT,
                'amends' => [['num' => '239', 'label' => 'Section 239 – Functions of ZEC', 'type' => 'amends']],
            ],
            [
                'num' => '18',
                'title' => 'Repeal — Zimbabwe Gender Commission',
                'body' => "Part 4 of Chapter 12 of the Constitution is repealed, thereby dissolving the Zimbabwe Gender Commission. The Commission ceases to exist as an independent constitutional commission.",
                'amends' => [['num' => '243', 'label' => 'Chapter 12 Part 4 – Zimbabwe Gender Commission', 'type' => 'repeals']],
            ],
            [
                'num' => '19',
                'title' => 'Amendment of Section 243 — Transfer of gender functions',
                'body' => "Section 243 of the Constitution (Zimbabwe Human Rights Commission) is amended to explicitly include gender equality and gender-related functions among the Commission's mandate. Functions previously performed by the Zimbabwe Gender Commission are transferred to the Zimbabwe Human Rights Commission.",
                'amends' => [['num' => '243', 'label' => 'Section 243 – ZHRC Functions', 'type' => 'amends']],
            ],
            [
                'num' => '20',
                'title' => 'Amendment of Section 259 — Prosecutor-General appointment',
                'body' => "Section 259 of the Constitution is amended to remove the requirement for the President to appoint the Prosecutor-General on the advice of the Judicial Service Commission. The amendment creates a clearer separation between the judiciary and the prosecution.",
                'amends' => [['num' => '259', 'label' => 'Section 259 – Prosecutor-General', 'type' => 'amends']],
            ],
            [
                'num' => '21',
                'title' => 'Amendment of Section 281 — Traditional leaders',
                'body' => "Section 281(2) of the Constitution is repealed. The subsection previously prohibited traditional leaders from being partisan. The code of conduct for traditional leaders will henceforth be governed by an Act of Parliament.",
                'amends' => [['num' => '281', 'label' => 'Section 281(2) – Traditional Leaders', 'type' => 'repeals']],
            ],
            [
                'num' => '22',
                'title' => 'Repeal — National Peace and Reconciliation Commission',
                'body' => "Part 6 of Chapter 12 of the Constitution is repealed, thereby dissolving the National Peace and Reconciliation Commission. The Commission ceases to exist as a constitutional commission.",
                'amends' => [['num' => '251', 'label' => 'Chapter 12 Part 6 – NPRC', 'type' => 'repeals']],
            ],
        ];
    }
}
