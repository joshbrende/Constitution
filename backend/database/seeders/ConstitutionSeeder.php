<?php

namespace Database\Seeders;

use App\Models\ArticleAlias;
use App\Models\Chapter;
use App\Models\Section;
use App\Models\SectionVersion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConstitutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // If the core ZANU PF constitution sections are already present, skip reseeding
        // to avoid unique constraint violations on slugs when db:seed is run multiple times.
        if (Section::where('slug', 'chapter-1-preamble')->exists()) {
            return;
        }

        // Basic chapter skeleton from the PDF contents
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

        // Chapter 1 Preamble
        $preamble = Section::create([
            'chapter_id' => $chapterOne->id,
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
            'body' => <<<TEXT
Whereas we the people of Zimbabwe are an African nation with a long, proud history and cultural heritage; and

Whereas we the people of Zimbabwe recognise the importance of God the Almighty and our ancestors in guiding the liberation movements in the execution of the armed struggle; and

Whereas we the people of Zimbabwe acknowledge the role played by our fore-bearers during the First Chimurenga War against colonial occupation; and

Whereas on the 18th April 1980 we the people of Zimbabwe regained our nationhood and joined the family of nations of the world as a sovereign state; and

Whereas we the people of Zimbabwe have always cherished our sovereignty by resisting aggression and foreign domination; and

Whereas we the people of Zimbabwe acknowledge the role played by the Zimbabwe Revolutionary War Fighters of the Second Chimurenga and those who died whilst fighting the colonial enemy and those that are still alive and the fact that they will forever be the custodians of the Zimbabwean revolution and the bedrock upon which the ZANU PF Party will continue building itself from; and

Whereas we the people of Zimbabwe acknowledge the importance of the supreme sacrifice of all Zimbabweans who paid the ultimate price for the liberation of our motherland Zimbabwe; and

Whereas we the people of Zimbabwe acknowledge the role played by Zimbabwean liberation parties and their armies, that is, ZAPU and ZIPRA, and ZANU and ZANLA in the liberation of our country Zimbabwe and in the subsequent formation of the ZANU PF Party after the 22nd December 1987 Unity Accord; and

Whereas ZANU PF and PF-ZAPU have irrevocably committed themselves to unite under one political party; and

Whereas on the 22nd December 1987 the leaders of the two parties of the Patriotic Front, which led the struggle for national liberation and won the support of the vast majority of the people of Zimbabwe in two successive general elections, agreed to unite all the people of Zimbabwe under a single political party; and

Whereas the Unity Accord of the 22nd December 1987 was subsequently approved by the special congresses of ZANU PF and PF-ZAPU respectively held in 1988; and

Whereas we are conscious of the historical links between ZANU PF and PF-ZAPU that culminated in the formation of the Patriotic Front Alliance which was the effective instrument for prosecuting the armed struggle and winning democracy and national independence; and

Whereas we the people of Zimbabwe acknowledge the immense contribution of the Liberation Committee of the then Organisation of African Unity (OAU), friendly and progressive countries like Algeria, Angola, Botswana, Egypt, Ghana, Libya, Mozambique, Nigeria, Tanzania, Zambia, Bulgaria, Cuba, Czechoslovakia, Democratic People’s Republic of Korea, Denmark, German Democratic Republic, Hungary, Norway, People’s Republic of China, Romania, Russia, Sweden, Syria and Yugoslavia, among others, in helping to sustain the struggle, and that it is our beholden duty to complete the empowerment process; and

Whereas we the people of Zimbabwe acknowledge the role played by the masses, parents and the war collaborators towards the liberation of Zimbabwe; and

Whereas we the people of Zimbabwe acknowledge the role played by the detainees and restrictees in the fight against colonialism and other forms of injustice during our liberation struggle; and

Whereas we acknowledge the importance of the youths as the future building block and vanguard of the Party; and

Whereas we are desirous to unite our nation permanently, preserve peace, order and good government, guarantee political stability, social and economic development; and

Whereas we are convinced that political stability, peace, order and good government, social and economic development can only be achieved under conditions of national unity; and

Whereas we the people of Zimbabwe desire to preserve and consolidate national independence for all time, to build a united, progressive, permanent political and social order;

Now therefore we the representatives of the people of Zimbabwe in Congress assembled and now united and reconstituted under the name ZANU PF do hereby adopt and grant unto ourselves this Constitution.
TEXT,
            'status' => 'published',
        ]);

        // Article 1: THE PARTY
        $article1 = Section::create([
            'chapter_id' => $chapterOne->id,
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
            'body' => <<<TEXT
Name of the Party
1. The name of the Party shall be the Zimbabwe African National Union Patriotic Front [hereinafter referred to as "ZANU PF" or "the Party"].

2. The Party shall be a body corporate with perpetual succession, capable of suing and being sued, acquiring and disposing of property, acquiring rights and incurring obligations and engaging in any lawful activities which are not inconsistent with its aims and objectives.

Official Seal
3. There shall be a seal of the Party which shall be kept by the President and First Secretary and shall be used under the direction and control of the President of the Party.

Official Flag
4. The Party shall have an official flag as approved by Congress, comprising of the colours of green, yellow, red and black.
Green - represents the vegetation, the life, flora and fauna, agriculture, growth, hope of continuity as a sovereign people.
Yellow - represents Zimbabwe's abundant mineral wealth, such as diamonds, platinum, gold, coal, methane gas, nickel, copper, iron, chrome, emeralds and others.
Red - represents the blood of the sons and daughters of the liberation struggle.
Black - represents the indigenous people as the sovereign owners and custodians of our motherland Zimbabwe.

Party Headquarters
5. The Head Office of the Party shall be in Harare.

Vision
6. Forever to remain the mass revolutionary socialist Party in the emancipation process of the people of Zimbabwe from all forms of oppression.

Mission
7. To maintain total ownership, control and equitable distribution of the means of production, natural resources, and the defence of the national sovereignty, policies of indigenisation, empowerment and wealth creation so as to sustain the independence of Zimbabwe; and to remain forever masters of our own destiny.
TEXT,
            'status' => 'published',
        ]);

        // Article 2: AIMS AND OBJECTIVES
        $article2 = Section::create([
            'chapter_id' => $chapterOne->id,
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
            'body' => <<<TEXT
The aims and objectives of the Party shall be:-

8. To preserve and defend the National Sovereignty and Independence of Zimbabwe.

9. To create conditions for the establishment of a democratic, political and social order which shall guarantee in perpetuity that the Government of the State shall be answerable to the people through periodic free and fair elections based on universal adult suffrage.

10. To uphold and apply fully the rule of equality before the law, and equality of opportunities for all the people in Zimbabwe, regardless of race, tribe, sex, religion or origin.

11. To establish and sustain a socialist society firmly based on our historical, cultural and social experience and to create conditions for economic independence, prosperity and equitable distribution of the wealth of the nation in a system of economic organisation and management in which elements of free enterprise and market economy, planned economy and public ownership are combined.

12. To continue to participate in the worldwide struggle for the complete eradication of imperialism, colonialism and all forms of racism. Accordingly the Party shall support liberation movements in their just struggle for self determination and social justice.

13. To support and promote all efforts for the attainment of the Pan-African goal for the complete independence and unity of African states.

14. To oppose resolutely tribalism, regionalism, nepotism, corruption, racism, religious fanaticism, xenophobia and related intolerance, discrimination on grounds of sex and all forms of exploitation of man by man in Zimbabwe.

15. To oppose resolutely homosexuality and same sex marriage relationships.
TEXT,
            'status' => 'published',
        ]);

        // Article 3: MEMBERSHIP
        $article3 = Section::create([
            'chapter_id' => $chapterOne->id,
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
            'body' => <<<TEXT
16. Membership of the Party shall be open to:-
    (1) all citizens and residents of Zimbabwe who subscribe to the Constitution, aims, objectives and policies of the Party; and
    (2) any organisation, association or society whose members are citizens or residents of Zimbabwe and whose aims and objectives are not inconsistent with those of the Party.

17. In order to become a member, a person shall make application:-
    (1) to the local branch nearest to the place he or she is ordinarily resident or working;
    (2) in exceptional circumstances, to the Politburo through the Secretary for Administration, and any person whose application has been rejected may, through the Secretary for Administration, appeal to the Central Committee whose decision shall be final.

18. An organisation may acquire affiliate membership of the Party by making application to the Secretary for Administration.

19. On acceptance, a member shall pay the joining fee.

Rights of Members
20. Every member of the Party shall have the right:
    (1) to vote in any Party elections in accordance with such rules and regulations as the Central Committee shall determine from time to time;
    (2) to be elected to any office in the Party, subject to such rules and regulations as the Central Committee shall determine from time to time;
    (3) to have audience with any officer of the Party;
    (4) to make representations to any officer or organ of the Party in respect of any matter which affects his or her rights as a member;
    (5) to participate in meetings and other activities organised by the Party;
    (6) not to be subjected to arbitrary or vexatious treatment by those in authority over him or her;
    (7) to seek a remedy in respect of any grievance as a result of the action of any person in authority over him or her.

Duties of Members
21. Every member of the Party shall have the duty:-
    (1) to be loyal to the Party;
    (2) to observe and abide by the policies, rules and regulations of the Party;
    (3) to strive continuously to raise the level of his or her own political and social consciousness and understanding of Party policies;
    (4) to strengthen, promote and defend the Party and popularise its policies among the people;
    (5) to conduct himself or herself honestly and honourably in his or her dealings with the Party and the public and not to bring the Party into disrepute or ridicule; and
    (6) to pay regular subscriptions.
TEXT,
            'status' => 'published',
        ]);

        // Article 4: PRINCIPAL ORGANS AND STRUCTURE OF THE PARTY
        $article4 = Section::create([
            'chapter_id' => $chapterOne->id,
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
            'body' => <<<TEXT
Principal Organs
22. (1) There shall be the following principal organs of the Party:-
    (1) The National People's Congress;
    (2) The National People's Conference;
    (3) The Central Committee;
    (4) The National Consultative Assembly;
    (5) The National Assembly of the Women's League;
    (6) The National Assembly of the Youth League;
    (7) The Provincial Coordinating Committees;
    (8) The Provincial Executive Council;
    (9) The District Committees;
    (10) The Branch Committees;
    (11) The Cell/Village Committees.

    (2) Women shall constitute at least one-third of the total membership of the principal organs of the Party referred to in sections 22(3), 22(6), 22(7), 22(8), 22(9), 22(10) and 22(11).
TEXT,
            'status' => 'published',
        ]);

        // Article 5: NATIONAL PEOPLE'S CONGRESS
        $article5 = Section::create([
            'chapter_id' => $chapterOne->id,
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
            'body' => <<<TEXT
National People's Congress
23. There shall be a National People's Congress (hereinafter referred to as "Congress") which shall be the supreme organ of the Party and shall be composed of:-
    (1) members of the Central Committee;
    (2) members of the National Consultative Assembly;
    (3) members of the Women's League;
    (4) members of the National Council of Youth League;
    (5) members of the Provincial Coordinating Committees;
    (6) members of the various Provincial Executive Councils; and
    (7) unless otherwise directed by the Central Committee, the Chairperson, Vice Chairperson, Secretary, Political Commissar and Treasurer, two members from the Women's League and two members from the Youth League from every District Executive Council of the Party.

Powers and Functions
24. Congress shall:-
    (1) be the supreme policy-making organ of the Party;
    (2) elect the President and First Secretary;
    (3) elect members of the Central Committee;
    (4) formulate, pronounce and declare all policies of the Party;
    (5) formulate and issue directives, rules and regulations to all organs of the Party;
    (6) approve the financial statements of accounts;
    (7) be the supreme and ultimate authority for the implementation and supervision of the policies, directives, rules and regulations of the Party; and
    (8) have the power and authority to amend the Party Constitution.

Convening of Congress
25. Congress shall convene in ordinary session once every five years and the following provisions shall apply:-
    (1) the Secretary for Administration shall be the Secretary of the Presidium;
    (2) the Secretary for Administration shall, at least three months before the due date, send notices convening Congress to all members, which notice shall state the date and venue of Congress;
    (3) resolutions and decisions of Congress other than Constitutional amendments shall be passed by a simple majority;
    (4) resolutions emanating from the constituent organs of Congress for consideration at Congress shall be forwarded to the Secretary for Administration two months prior to the date of Congress;
    (5) the said resolutions shall be circulated to the said constituent organs of Congress at least fourteen days prior to the date of Congress;
    (6) there shall be a Presidium consisting of the President and First Secretary, two Vice Presidents and Second Secretaries and the National Chairperson, who shall preside over proceedings of Congress as directed by the President and First Secretary of the Party; provided that following a dissolution of the Central Committee immediately preceding the election of a new Central Committee in terms of section 32 of this Constitution, the Presidium established under this section shall continue in office until the conclusion of the business of Congress;
    (7) the Central Committee shall formulate the necessary procedures for the execution of the business of Congress; and
    (8) half of the total membership of Congress shall form a quorum.

Extraordinary Congress
26. An extraordinary session of Congress may be convened-
    (1) whenever it is deemed necessary and at the instance of:-
        (a) the majority of the members of the Central Committee; or
        (b) the President and First Secretary, at the instance of not less than one third of members of the Central Committee; or
        (c) the President and First Secretary, at the instance of at least five Provincial Executive Councils by resolutions to that effect;
    (2) in the event of a vacancy occurring in the office of National President requiring the convening of an extraordinary session of Congress.

27. The President and First Secretary, on receipt of a resolution in terms of section 26(1) requesting an extraordinary session of Congress, shall forward the same to the Secretary for Administration.

28. The Secretary for Administration shall, on receipt of the said resolution, give at least six weeks notice convening an extraordinary session of Congress.

29. The Central Committee shall formulate the necessary procedures for the execution of the business of the extraordinary session of the Congress.

30. The extraordinary session of Congress shall deliberate only on those matters for which it has been specifically convened.

31. Three-quarters of the members of Congress shall form a quorum for the convening of the extraordinary session.
TEXT,
            'status' => 'published',
        ]);

        // Article 6: NATIONAL PEOPLE'S CONFERENCE
        $article6 = Section::create([
            'chapter_id' => $chapterOne->id,
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
            'body' => <<<TEXT
National People's Conference
Composition
32. There shall be a National People's Conference (hereinafter called "the People's Conference") composed of:-
    (1) members of the Central Committee;
    (2) members of the National Consultative Assembly;
    (3) members of the National Council of the Women's League;
    (4) members of the National Council of the Youth League;
    (5) members of the Provincial Coordinating Committees;
    (6) members of the Provincial Councils.

Powers and Functions of the National People's Conference
33. The powers and functions of the National People's Conference shall be:-
    (1) to receive and consider reports of the Central Committee on behalf of Congress;
    (2) to co-ordinate and supervise the implementation of decisions and programmes of Congress by the Central Committee;
    (3) to declare the President of the Party elected at Congress as the State Presidential Candidate of the Party;
    (4) to exercise any such powers and authority as may be incidental thereto;
    (5) to make resolutions for implementation by the Central Committee.

Sessions of the National People's Conference
34. The sessions of the People's Conference shall be:-
    (1) convened once every year in ordinary sessions or at any time in special or extraordinary session;
    (2) conducted as laid down for Congress hereunder.
TEXT,
            'status' => 'published',
        ]);

        // Article 7: CENTRAL COMMITTEE
        $article7 = Section::create([
            'chapter_id' => $chapterOne->id,
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
            'body' => <<<TEXT
Central Committee
35. There shall be a Central Committee which shall be the principal organ of the Party in between Congress and shall consist of three hundred (300) members as follows:-
    (1) Four (4) members being:-
        (a) President and First Secretary, nominated by at least two Provinces and elected nationally by Party members for his or her probity, integrity and commitment to the Party, its ideology, values, principles and policies;
        (b) Two (2) Vice Presidents and Second Secretaries, appointed in accordance with the Unity Accord by the President and First Secretary for their skill, experience, probity, integrity and commitment to the Party, its ideology, values, principles and policies;
        (c) The National Chairperson of the Party, appointed by the President and First Secretary for his or her skill, experience, probity, integrity and commitment to the Party, its ideology, values, principles and policies.

36.
    (2) Ninety-four (94) members who shall be allocated to the Provinces in such a way and manner that each Province shall have a proportionate quota or number having regard to the results of the preceding General Election and/or as may be determined by the Central Committee from time to time.
    Provided that the respective Provincial Coordinating Committees shall nominate the candidates in an equitable way, in such a way that each Administrative District shall have at least one member elected to the Central Committee.
    (3) One hundred (100) members allocated on equal basis to Provinces;
    (4) The Secretary for Women's League;
    (5) The Secretary for Youth Affairs;
    (6) Twenty (20) members representing the Women's League nominated by the League at the National Women's Conference;
    (7) Twenty (20) members representing the Youth League nominated at the National Youth Conference;
    (8) Ten (10) members nominated by the President and First Secretary on account of their outstanding contribution to either the armed liberation struggle of the country and/or its development after Independence;
    (9) Fifty (50) members who shall be women allocated to the Provinces in such a way and manner that each Province shall have five members.

    For the avoidance of doubt, each Provincial Coordinating Committee shall act as the electoral college for the purpose of arriving at the nominations referred to in section 32(1).
    The manner in which candidates shall be nominated by the Provincial Coordinating Committees in terms of section 32(2) and 32(3) shall be guided by rules and regulations determined by the Central Committee from time to time.

    Any member of the Party other than Provincial Chairmen, who is elected or appointed a member of the Central Committee shall automatically cease to hold office in any subordinate organ of the Party. Where a vacancy occurs in the organ as a result of this provision, a by-election or co-option, as the case may be, shall be held or done to fill that vacancy.

Powers and Functions of the Central Committee
37. The Central Committee, being the highest organ of the Party in-between Congress and acting on behalf of Congress when Congress is not in session, shall have full plenary unfettered powers to:-
    (1) make rules, regulations and procedures to govern the conduct of the Party and its members;
    (2) implement all policies, resolutions, directives, decisions and programmes enunciated by Congress;
    (3) give directions, supervise and superintend all the functions of the Central Government in relation to the programmes as enunciated by Congress;
    (4) set up Committees, Institutions, Commissions and Enterprises in the name and on behalf of the Party;
    (5) convene Congress in ordinary and/or extraordinary session;
    (6) formulate the agenda, procedures and regulations for business of Congress;
    (7) meet once every three months in ordinary session or at any time in special or extraordinary sessions;
    (8) amend the Constitution, if deemed necessary, subject to ratification by Congress.

Sessions of Central Committee
38. The President and First Secretary of the Party or, in his or her absence, one of the Vice Presidents and Second Secretaries or the National Chairperson shall preside over the meeting of the Central Committee and at such a meeting:-
    (1) decisions of the Central Committee shall be by simple majority; and
    (2) a majority of the total membership shall form a quorum.
TEXT,
            'status' => 'published',
        ]);

        // Article 8: THE POLITICAL BUREAU AND THE SECRETARIAT OF THE CENTRAL COMMITTEE
        $article8 = Section::create([
            'chapter_id' => $chapterOne->id,
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
            'body' => <<<TEXT
The Political Bureau and the Secretariat of the Central Committee
39. There shall be a Political Bureau (hereafter referred to as "Politburo") which shall consist of the President and First Secretary of the Party, the two (2) Vice Presidents and Second Secretaries, the National Chairperson, the Heads of Departments and five (5) committee members appointed by the President and First Secretary under section 40. The President and First Secretary may, at his or her discretion, appoint any or all of the Heads of Departments listed below. If in the opinion of the President and First Secretary it is desirable to create additional departments to the ones listed below, he or she shall at his or her discretion add such departments and appoint Heads to the departments so created.
    (1) The Secretary for Administration;
    (2) The Secretary for Finance;
    (3) The Secretary for Commissariat;
    (4) The Secretary for External Relations;
    (5) The Secretary for National Security;
    (6) The Secretary for Transport and Social Welfare;
    (7) The Secretary for Information and Publicity;
    (8) The Secretary for Legal Affairs;
    (9) The Secretary for Indigenisation and Economic Empowerment;
    (10) The Secretary for Production and Labour;
    (11) The Secretary for Health and Child Welfare and the Elderly;
    (12) The Secretary for Economic Affairs;
    (13) The Secretary for Women's League;
    (14) The Secretary for Youth Affairs;
    (15) The Secretary for Education;
    (16) The Secretary for Gender and Culture;
    (17) The Secretary for Welfare of the Disabled and Disadvantaged Persons;
    (18) The Secretary for Land Reform and Resettlement;
    (19) The Secretary for Science and Technology;
    (20) The Secretary for Business Liaison and Development;
    (21) The Secretary for Environment and Tourism.

Appointment of Members of the Politburo and the Deputy Heads of Departments
40. Soon after the election of the President and First Secretary and members of the Central Committee, the President and First Secretary of the Party shall, during the sitting of the Congress, appoint from the newly elected Central Committee, two (2) Vice Presidents and Second Secretaries, the National Chairperson, the Heads of Departments of the Politburo, the committee members of the Politburo and the deputies to the Heads of Departments.

41. The deputies to the Heads of Departments of the Politburo shall not sit in the Politburo.

Powers and Functions of the Politburo
42. The Politburo shall be the executive committee of the Central Committee and shall have the following powers and functions:-
    (1) act as the administrative organ of the Central Committee;
    (2) implement all decisions, directives, rules and regulations of the Central Committee;
    (3) be answerable to the Central Committee on all matters;
    (4) meet at least once a month in ordinary session;
    (5) meet in special session as determined by the President and First Secretary.

Meetings and Sessions of the Politburo
43. During the meetings and sessions of the Politburo:-
    (1) the President and First Secretary shall preside over the meeting of the Politburo provided that, in his or her absence, one of the Vice Presidents and Second Secretaries shall preside;
    (2) decisions of the Politburo shall be by simple majority;
    (3) the President and First Secretary or Chairperson shall have a casting vote;
    (4) two-thirds of the total membership of the Politburo shall constitute a quorum.
TEXT,
            'status' => 'published',
        ]);

        // Article 10: NATIONAL AND SUBORDINATE DISCIPLINARY COMMITTEES
        $article10 = Section::create([
            'chapter_id' => $chapterOne->id,
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
            'body' => <<<TEXT
National and Subordinate Disciplinary Committees
63. There shall be established a National Disciplinary Committee of the Central Committee which shall have jurisdiction of first instance, appeal and review, comprising:-
    (1) the Party National Chairman who shall be Chairman of the Committee;
    (2) four other members of the Central Committee appointed by the Presidency for their ability, probity and integrity.

64. The National Disciplinary Committee may confirm, amend and reverse the decision of any lower disciplinary committee and may direct that any disciplinary proceedings shall be transferred to it without being completed or commenced by any subordinate Disciplinary Committee.

65. The National Disciplinary Committee shall conduct hearings informally but having proper regard to the principles of natural justice.

66. The National Disciplinary Committee shall submit a report to the Central Committee on each case so adjudicated.

67. The report shall include a summary of the evidence, the conclusion reached and penalty imposed.

68. The Central Committee may, on appeal or review, confirm, amend or reverse the decision of the National Disciplinary Committee.

69. A member shall have the right to be assisted or represented in the conduct of his or her case by any member of his or her own choice.

Ad Hoc Appeals Committee of Congress
70. During the sitting of Congress:-
    (1) Congress may create an Ad Hoc Appeals Committee to determine any appeals against the determination of the National Disciplinary Committee and any determination of the Central Committee itself.
    (2) The Ad Hoc Appeals Committee shall be composed of the following members:-
        (a) two from the Legal Affairs Committee, who are legally qualified where possible;
        (b) one from the Department of the Commissariat;
        (c) one from the National Disciplinary Committee;
        (d) one from the Department of National Security;
        (e) one from the Women's League; and
        (f) one from the Youth League;
        (g) one of the Vice Presidents and Second Secretaries who shall chair the Ad Hoc Appeals Committee of the Congress.

Provincial, District and Branch Disciplinary Committees
71. There shall be established Disciplinary Committees at the Branch, District and Provincial levels of the Party whose composition shall be as follows:-
    (1) the Vice Chairperson of the Branch, District or Province who shall be Chairperson of the Committee respectively;
    (2) the Political Commissar;
    (3) three other members of the Branch, District or Province appointed by Branch, District or Provincial Committees as the case may be, for their ability, probity and integrity.

72. Members of the District Coordinating Committee shall be subject to the discipline of the Disciplinary Committee of the Province.

73. All Disciplinary Committees of the Party shall conduct their hearings informally, but having proper regard to the principles of natural justice.

74. (1) Any member of the Party against whom disciplinary action is intended to be taken shall first be issued with a prohibition order and notice of charges in writing, for a period not exceeding fourteen (14) days in respect of the Branch and District and twenty-one (21) days in respect of the Province. The notice of charges shall state the charges and the date and venue of the hearing of the case by the appropriate disciplinary organ. The notice shall also inform the member being charged of his or her constitutional rights under Article 10 section 69.
    (2) After the hearing of the matter, each respective Disciplinary Committee shall hand down its decision in writing.
        Provided that the prohibition order against any member shall cease if the hearing does not take place as provided under this section unless, for good cause, an extension of time has been sought from the Chairperson of the Disciplinary Committee of the next superior organ before the time stipulated in the Constitution has elapsed.

75. Any member of the Party who has been found guilty of any disciplinary offences prescribed by the Constitution, rules and regulations shall be liable to any one or more of the following punishments:-
    (1) oral reprimand;
    (2) written reprimand;
    (3) a fine;
    (4) suspension or removal from holding any of the offices in the Party;
    (5) expulsion from membership of the Party.

76. Where the member has lodged his or her appeal timeously and the Appeals Committee has failed, without good cause, to hear the appeal, the charges shall fall away and may not be revived without the sanction of the Chairperson of the next superior organ.
    Provided that it shall not be competent for inferior Disciplinary Committees of the Party to expel a member, save only that they may, where deemed appropriate, recommend to the National Disciplinary Committee the expulsion of any member found guilty of a serious offence.

77. For the avoidance of doubt only the National Disciplinary Committee shall have the power to expel a member from the Party.

Appeals from decisions of Disciplinary Committees at Branch, District and District Coordinating Committee levels
78. In the case of appeals, the following provisions shall apply:-
    (1) any appeal against the decision of the Branch or District Disciplinary Committees or District Coordinating Committee in respect of minor offences attracting punishment provided for in subsections (1), (2) and (3) of section 75 shall lie to the District Committee or District Coordinating Committee or Provincial Disciplinary Committee, as the case may be. In all cases referred to under subsections (1), (2) and (3) of section 75, and subject to section 64 of Article 10, the decision of the Provincial Disciplinary Committee shall be final;
    (2) save as provided under section 75 above, in all serious offences attracting punishment provided for under subsection (4) and recommendation for the expulsion from membership of the Party as provided under subsection (5) of section 75, appeals from the Branch Disciplinary Committee shall lie to the District Disciplinary Committee and appeals from the District Disciplinary Committee shall lie to the District Coordinating Committee and from the District Coordinating Committee shall lie to the Provincial Disciplinary Committee and only appeals from the Provincial Disciplinary Committee shall lie to the National Disciplinary Committee of the Central Committee;
    (3) appeals from the decisions of the National Disciplinary Committee shall lie in the first instance to the Central Committee and thereafter to the Ad Hoc Appeals Committee of Congress whose decisions shall be final;
    (4) any appeal against the decision of an inferior Disciplinary Committee shall be lodged with the Chairperson of the immediate superior Disciplinary Committee within fourteen days of the decision of the inferior Disciplinary Committee and such an appeal shall be heard and disposed of by such superior Disciplinary Committee within thirty days of the noting of the appeal;
    (5) any appeal against the decision of the Provincial Disciplinary Committee shall be lodged with the Chairperson of the National Disciplinary Committee within sixty days of the decision of the Provincial Disciplinary Committee and such appeal shall be heard and disposed of by the National Disciplinary Committee within ninety days of the noting of the appeal.
TEXT,
            'status' => 'published',
        ]);

        // Article 11: NATIONAL CONSULTATIVE ASSEMBLY
        $article11 = Section::create([
            'chapter_id' => $chapterOne->id,
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
            'body' => <<<TEXT
National Consultative Assembly
Membership of National Consultative Assembly
79. There shall be a National Consultative Assembly of the Party comprising:-
    (1) members of the Central Committee;
    (2) members of the National Assembly of the Women's League and their deputies;
    (3) members of the National Assembly of the Youth League and their deputies;
    (4) members of the ten Provincial Executive Councils;
    (5) such other members designated by the Central Committee on account of their contribution to the liberation struggle or development of the country after Independence;
    (6) former members of the Central Committee.

Powers and Functions
80. The powers and functions of the National Consultative Assembly shall be:-
    (1) to receive, hear and debate any major matters of policy as the President and First Secretary or the Central Committee shall from time to time determine;
    (2) to make recommendations to the Central Committee on any matter of policy relating to the Party or Government.

Sessions of the National Consultative Assembly
81. The President and First Secretary, at the instance of the Central Committee, shall cause to be convened the National Consultative Assembly at least twice a year.
TEXT,
            'status' => 'published',
        ]);

        // Article 12: THE PROVINCE
        $article12 = Section::create([
            'chapter_id' => $chapterOne->id,
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
            'body' => <<<TEXT
The Province

Provincial Coordinating Committee
82. There shall be a Provincial Coordinating Committee composed of:-
    (a) the Provincial Executive Council;
    (b) members of the Central Committee in the Province;
    (c) members of the National Consultative Assembly in the Province;
    (d) the Provincial Executive Committee of the Women's League;
    (e) the Provincial Executive Committee of the Youth League;
    (f) Party Members of Parliament from the Province;
    (g) the Chairpersons of District Coordinating Committees from the Province.

Powers and Functions
83. (1) The Provincial Coordinating Committee shall be chaired by the Chairperson of the Province and shall meet once every three months or as the situation may demand from time to time at the instance of either the Chairperson, the Provincial Executive Council, or at least one third of the members of the Central Committee and the National Consultative Assembly in the Province.
    (2) The functions of the Provincial Coordinating Committee shall be:-
        (a) to act as the Elections Directorate of the Province;
        (b) to monitor and recommend any political or development programmes and initiatives in the Province;
        (c) to foster an integrated approach to provincial issues between the Party, Government and non-governmental organisations.

The Joint Provincial Council
84. There shall be a Joint Provincial Council in each Province composed of:-
    (1) the Provincial Executive Council of the Party;
    (2) the Provincial Committee of the Women's League;
    (3) the Provincial Committee of the Youth League.

85. The main functions of the Joint Provincial Council shall be to co-ordinate the affairs of the three wings of the Party within the Province.

86. The Joint Provincial Council shall meet at least twice a year.

87. The Provincial Chairperson shall preside over the deliberations of the Joint Provincial Council.

88. A majority of the total membership shall form a quorum.

The Provincial Executive Council
89. There shall be a Provincial Executive Council for each Province which shall consist of forty-four (44) members as follows:-
    (1) the Chairperson;
    (2) the Vice Chairperson;
    (3) the Secretary;
    (4) the Secretary for Finance;
    (5) the Secretary for Commissariat;
    (6) the Secretary for Security;
    (7) the Secretary for Transport and Social Welfare;
    (8) the Secretary for Information and Publicity;
    (9) the Secretary for Legal Affairs;
    (10) the Secretary for Indigenisation and Economic Empowerment;
    (11) the Secretary for Production and Labour;
    (12) the Secretary for Health and Child Welfare;
    (13) the Secretary for Economic Affairs;
    (14) the Secretary for Education;
    (15) the Secretary for Gender and Culture;
    (16) the Secretary for Welfare of the Disabled and the Disadvantaged Persons;
    (17) the Secretary for Land Reform and Resettlement;
    (18) two (2) committee members;
    (19) five (5) members being the Chairperson, Vice Chairperson, Secretary, Treasurer and Commissar of the Provincial Women's League;
    (20) five (5) members being the Chairperson, Vice Chairperson, Secretary, Treasurer and Commissar of the Provincial Youth League;
    (21) fifteen (15) Vice Secretaries for offices listed in subsections (3) to (17) above.

90. Members of the Provincial Executive Council shall be elected for a maximum period of four years at a Provincial Conference specially convened for the purpose by delegates representing each of the Party Districts in the Province.

91. Any person holding executive office in the lower organs of the Party, namely the Branch, District Executive Council or District Coordinating Committee, and who is elected a member of the Provincial Executive Council shall, on being so elected, automatically cease to be an executive member of the Branch or District Committee, as the case may be.

92. The number of delegates from each District to the Provincial Conference shall be determined from time to time by the Central Committee.

93. The Provincial Executive Council shall hold the Provincial Conference at least twelve months before Congress.

94. A majority of the membership of the Provincial Executive Council shall constitute a quorum.

Powers and Functions
95. (1) The Provincial Executive Council shall be responsible for:-
        (a) the implementation of the Party decisions, directives, rules and regulations; and
        (b) the organisation of public meetings and provincial rallies of the Party.
    (2) The Provincial Executive Council shall meet at least once every month.

96. Each of the Provincial Heads of Departments shall exercise the functions specified in relation to their office.
TEXT,
            'status' => 'published',
        ]);

        // Article 13: THE DISTRICT CO-ORDINATING COMMITTEE
        $article13 = Section::create([
            'chapter_id' => $chapterOne->id,
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
            'body' => <<<TEXT
The District Coordinating Committee

114. There shall be a District Coordinating Committee in each and every administrative district of each Province.

115. The following shall comprise the District Coordinating Committee:-
    (1) the Chairperson;
    (2) the Vice Chairperson;
    (3) the Secretary for Administration;
    (4) the Secretary for Finance;
    (5) the Secretary for Commissariat;
    (6) the Secretary for Security;
    (7) the Secretary for Transport and Social Welfare;
    (8) the Secretary for Information and Publicity;
    (9) the Secretary for Indigenisation and Economic Empowerment;
    (10) the Secretary for Production and Labour;
    (11) the Secretary for Women's Affairs;
    (12) the Secretary for Youth Affairs;
    (13) the Secretary for Land Reform and Resettlement;
    (14) all members of the Central Committee from that District;
    (15) all members of the National Consultative Assembly from that District;
    (16) all members of the Provincial Executive Council from that District;
    (17) all Party Members of Parliament from that District;
    (18) the Chairperson of the War Veterans Association in that District;
    (19) the Chairperson of the Zimbabwe Ex-Political Prisoners and Restrictees Association (ZEPPEDRA) in that District;
    (20) the Chairperson of the War Collaborators Association in that District.

116. The District Coordinating Committee shall be elected by such number of delegates, as may be determined by the Central Committee from time to time, from the Party Districts in each administrative district every three years at a conference called for that purpose.

117. The main function of the District Coordinating Committee shall be to coordinate the activities of the Party Districts in each administrative district.
TEXT,
            'status' => 'published',
        ]);

        // Article 14: THE DISTRICT
        $article14 = Section::create([
            'chapter_id' => $chapterOne->id,
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
            'body' => <<<TEXT
The District

118. There shall be a District Executive Committee for each District of the Province which shall consist of all branches within the District.

119. Members of the District Executive Committees shall be elected to hold office for a maximum period of three years at a District Conference specially convened for the purpose by delegates representing each of the branches in the District.

120. The District Executive Council shall consist of forty-four (44) members constituted as follows:-
    (1) the Chairperson;
    (2) the Vice Chairperson;
    (3) the Secretary for Administration;
    (4) the Secretary for Finance;
    (5) the Secretary for Commissariat;
    (6) the Secretary for Security;
    (7) the Secretary for Transport and Social Welfare;
    (8) the Secretary for Information and Publicity;
    (9) the Secretary for Legal Affairs;
    (10) the Secretary for Indigenisation and Economic Empowerment;
    (11) the Secretary for Production and Labour;
    (12) the Secretary for Health and Child Welfare;
    (13) the Secretary for Economic Affairs;
    (14) the Secretary for Education;
    (15) the Secretary for Gender and Culture;
    (16) the Secretary for Welfare of the Disabled and Disadvantaged Persons;
    (17) the Secretary for Land Reform and Resettlement;
    (18) two (2) committee members;
    (19) five (5) members being the Chairperson, Vice Chairperson, Secretary, Secretary for Finance and Secretary for Commissariat of the District Women's League;
    (20) five (5) members being the Chairperson, Vice Chairperson, Secretary, Secretary for Finance and Secretary for Commissariat of the District Youth League;
    (21) fifteen (15) Vice Secretaries for posts referred to in subsections (3) to (17) above.

121. Any member holding an executive office in the Branch who is elected a member of the District Executive Committee shall, on being so elected, automatically cease to be an executive member of the Branch Committee.

122. The number of delegates from each Branch to the District Conference shall be determined by the Central Committee from time to time.

123. The District Executive Committee shall hold the District Conference at least twelve months before the Provincial Conference.

124. The District Executive Council shall meet at least once every month.

125. A majority of the membership of the District Executive Council shall constitute a quorum.

126. There shall be a District Inter-Branch meeting at least twice a year for purposes of reviewing Party programmes and projects or to discuss any other matters referred to it by the District Coordinating Committee and the Provincial Executive Council.
TEXT,
            'status' => 'published',
        ]);

        // Article 15: THE BRANCH
        $article15 = Section::create([
            'chapter_id' => $chapterOne->id,
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
            'body' => <<<TEXT
The Branch

127. There shall be a Branch Executive Committee for each Branch in the Province.

128. Members of the Branch Executive Committee shall be elected to hold office for a maximum period of two years at a Branch Conference specially convened for the purpose by such number of delegates representing each of the cells or villages in the Branch as may be determined by the Central Committee from time to time.

129. The Branch Executive Committee shall consist of forty-four (44) members constituted as follows:-
    (1) the Chairperson;
    (2) the Vice Chairperson;
    (3) the Secretary for Administration;
    (4) the Secretary for Finance;
    (5) the Secretary for Commissariat;
    (6) the Secretary for Security;
    (7) the Secretary for Transport and Social Welfare;
    (8) the Secretary for Information and Publicity;
    (9) the Secretary for Legal Affairs;
    (10) the Secretary for Indigenisation and Economic Empowerment;
    (11) the Secretary for Production and Labour;
    (12) the Secretary for Health and Child Welfare;
    (13) the Secretary for Economic Affairs;
    (14) the Secretary for Education;
    (15) the Secretary for Gender and Culture;
    (16) the Secretary for Welfare of the Disabled and Disadvantaged Persons;
    (17) the Secretary for Land Reform and Resettlement;
    (18) two (2) committee members;
    (19) five (5) members being the Chairperson, Vice Chairperson, Secretary, Secretary for Finance and Secretary for Commissariat of the Branch Women's League;
    (20) five (5) members being the Chairperson, Vice Chairperson, Secretary, Secretary for Finance and Secretary for Commissariat of the Branch Youth League;
    (21) fifteen (15) Vice Secretaries for posts referred to in subsections (3) to (17) above.

130. Any person holding an executive office in the Cell or Village Committee who is elected a member of the Branch Executive Committee shall, on being so elected, automatically cease to be an executive member of the Cell or Village Committee.

131. The number of delegates from each Cell or Village to the Branch Conference shall be determined by the Central Committee from time to time.

132. The Branch Executive Committee shall meet at least once a month.

133. In order to become a member, a person shall make application:-
    (1) to the local Branch nearest to the place where he or she is ordinarily resident or working;
    (2) directly to the Secretary for Administration in exceptional circumstances.

134. A majority of the membership of the Branch Executive Committee shall constitute a quorum.

135. The Branch Executive Committee shall hold office for a term of two years.
TEXT,
            'status' => 'published',
        ]);

        // Chapter 2 – Article 17: NAME / AIMS AND OBJECTS / MEMBERSHIP
        $article17 = Section::create([
            'chapter_id' => $chapterTwo->id,
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
            'body' => <<<TEXT
ARTICLE 17 – Name, Aims and Objects, Membership

The Name
140. There is hereby constituted and established the Women's Wing as a constituent component and integral part of the Zimbabwe African National Union Patriotic Front and the Women's Wing shall be known as the Women's League.

Aims and Objects
141. The aims and objects of the Women's League shall be the same as those set out in Article 2 of this Constitution and, in addition, it shall be the duty of the Women's League:-
    (1) to mobilise the women of Zimbabwe in support of the Party;
    (2) to defend and promote the rights of women and remove customs and attitudes that oppress and suppress women;
    (3) to promote the education and adult literacy of women;
    (4) to achieve for women equality of opportunity in employment and education and in society generally;
    (5) to promote the dignity of women as mothers and as custodians of what is good in the national cultural heritage;
    (6) to promote and protect the rights of children;
    (7) to foster solidarity with progressive women's organisations and groups in Zimbabwe and internationally.

Membership
142. Every woman who has attained the age of 18 years and who is a member of the Party shall be entitled to membership of the Women's League through her Branch Executive Committee.

143. Every woman who is a fully paid up member of the Women's League shall be entitled to be issued with a membership card of the Women's League.
TEXT,
            'status' => 'published',
        ]);

        // Chapter 2 – Article 18: ORGANS AND STRUCTURES OF THE WOMEN'S LEAGUE
        $article18 = Section::create([
            'chapter_id' => $chapterTwo->id,
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
            'body' => <<<TEXT
ARTICLE 18 – Organs and Structures of the Women\'s League

144. The Women's League shall consist of the following organs:-
    (1) the National Conference;
    (2) the National Assembly;
    (3) the National Executive Council;
    (4) the Provincial Executive Committee;
    (5) the District Executive Committee;
    (6) the Branch Executive Committee; and
    (7) the Cell/Village Committee.

National Conference
145. There shall be a National Conference which, subject to the overriding authority of the Central Committee of the Party, shall be the principal organ of the Women's League responsible for all matters of policy and shall be governed by the following provisions:-
    (1) the National Conference shall be composed of:-
        (a) members of the Provincial Executive Council of each Province;
        (b) such number of members from every District as the Central Committee may determine from time to time.
    (2) the National Conference shall have exclusive power and authority to elect members of the National Executive Council who shall be heads of department.
    (3) the National Conference may convene in extraordinary session at the instance of the Central Committee in consultation with the National Executive Council of the Women's League;
    (4) more than half the total membership of the National Conference shall form a quorum.

National Assembly
146. There shall be a National Assembly which shall be the principal deliberative organ of the Women's League on matters of policy and the following provisions shall govern its operations:-
    (1) the National Assembly shall be composed of:-
        (a) heads of departments;
        (b) deputy heads of departments;
        (c) heads of departments of the Provincial Executive Councils.
    (2) the main functions and responsibilities of the National Assembly shall be:-
        (a) to issue directives to all organs of the Women's League;
        (b) to supervise the implementation of the policies and directives of the Party;
        (c) to approve reports, including reports of the accounts and other financial affairs.
    (3) more than half the total membership of the National Assembly shall form a quorum.

National Executive Council
147. There shall be a National Executive Council which, subject to the overriding authority of the Central Committee of the Party, shall:-
    (1) be the principal organ for the implementation of the policies of the Party and the administration of the affairs of the Women's League;
    (2) be constituted by:-
        (a) the Secretary for Women's Affairs;
        (b) other Secretaries and Deputies as provided for hereunder;
        (c) Chairpersons of Provincial Executive Committees of the Women's League who shall be ex officio members;
    (3) convene in ordinary session once every two months;
    (4) have a quorum of more than half of the total membership;
    (5) convene in extraordinary session at any time with a quorum of more than half of the total membership.

Principal Officers
148. The Principal Officers of the Women's League shall be:-
    (1) the Secretary for Women's Affairs;
    (2) the Deputy Secretary for Women's Affairs;
    (3) the Secretary for Administration;
    (4) the Secretary for Finance;
    (5) the Secretary for Commissariat;
    (6) the Secretary for External Relations;
    (7) the Secretary for Security;
    (8) the Secretary for Transport and Social Welfare;
    (9) the Secretary for Information and Publicity;
    (10) the Secretary for Legal Affairs and Women's Rights;
    (11) the Secretary for Indigenisation and Economic Empowerment;
    (12) the Secretary for Production and Labour;
    (13) the Secretary for Health and Child Welfare;
    (14) the Secretary for Economic Affairs;
    (15) the Secretary for Education;
    (16) the Secretary for Gender and Culture;
    (17) the Secretary for Welfare of the Disabled and Disadvantaged Persons;
    (18) the Secretary for Land Reform and Resettlement;
    (19) the Secretary for Science and Technology;
    (20) deputies to officers referred to in subsections (3) to (18).
TEXT,
            'status' => 'published',
        ]);

        // Chapter 2 – Article 19: PROVINCE (WOMEN'S LEAGUE)
        $article19 = Section::create([
            'chapter_id' => $chapterTwo->id,
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
            'body' => <<<TEXT
ARTICLE 19 – Province (Women\'s League)

Provincial Conference
167. There shall be a Provincial Conference of the Women's League for each Province which shall be composed of:-
    (1) members of the Provincial Executive Council of the Province;
    (2) such number of delegates, representing each District in the Province as the Central Committee may determine from time to time.

168. The Provincial Conference shall have power and authority:-
    (1) to elect members of the Provincial Executive Committee who shall be the Heads of Departments in the Province;
    (2) to elect the Deputy Heads of Departments in the Province who shall be members of the National Conference.

169. The Provincial Conference shall:-
    (1) convene in ordinary session once every four years;
    (2) convene in extraordinary session when so directed by the Central Committee.

170. More than half of the total membership shall form a quorum.
TEXT,
            'status' => 'published',
        ]);

        // Chapter 2 – Article 20: PRINCIPAL OFFICERS OF THE PROVINCIAL EXECUTIVE COMMITTEE OF WOMEN'S LEAGUE
        $article20 = Section::create([
            'chapter_id' => $chapterTwo->id,
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
            'body' => <<<TEXT
ARTICLE 20 – The Principal Officers of the Provincial Executive Committee of the Women\'s League

Principal Officers
171. There shall be a Provincial Executive Committee of the Women's League in each Province of the Party composed of:-
    (1) the Chairwoman;
    (2) the Vice Chairwoman;
    (3) the Secretary for Administration;
    (4) the Secretary for Finance;
    (5) the Secretary for Commissariat;
    (6) the Secretary for Security;
    (7) the Secretary for Transport and Social Welfare;
    (8) the Secretary for Information and Publicity;
    (9) the Secretary for Legal Affairs and Women's Rights;
    (10) the Secretary for Indigenisation and Economic Empowerment;
    (11) the Secretary for Production and Labour;
    (12) the Secretary for Health and Child Welfare;
    (13) the Secretary for Economic Affairs;
    (14) the Secretary for Education;
    (15) the Secretary for Gender and Culture;
    (16) the Secretary for Welfare of the Disabled and Disadvantaged Persons;
    (17) the Secretary for Land Reform and Resettlement;
    (18) deputies to officers referred to in subsections (3) to (17).

172. The Provincial Executive Committee of the Women's League shall be responsible for the implementation of the policies of the Party and administration of the affairs of the Women's League in the Province.

173. The Provincial Executive Committee shall be answerable to the Provincial Conference and the Central Committee of the Party.
TEXT,
            'status' => 'published',
        ]);

        // Chapter 2 – Article 21: DISTRICTS (WOMEN'S LEAGUE)
        $article21 = Section::create([
            'chapter_id' => $chapterTwo->id,
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
            'body' => <<<TEXT
ARTICLE 21 – Districts (Women\'s League)

District Executive Committee
174. There shall be a District Executive Committee of the Women's League for each District of the Party.

175. Each District Executive Committee shall comprise such number of members of the Women's League as the Central Committee of the Party may from time to time specify.

176. The members of the Women's League who constitute the District Executive Committee shall be representatives from branches in the District, and the number of such representatives from each branch shall be as specified by the Central Committee from time to time and the number of branches sending representatives to the District Executive Committee shall also be specified by the Central Committee from time to time.

177. All members of the District Executive Committee shall be elected once every two years at an inter-branch meeting of the District specifically convened for that purpose attended by duly elected representatives of the branches.

178. In every District Executive Committee the number and designation of every office and the title of every officer holding office in the District Executive Committee shall be the same as the corresponding office, designation and title in the Provincial Executive as provided in this Constitution.

179. Every officer of the District Executive Committee and her deputy shall be directly responsible to the appropriate officer of the Provincial Executive Council to whom they shall submit reports on matters relating to their functions and responsibilities and as often as may be directed.
TEXT,
            'status' => 'published',
        ]);

        // Chapter 2 – Article 22: BRANCH (WOMEN'S LEAGUE)
        $article22 = Section::create([
            'chapter_id' => $chapterTwo->id,
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
            'body' => <<<TEXT
ARTICLE 22 – Branch (Women\'s League)

Branch Executive Committee
180. There shall be a Branch Executive Committee of the Women's League for each Branch of the Party.

181. A Branch Executive Committee shall comprise such number of members as the Central Committee of the Party may determine from time to time.

182. All members of the Branch Executive Committee shall be elected annually at a meeting of all the members of the Branch specifically convened for that purpose.

183. In every Branch Executive Committee the number and designation of every office and the title of every officer holding office in the Branch Executive Committee shall be the same as the corresponding office, designation and title in the District Executive Committee.

184. Every officer of the Branch Executive Committee and her Deputy shall be directly responsible to the appropriate officer of the District Executive Committee to whom they shall submit reports on matters relating to their functions and responsibilities.
TEXT,
            'status' => 'published',
        ]);

        // Chapter 3 – Article 23: NAME / AIMS AND OBJECTS / MEMBERSHIP (YOUTH LEAGUE)
        $article23 = Section::create([
            'chapter_id' => $chapterThree->id,
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
            'body' => <<<TEXT
ARTICLE 23 – Name, Aims and Objects, Membership (Youth League)

Name
185. There is hereby constituted and established the Youth Wing as a constituent component and integral part of the Zimbabwe African National Union Patriotic Front and the Youth Wing shall be known as the Youth League.

Aims and Objects
186. The aims and objects of the Youth League shall be those set out in Article 2 of this Constitution and, in addition, it shall be the duty of the Youth League:-
    (1) to mobilise the youth for full participation in the political, social, cultural and economic affairs of the country;
    (2) to mobilise the youth in support and defence of the Party;
    (3) to promote and protect the interests of the youth;
    (4) to foster solidarity with progressive youth organisations and groups in Zimbabwe and internationally.

Membership
187. Membership of the Youth League shall be open to all citizens and residents of Zimbabwe who have attained the age of 15 years but have not attained the age of 30 years.
TEXT,
            'status' => 'published',
        ]);

        // Chapter 3 – Article 24: YOUTH LEAGUE ORGANS (Principal Organs and National Conference)
        $article24 = Section::create([
            'chapter_id' => $chapterThree->id,
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
            'body' => <<<TEXT
ARTICLE 24 – Youth League Organs and Structures

Principal Organs
188. The Youth League shall consist of the following organs:-
    (1) the National Conference;
    (2) the National Assembly;
    (3) the National Executive Committee;
    (4) the Provincial Conference;
    (5) the Provincial Executive Committee;
    (6) the District Executive Committee;
    (7) the Branch Executive Committee; and
    (8) the Cell/Village Committee.

The National Conference of the Youth League
189. There shall be a National Conference which shall be the principal organ of the Youth League and shall consist of:-
    (1) the Secretary for Youth Affairs;
    (2) the Deputy Secretary for Youth Affairs;
    (3) the National Executive Committee;
    (4) such number of members elected by the Provincial Conference from each Province as the Central Committee may from time to time determine.

190. The National Conference shall have the power and authority to:-
    (1) elect the members of the National Executive Committee;
    (2) formulate and declare the policies and programmes of the Youth League;
    (3) exercise any such powers and authority as may be incidental thereto.

191. The National Conference shall convene in ordinary session once every four years and may be convened in extraordinary session when directed by the Central Committee.

192. More than half of the total membership shall form a quorum.
TEXT,
            'status' => 'published',
        ]);

        // Chapter 3 – Article 25: PRINCIPAL OFFICERS OF THE YOUTH LEAGUE
        $article25 = Section::create([
            'chapter_id' => $chapterThree->id,
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
            'body' => <<<TEXT
ARTICLE 25 – Principal Officers of the Youth League

Principal Officers
198. The Principal Officers of the Youth League shall be:-
    (1) the National Secretary for Youth Affairs;
    (2) the Deputy Secretary for Youth Affairs;
    (3) the Secretary for Administration;
    (4) the Secretary for Finance;
    (5) the Secretary for Commissariat;
    (6) the Secretary for External Relations;
    (7) the Secretary for Security;
    (8) the Secretary for Transport and Social Welfare;
    (9) the Secretary for Information and Publicity;
    (10) the Secretary for Legal Affairs;
    (11) the Secretary for Indigenisation and Economic Empowerment;
    (12) the Secretary for Production and Labour;
    (13) the Secretary for Health and Child Welfare;
    (14) the Secretary for Economic Affairs;
    (15) the Secretary for Education;
    (16) the Secretary for Gender and Culture;
    (17) the Secretary for Welfare of the Disabled and Disadvantaged Persons;
    (18) the Secretary for Land Reform and Resettlement;
    (19) deputies to officers referred to in subsections (3) to (18).

Functions and Responsibilities of the Principal Officers of the Youth League

199. There shall be a National Secretary for Youth Affairs whose main functions and responsibilities shall be:-
    (1) to be Chief Political and Administration Officer;
    (2) to promote, foster and encourage self-reliance among the youth;
    (3) in consultation with the Secretary for External Relations, to establish and maintain international relations with organisations, institutions and solidarity groups whose aims and objectives are not inconsistent with those of the Party;
    (4) to preside over sessions of the National Conference, the National Assembly and the National Executive Committee of the Youth League.

200. There shall be a Secretary for Administration whose main functions and responsibilities shall be:-
    (1) to conduct and receive all correspondence relating to the overall administration of the Youth League;
    (2) to act as Secretary to the National Conference, the National Assembly and National Executive Committee;
    (3) to keep and maintain minutes of sessions of the National Conference, the National Assembly and National Executive Committee;
    (4) to direct, supervise and co-ordinate the efficient administration of the Youth League.

201. There shall be a Secretary for Finance whose main functions and responsibilities shall be:-
    (1) to raise funds and mobilise resources for the Youth League;
    (2) to receive and deposit funds into a bank account and to disburse the same in accordance with the rules and regulations of the Party;
    (3) to keep financial accounts and records of moveable and immoveable property assigned for use to the Youth League;
    (4) to prepare an annual financial statement of accounts and to submit externally audited accounts to the National Conference.

202. There shall be a Secretary for Commissariat whose main functions and responsibilities shall be:-
    (1) to consult and liaise with the National Secretary for Commissariat;
    (2) to prepare and maintain records relating to membership;
    (3) to implement the Party's political programmes;
    (4) to direct, supervise and co-ordinate the activities of all lower organs of the Youth League.

203. There shall be a Secretary for External Relations whose main functions and responsibilities shall be:-
    (1) to consult and liaise with the National Secretary for External Relations in the implementation of Party policies;
    (2) to work in close consultation and co-ordination with the National Secretary for External Relations to establish, promote and maintain friendly relations with all organisations, associations and solidarity groups whose aims and objects are not inconsistent with those of the Party;
    (3) to establish, promote and maintain sporting and cultural ties with the youth of friendly countries.

204. There shall be a Secretary for Security whose main functions and responsibilities shall be:-
    (1) to advise on matters of security;
    (2) to liaise and co-ordinate with the National Secretary for Security;
    (3) to be responsible for all matters of security and protocol;
    (4) to carry out such other functions as may be directed from time to time by the National Secretary for Security.

205. There shall be a Secretary for Transport and Social Welfare whose main functions and responsibilities shall be:-
    (1) to administer and maintain a transport fleet assigned to the Youth League;
    (2) to prepare and maintain records and reports of all property assigned to the Youth League;
    (3) to liaise with the National Secretary for Transport and Social Welfare.

206. There shall be a Secretary for Information and Publicity whose main functions and responsibilities shall be:-
    (1) to consult and liaise with the National Secretary for Information and Publicity;
    (2) to organise and arrange publicity of the policies of the Party and activities of the Youth League.

207. There shall be a Secretary for Legal Affairs whose main functions and responsibilities shall be:-
    (1) to be the Legal Advisor of the Youth League on all legal and constitutional matters;
    (2) to liaise and co-ordinate with the National Secretary for Legal Affairs.

208. There shall be a Secretary for Indigenisation and Economic Empowerment whose main functions and responsibilities shall be:-
    (1) to design and implement programmes and activities for indigenisation of the economy and economic empowerment of the previously economically discriminated and disadvantaged black majority;
    (2) to liaise and co-ordinate with groups and individuals, non-governmental organisations and government agencies whose responsibility, role and function is to deal with the issues of indigenisation and economic empowerment of the previously economically discriminated and disadvantaged black majority.

209. There shall be a Secretary for Production and Labour whose main functions and responsibilities shall be to establish co-operatives, commercial farms, estates and other productive undertakings in consultation with the National Secretary for Production and Labour.

210. There shall be a Secretary for Health and Child Welfare whose main functions and responsibilities shall be:-
    (1) to promote primary health care programmes among the youth throughout Zimbabwe;
    (2) to promote and implement the Party's policy on the welfare of children;
    (3) to liaise with the National Secretary for Health and Child Welfare.

211. There shall be a Secretary for Economic Affairs whose main functions and responsibilities shall be:-
    (1) to implement Party economic policies;
    (2) to formulate economic strategies;
    (3) to be the Chairperson of the Economic Committee of the Youth League.

212. There shall be a Secretary for Education whose main functions and responsibilities shall be:-
    (1) to promote educational opportunities for the youth;
    (2) to liaise with the National Secretary for Education on matters relating to education for the youth.

213. There shall be a Secretary for Gender and Culture whose main functions and responsibilities shall be:-
    (1) to ensure that the issues of gender balance and equity are addressed and incorporated in all economic and social spheres of the Party and society;
    (2) to ensure that Zimbabwean culture is addressed by the Party in all spheres of life and activities;
    (3) to liaise and co-ordinate with community groups, NGOs and Government agencies responsible for gender and culture.

214. There shall be a Secretary for Welfare of the Disabled and Disadvantaged Persons whose main functions and responsibilities shall be:-
    (1) to draw up programmes and activities promoting the welfare of the disabled and disadvantaged persons;
    (2) to liaise and co-ordinate with community groups, non-governmental organisations and government agencies responsible for the welfare of the disabled and disadvantaged.

215. There shall be a Secretary for Land Reform and Resettlement whose main functions and responsibilities shall be:-
    (1) to formulate strategies that ensure equitable redistribution of land and in particular to ensure that youth have access to land;
    (2) to work in close consultation and co-ordination with Government agencies responsible for matters relating to Land Reform and Resettlement Programmes.
TEXT,
            'status' => 'published',
        ]);

        // Chapter 3 – Article 26: PROVINCE (Youth League)
        $article26 = Section::create([
            'chapter_id' => $chapterThree->id,
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
            'body' => <<<TEXT
ARTICLE 26 – Province (Youth League)

Provincial Conferences of the Youth League
216. There shall be a Provincial Conference of the Youth League for each Province which shall be composed of:-
    (1) members of the Provincial Executive Committee of the Province;
    (2) such members or delegates representing each District in the Province as may from time to time be determined by the Central Committee.

Functions and Responsibilities of the Provincial Conferences of the Youth League
217. The main functions and responsibilities of the Provincial Conference shall be:-
    (1) to elect Heads of Departments in the Province;
    (2) to elect Deputy Heads of Departments.

Convening of Provincial Conference
218. (1) The Provincial Conference of the Youth League shall:-
        (i) convene in ordinary session once a year; and
        (ii) convene in extra-ordinary session at any time at the direction of the Central Committee.
    (2) More than half the number of the members of the Heads and Deputy Heads of Departments shall form a quorum.

Provincial Executive Committee
219. There shall be a Provincial Executive Committee of the Youth League in each Province which shall be composed of:-
    (1) the Provincial Chairman;
    (2) the Deputy Provincial Chairman;
    (3) the Provincial Secretary for Administration;
    (4) the Provincial Secretary for Finance;
    (5) the Provincial Secretary for Commissariat;
    (6) the Provincial Secretary for Security;
    (7) the Provincial Secretary for Transport and Social Welfare;
    (8) the Provincial Secretary for Information and Publicity;
    (9) the Provincial Secretary for Legal Affairs;
    (10) the Provincial Secretary for Indigenisation and Economic Empowerment;
    (11) the Provincial Secretary for Production and Labour;
    (12) the Provincial Secretary for Health and Child Welfare;
    (13) the Provincial Secretary for Economic Affairs;
    (14) the Provincial Secretary for Education;
    (15) the Provincial Secretary for Gender and Culture;
    (16) the Provincial Secretary for the Disabled and Disadvantaged Persons;
    (17) the Provincial Secretary for Land Reform and Resettlement;
    (18) deputies to officers referred to in subsections (3) to (17).

Functions and Responsibilities of Provincial Executive Committee
220. The Provincial Executive Committee of the Youth League shall be:-
    (1) responsible for the implementation of the youth policies of the Party;
    (2) responsible for the administration of the affairs of the Youth League;
    (3) accountable to the National Executive Committee of the Youth League.

Provincial Chairman of the Youth League
221. There shall be a Provincial Chairman of the Youth League whose main functions and responsibilities shall be:-
    (1) to preside over meetings of the Provincial Conference and Provincial Executive Committee;
    (2) to prepare annual reports on the activities of the Youth League and submit the same to the Chairman of the Provincial Council (Main Wing).

Provincial Secretary for Administration
222. There shall be a Provincial Secretary for Administration whose main functions and responsibilities shall be:-
    (1) to keep and maintain minutes of meetings of the Provincial Conference and of the Provincial Executive Committee;
    (2) to receive and conduct correspondence relating to the overall administration of the Province;
    (3) to direct, supervise and co-ordinate efficient administration;
    (4) to submit reports to the National Secretary for the Youth League.

Provincial Secretary for Finance
223. There shall be a Provincial Secretary for Finance whose main functions and responsibilities shall be:-
    (1) to raise funds and mobilise resources;
    (2) to receive and deposit all funds into a bank account of the Party in accordance with the directives of the Secretary for Finance of the Party;
    (3) to keep accounts and records of all financial transactions;
    (4) to prepare and submit to the Secretary for Finance of the Party reports relating to the assets and liabilities at least once a year or as may be directed by the Secretary for Finance from time to time.

Provincial Secretary for Commissariat
224. There shall be a Provincial Secretary for Commissariat whose main functions and responsibilities shall be:-
    (1) to organise and supervise the implementation of Party political programmes at District Coordinating Committee, District and Branch levels;
    (2) to prepare and maintain records of membership at District, Branch and Cell/Village levels;
    (3) to consult and liaise with the National Secretary for Commissariat of the Party;
    (4) to prepare reports on matters relating to his or her functions and responsibilities and submit through the Chairman of the Province such reports to the National Political Commissar of the Youth League as may be directed.

Provincial Secretary for Security
225. There shall be a Provincial Secretary for Security whose main functions and responsibilities shall be:-
    (1) to advise the National Secretary for Security of the Youth League and National Secretary for Security of the Party on matters relating to security in the Province through the Chairman of the Province;
    (2) to carry out any new functions as may be directed from time to time by the Secretary for National Security of the Party and Secretary for Security of the Youth League.

Provincial Secretary for Transport and Social Welfare
226. There shall be a Provincial Secretary for Transport and Social Welfare whose main functions and responsibilities shall be:-
    (1) to administer and maintain a transport fleet;
    (2) to prepare and maintain records and reports of all property connected with transport;
    (3) to liaise and co-ordinate with the National Secretary for Transport and Social Welfare of the Youth League.

Provincial Secretary for Information and Publicity
227. There shall be a Provincial Secretary for Information and Publicity whose main functions and responsibilities shall be:-
    (1) to be Chief Information and Publicity Officer;
    (2) to organise and arrange publicity for the policies of the Party and activities of the Youth League;
    (3) to direct, control and co-ordinate activities relating to information and publicity at Provincial, District and Branch levels as often as may be directed by the National Secretary for the Youth League.

Provincial Secretary for Legal Affairs
228. There shall be a Provincial Secretary for Legal Affairs whose functions and responsibilities shall be:-
    (1) to act as Legal Adviser;
    (2) to liaise with the Secretary for Legal Affairs of the Youth League;
    (3) where appropriate, to represent the Youth League and its members in the courts of law.

Provincial Secretary for Indigenisation and Economic Empowerment
229. There shall be a Provincial Secretary for Indigenisation and Economic Empowerment whose main functions and responsibilities shall be:-
    (1) to design and implement programmes and activities for indigenisation of the economy and economic empowerment of the previously economically discriminated and disadvantaged black majority;
    (2) to liaise and co-ordinate with groups and individuals, non-governmental organisations and government agencies whose responsibility, role and function is to deal with the issues of economically discriminated and disadvantaged black majority.

Provincial Secretary for Production and Labour
230. There shall be a Provincial Secretary for Production and Labour whose main functions and responsibilities shall be:-
    (1) to establish co-operatives, commercial farms and estates in consultation with the Secretary for Production and Labour of the Youth League;
    (2) to liaise and co-ordinate with agencies of the Government responsible for production and labour;
    (3) to promote self-reliance among the youth of the Province.

Provincial Secretary for Health and Child Welfare
231. There shall be a Provincial Secretary for Health and Child Welfare whose main functions and responsibilities shall be:-
    (1) to promote primary health care among the youth in the Province;
    (2) to promote and implement the Party's policy on the welfare of children;
    (3) to work in close consultation with Government agencies responsible for matters relating to health and child welfare.

Provincial Secretary for Economic Affairs
232. There shall be a Provincial Secretary for Economic Affairs whose main functions and responsibilities shall be:-
    (1) to formulate and implement economic strategies for co-ordinated development in the Province;
    (2) to be the Chairman of the Economic Committee of the Province.

Provincial Secretary for Gender and Culture
233. There shall be a Provincial Secretary for Gender and Culture whose main functions and responsibilities shall be:-
    (1) to ensure that the issues of gender balance and equity are addressed and incorporated in all economic and social spheres of the Party and society;
    (2) to ensure that Zimbabwean culture is addressed by the Party in all spheres of life and activities;
    (3) to liaise and co-ordinate with community groups, NGOs and Government agencies responsible for gender and culture.

Provincial Secretary for Welfare of the Disabled and Disadvantaged Persons
234. There shall be a Provincial Secretary for Welfare of the Disabled and Disadvantaged Persons whose main functions and responsibilities shall be:-
    (1) to draw up programmes and activities promoting the welfare of the disabled and disadvantaged persons;
    (2) to liaise and co-ordinate with community groups, non-governmental organisations and Government agencies responsible for the welfare of the disabled and disadvantaged persons.

Provincial Secretary for Land Reform and Resettlement
235. There shall be a Provincial Secretary for Land Reform and Resettlement whose main functions and responsibilities shall be:-
    (1) to formulate strategies that ensure equitable redistribution of land and in particular to ensure that the youth have access to land;
    (2) to work in close consultation and co-ordination with Government agencies responsible for matters relating to Land Reform and Resettlement Programmes.

Provincial Deputy Chairman and Deputy Secretaries
236. There shall be a Provincial Deputy Chairman of the Youth League and a Deputy Secretary for each of the offices of Secretary specified above.
TEXT,
            'status' => 'published',
        ]);

        // Chapter 3 – Article 28: BRANCH (YOUTH LEAGUE)
        $article28 = Section::create([
            'chapter_id' => $chapterThree->id,
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
            'body' => <<<TEXT
ARTICLE 28 – Branch (Youth League)

Branch Executive Committee
243. There shall be a Branch Executive Committee of the Youth League for each Branch of the Party.

244. A Branch Executive Committee shall comprise such number of members as the Central Committee of the Party may determine from time to time.

245. A Branch Executive Committee shall be elected once every two years at a meeting of all the members of the Branch specifically convened for the purpose.

246. In every Branch Executive Committee the number and designation of every office and the title of every officer holding office in the Branch Executive Committee shall be the same as the corresponding office, designation and title in the District Executive Committee.

247. Every officer of the Branch Executive Committee and his or her Deputy shall be directly responsible to their Branch Executive Committee to whom they shall submit reports on matters relating to their functions and responsibilities.
TEXT,
            'status' => 'published',
        ]);

        // Chapter 4 – Article 29: GENERAL PROVISIONS
        $article29 = Section::create([
            'chapter_id' => $chapterFour->id,
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
            'body' => <<<TEXT
ARTICLE 29 – General Provisions

248. Any member holding office in any organ at any level of the Party who has failed to discharge his or her functions and responsibilities shall be liable to disciplinary action.

249. Any member holding office in any organ at any level of the Party who absents himself or herself from any meeting on more than three consecutive occasions without reasonable cause may, on a resolution of the relevant organ, lose office.

250. Any office which falls vacant in any organ of the Party, other than the Central Committee, shall be filled by co-option by the Executive Council or Committee of the appropriate organ until the next elections, but where the vacant offices are one third of the total membership that organ shall automatically dissolve itself and new elections shall be held.

251. A motion of no confidence shall be by a simple majority of all members of the appropriate organ. Provided that where a vote of no confidence is passed against one third of the total membership of any organ, that organ shall automatically dissolve itself and new elections shall be held.
TEXT,
            'status' => 'published',
        ]);

        // Chapter 4 – Article 30: INTERPRETATION AND AMENDMENT OF THE CONSTITUTION
        $article30 = Section::create([
            'chapter_id' => $chapterFour->id,
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
            'body' => <<<TEXT
ARTICLE 30 – Interpretation and Amendment of the Constitution

Interpretation of the Constitution
252. Any issue or matter arising in connection with the interpretation or application of this Constitution which cannot be resolved otherwise under this Constitution shall be referred for determination to the Central Committee whose decision thereon shall be final.

Amendments to the Constitution
253. The power to amend the Constitution shall vest in the Central Committee subject to ratification by Congress and the following provisions shall apply in respect thereof:-
    (1) any member of the Party supported by fifty other members may propose or move an amendment to the Constitution and shall be required to submit such proposed amendment to the District Coordinating Committee which shall, on receipt of the said proposed amendment, forward the same to the Provincial Executive Council;
    (2) any organ of the Party may propose or move an amendment to the Constitution and shall, in the case of subordinate organs of the Party, be required to submit such proposed amendment to the next superior Party organ for onward transmission to the Provincial Executive Council;
    (3) the Provincial Executive Council shall, in the case of all proposed amendments, whether these emanate from it or from subordinate Party organs, forward the same to the Secretary for Administration;
    (4) any proposed amendments shall be submitted to the Secretary for Administration at least three months before the date of the meeting of the Central Committee at which the amendment is to be considered;
    (5) the Secretary for Administration, upon receipt of the proposed amendments, shall cause the same to be circulated to the Provinces at least two months before the date of meeting;
    (6) a two-thirds majority of delegates of the Central Committee present and voting shall be required for the adoption of the proposed amendment to the Constitution;
    (7) a two-thirds majority of delegates of Congress present and voting shall be required for the ratification of amendments effected by the Central Committee.
TEXT,
            'status' => 'published',
        ]);
    }
}
