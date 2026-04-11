<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Section;
use App\Models\SectionVersion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ZimbabweConstitutionSeeder extends Seeder
{
    public function run(): void
    {
        $slug = 'zimbabwe';

        // Preamble
        $ch0 = Chapter::firstOrCreate(
            ['constitution_slug' => $slug, 'number' => '0', 'title' => 'Preamble'],
            ['part_id' => null, 'order' => 0]
        );

        $pre = Section::firstOrCreate(
            ['chapter_id' => $ch0->id, 'logical_number' => '0'],
            [
                'slug' => 'zw-preamble',
                'title' => 'Preamble',
                'order' => 0,
                'is_active' => true,
            ]
        );

        SectionVersion::firstOrCreate(
            ['section_id' => $pre->id, 'version_number' => 1],
            [
                'law_reference' => 'Constitution of Zimbabwe (2013)',
                'body' => "We the people of Zimbabwe,\n\n"
                    . "United in our diversity by our common desire for freedom, justice and equality, and our heroic resistance to colonialism, racism and all forms of domination and oppression,\n\n"
                    . "Exalting and extolling the brave men and women who sacrificed their lives during the Chimurenga / Umvukela and national liberation struggles,\n\n"
                    . "Honouring our forebears and compatriots who toiled for the progress of our country,\n\n"
                    . "Recognising the need to entrench democracy, good, transparent and accountable governance and the rule of law,\n\n"
                    . "Reaffirming our commitment to upholding and defending fundamental human rights and freedoms,\n\n"
                    . "Acknowledging the richness of our natural resources,\n\n"
                    . "Celebrating the vibrancy of our traditions and cultures,\n\n"
                    . "Determined to overcome all challenges and obstacles that impede our progress,\n\n"
                    . "Cherishing freedom, equality, peace, justice, tolerance, prosperity and patriotism in search of new frontiers under a common destiny,\n\n"
                    . "Acknowledging the supremacy of Almighty God, in whose hands our future lies,\n\n"
                    . "Resolve by the tenets of this Constitution to commit ourselves to build a united, just and prosperous nation, founded on values of transparency, equality, freedom, fairness, honesty and the dignity of hard work,\n\n"
                    . "And, imploring the guidance and support of Almighty God, hereby make this Constitution and commit ourselves to it as the fundamental law of our beloved land.",
                'status' => 'published',
            ]
        );

        $chapters = [
            ['num' => '1', 'title' => 'Founding Provisions', 'sections' => [
                ['num' => '1', 'title' => 'The Republic', 'body' => "Zimbabwe is a unitary, democratic and sovereign republic."],
                ['num' => '2', 'title' => 'Supremacy of Constitution', 'body' => "1. This Constitution is the supreme law of Zimbabwe and any law, practice, custom or conduct inconsistent with it is invalid to the extent of the inconsistency.\n\n2. The obligations imposed by this Constitution are binding on every person, natural or juristic, including the State and all executive, legislative and judicial institutions and agencies of government at every level, and must be fulfilled by them."],
                ['num' => '3', 'title' => 'Founding values and principles', 'body' => "1. Zimbabwe is founded on respect for the following values and principles—\n(a) supremacy of the Constitution;\n(b) the rule of law;\n(c) fundamental human rights and freedoms;\n(d) the nation's diverse cultural, religious and traditional values;\n(e) recognition of the equality of all human beings;\n(f) gender equality;\n(g) good governance;\n(h) recognition of and respect for the liberation struggle.\n\n[Content continues – see Constitution of Zimbabwe (2013) for full text.]"],
                ['num' => '4', 'title' => 'National Flag, National Anthem, Public Seal and Coat of arms'],
                ['num' => '5', 'title' => 'Tiers of government'],
                ['num' => '6', 'title' => 'Languages'],
                ['num' => '7', 'title' => 'Promotion of public awareness of Constitution'],
            ]],
            ['num' => '2', 'title' => 'National Objectives', 'sections' => [
                ['num' => '8', 'title' => 'Objectives to guide State and all institutions and agencies of Government'],
                ['num' => '9', 'title' => 'Good governance'],
                ['num' => '10', 'title' => 'National unity, peace and stability'],
                ['num' => '11', 'title' => 'Fostering of fundamental rights and freedoms'],
                ['num' => '12', 'title' => 'Foreign policy'],
                ['num' => '13', 'title' => 'National development'],
                ['num' => '14', 'title' => 'Empowerment and employment creation'],
                ['num' => '15', 'title' => 'Food security'],
                ['num' => '16', 'title' => 'Culture'],
                ['num' => '17', 'title' => 'Gender balance'],
                ['num' => '18', 'title' => 'Fair regional representation'],
                ['num' => '19', 'title' => 'Children'],
                ['num' => '20', 'title' => 'Youths'],
                ['num' => '21', 'title' => 'Elderly persons'],
                ['num' => '22', 'title' => 'Persons with disabilities'],
                ['num' => '23', 'title' => 'Veterans of the liberation struggle'],
                ['num' => '24', 'title' => 'Work and labour relations'],
                ['num' => '25', 'title' => 'Protection of the family'],
                ['num' => '26', 'title' => 'Marriage'],
                ['num' => '27', 'title' => 'Education'],
                ['num' => '28', 'title' => 'Shelter'],
                ['num' => '29', 'title' => 'Health services'],
                ['num' => '30', 'title' => 'Social welfare'],
                ['num' => '31', 'title' => 'Legal aid'],
                ['num' => '32', 'title' => 'Sporting and recreational facilities'],
                ['num' => '33', 'title' => 'Preservation of traditional knowledge'],
                ['num' => '34', 'title' => 'Domestication of international instruments'],
            ]],
            ['num' => '3', 'title' => 'Citizenship', 'sections' => [
                ['num' => '35', 'title' => 'Zimbabwean citizenship'],
                ['num' => '36', 'title' => 'Citizenship by birth'],
                ['num' => '37', 'title' => 'Citizenship by descent'],
                ['num' => '38', 'title' => 'Citizenship by registration'],
                ['num' => '39', 'title' => 'Revocation of citizenship'],
                ['num' => '40', 'title' => 'Retention of citizenship despite marriage or dissolution of marriage'],
                ['num' => '41', 'title' => 'Citizenship and Immigration Board'],
                ['num' => '42', 'title' => 'Powers of Parliament in regard to citizenship'],
                ['num' => '43', 'title' => 'Continuation and restoration of previous citizenship'],
            ]],
            ['num' => '4', 'title' => 'Declaration of Rights', 'sections' => [
                ['num' => '44', 'title' => 'Application and interpretation of Chapter 4'],
                ['num' => '45', 'title' => 'Fundamental human rights and freedoms'],
                ['num' => '46', 'title' => 'Elaboration of certain rights'],
                ['num' => '47', 'title' => 'Enforcement of fundamental human rights and freedoms'],
                ['num' => '48', 'title' => 'Limitation of fundamental human rights and freedoms'],
            ]],
            ['num' => '5', 'title' => 'The Executive'],
            ['num' => '6', 'title' => 'The Legislature'],
            ['num' => '7', 'title' => 'Elections'],
            ['num' => '8', 'title' => 'The Judiciary and Courts'],
            ['num' => '9', 'title' => 'Principles of Public Administration and Leadership'],
            ['num' => '10', 'title' => 'Civil Service'],
            ['num' => '11', 'title' => 'Security Services'],
            ['num' => '12', 'title' => 'Independent Commissions Supporting Democracy'],
            ['num' => '13', 'title' => 'Institutions to Combat Corruption and Crime'],
            ['num' => '14', 'title' => 'Provincial and Local Government'],
            ['num' => '15', 'title' => 'Traditional Leaders'],
            ['num' => '16', 'title' => 'Agricultural Land'],
            ['num' => '17', 'title' => 'Finance'],
            ['num' => '18', 'title' => 'General and Supplementary Provisions'],
        ];

        $order = 1;
        foreach ($chapters as $ch) {
            if (isset($ch['sections'])) {
                $chapter = Chapter::firstOrCreate(
                    ['constitution_slug' => $slug, 'number' => $ch['num'], 'title' => $ch['title']],
                    ['part_id' => null, 'order' => $order++]
                );
                $secOrder = 0;
                foreach ($ch['sections'] as $sec) {
                    $s = Section::firstOrCreate(
                        ['chapter_id' => $chapter->id, 'logical_number' => $sec['num']],
                        [
                            'slug' => 'zw-' . $sec['num'] . '-' . Str::slug($sec['title']),
                            'title' => $sec['title'],
                            'order' => $secOrder++,
                            'is_active' => true,
                        ]
                    );
                    $body = $sec['body'] ?? 'Content to be added. See Constitution of Zimbabwe (2013), Section ' . $sec['num'] . ' – ' . $sec['title'] . '.';
                    SectionVersion::firstOrCreate(
                        ['section_id' => $s->id, 'version_number' => 1],
                        [
                            'law_reference' => 'Constitution of Zimbabwe (2013)',
                            'body' => $body,
                            'status' => 'published',
                        ]
                    );
                }
            } else {
                // Chapters 5-18: create chapter only; sections 88+ are added by ImportZimbabweConstitution command
                Chapter::firstOrCreate(
                    ['constitution_slug' => $slug, 'number' => $ch['num'], 'title' => $ch['title']],
                    ['part_id' => null, 'order' => $order++]
                );
            }
        }
    }
}
