# Membership Course Plan

**Purpose**: Define the course that applicants must complete and pass to become ZANU PF members. Content is derived from both the ZANU PF Constitution and the Constitution of the Republic of Zimbabwe (2013). Passing the final assessment grants membership and registers the applicant with Admin.

**Reference structure**: `lms-example` (Course → Units: content + quiz). Constitution app maps this to **Course → Modules → Lessons** (content) and **Course → Assessment** (final exam).

---

## 1. User Flow

```
Register (student only) → Read both constitutions (required context)
    → Enrol in Membership Course
    → Complete all Modules & Lessons
    → Take Membership Assessment
    → Pass (≥70%) → Grant Member role + Register in Admin + Enrolment completed
    → Fail → Retake assessment (subject to policy: unlimited vs limited attempts)
```

- **New users**: Register as `student` only. No `member` role until they pass.
- **Membership course**: Single mandatory course, `is_mandatory = true`, `grants_membership = true` (see schema section).
- **Admin registration**: When user passes, they receive `member` role and appear in Admin member lists; enrolment marked `completed` with `completed_at` timestamp.

---

## 2. Course Structure (aligned with lms-example)

The lms-example uses:
- **Course** → **Units** (content units or Knowledge Check / quiz units)
- Content units: text, video, audio, document
- Quiz units: multiple choice, true/false (and short answer in lms)

The constitution app uses:
- **Course** → **Modules** (content grouping)
- **Course** → **Assessments** (course-level exams)
- **Module** → **Lessons** (content pieces)

For the membership course, we use:
- **Modules** as content blocks (equivalent to lms “module” groupings)
- **Lessons** for text content (equivalent to lms content units)
- **One final Assessment** covering both constitutions (equivalent to a comprehensive Knowledge Check)

No per-module quizzes in the initial design; one final assessment is simpler and matches “pass this course to become a member”.

---

## 3. Module & Lesson Layout

| Module | Title | Content focus | Lessons |
|--------|-------|----------------|---------|
| 1 | ZANU PF Constitution – Foundation & Objectives | Preamble, name, aims and objectives, founding principles | 3–4 lessons |
| 2 | ZANU PF Constitution – Membership | Qualifications, rights, duties, obligations, discipline | 3–4 lessons |
| 3 | ZANU PF Constitution – Structure & Organs | Congress, Central Committee, Politburo, Provincial structures | 3–4 lessons |
| 4 | ZANU PF Constitution – Leagues & Wings | Women's League, Youth League, aims and membership | 2–3 lessons |
| 5 | Constitution of Zimbabwe – Founding Values | Preamble, founding values, supremacy | 2–3 lessons |
| 6 | Constitution of Zimbabwe – Rights & Citizenship | Bill of Rights, citizenship, key duties | 3–4 lessons |
| 7 | Constitution of Zimbabwe – Executive & Legislature | President, Cabinet, Parliament (Senate, National Assembly) | 2–3 lessons |
| 8 | Constitution of Zimbabwe – Elections | Electoral systems, ZEC, voting, multi-party democracy | 2–3 lessons |
| 9 | Constitution of Zimbabwe – Judiciary & Rule of Law | Courts, Constitutional Court, judicial independence | 2–3 lessons |
| 10 | Constitution of Zimbabwe – Provincial & Local Government | Devolution, provincial councils, local authorities, leadership principles | 2–3 lessons |

### Lesson content sources

- **Module 1**: ZANU PF Preamble, Chapter 1 (Name, Aims, Objectives), founding principles.
- **Module 2**: Membership articles (qualifications, application, rights, duties, discipline).
- **Module 3**: Congress, Central Committee, Politburo, Provincial Executive structures.
- **Module 4**: Women's League, Youth League (name, aims, membership, structures).
- **Module 5**: Zimbabwe Preamble, founding values (Ch 1), supremacy (Ch 2).
- **Module 6**: Zimbabwe Ch 3 (Citizenship), Ch 4 (Declaration of Rights), duties.
- **Module 7**: Zimbabwe Ch 5 (Executive), Ch 6 (Legislature).
- **Module 8**: Zimbabwe Ch 7 (Elections), ZEC, electoral systems.
- **Module 9**: Zimbabwe Ch 8 (Judiciary), rule of law, courts.
- **Module 10**: Zimbabwe Ch 14 (Provincial & Local Gov't), Ch 9 (Leadership).

Content should be **educational summaries** that point to specific articles/sections in the constitutions, not verbatim copies. Learners are expected to have read the full documents before or alongside the course.

---

## 4. Assessment Design

### 4.1 Structure

- **One assessment** per course: “Membership Assessment”
- **Duration**: 30–45 minutes (configurable)
- **Pass mark**: 70% (configurable; recommend 70% to ensure comprehension)
- **Question mix**: Multiple choice (MCQ) and True/False (schema supports both via `options` + `is_correct`)

### 4.2 Question distribution

**Minimum 10 questions per module** (60+ total). Backend Admin can edit any question and answer.

| Module | Min questions | Topics |
|--------|---------------|--------|
| 1 – ZANU PF Foundation | 10 | Preamble, name, aims and objectives, founding principles |
| 2 – ZANU PF Membership | 10 | Qualifications, rights, duties, discipline |
| 3 – ZANU PF Structure | 10 | Congress, Central Committee, Politburo, provincial organs |
| 4 – ZANU PF Leagues | 10 | Women's League, Youth League |
| 5 – Zimbabwe Values | 10 | Preamble, founding values, supremacy |
| 6 – Zimbabwe Rights & Citizenship | 10 | Bill of Rights, citizenship, duties |
| 7 – Zimbabwe Executive & Legislature | 10 | President, Cabinet, Parliament |
| 8 – Zimbabwe Elections | 10 | Electoral systems, ZEC, multi-party democracy |
| 9 – Zimbabwe Judiciary | 10 | Courts, rule of law, judicial independence |
| 10 – Zimbabwe Provincial & Local | 10 | Devolution, provincial councils, local authorities |

Total: 100+ questions. Assessment can use a subset (e.g. random selection per attempt) or all questions; Admin configures as needed.

### 4.3 Political language & question style

- **Clear and precise**: Use official terminology from the constitutions.
- **No ambiguity**: Questions should have one clearly correct answer.
- **Fair**: Test understanding, not memory of minor details.
- **Appropriate tone**: Formal, respectful of political and constitutional context.

**Examples**

| Good | Avoid |
|------|--------|
| “The supreme organ of ZANU PF is…” | “The most important body is…” |
| “According to Article X, a member has the duty to…” | Vague “A member should…” |
| “The Constitution of Zimbabwe (2013) establishes that…” | Informal phrasing |

**Question formats**

- **MCQ**: 3–4 options, one correct.
- **True/False**: Direct statements from or closely paraphrased from the constitutions.

---

## 5. Example Questions (draft)

All questions can be edited via the backend Admin. Below: **Module 1 – Foundation & Objectives** (12 questions) as the reference set.

---

### Module 1 – ZANU PF Constitution: Foundation & Objectives (12 questions)

1. **(T/F)** The full name of the Party is the Zimbabwe African National Union Patriotic Front, hereinafter referred to as "ZANU PF" or "the Party".

2. **(MCQ)** The Party is described in the Constitution as:  
   (a) an unincorporated association  
   (b) a body corporate with perpetual succession  
   (c) a trust  
   (d) a partnership  

3. **(T/F)** The Unity Accord that united ZANU PF and PF-ZAPU was concluded on 22nd December 1987.

4. **(MCQ)** The Patriotic Front Alliance was:  
   (a) a colonial institution  
   (b) the effective instrument for prosecuting the armed struggle and winning democracy and national independence  
   (c) a regional trade bloc  
   (d) a religious organisation  

5. **(MCQ)** The Party headquarters are located in:  
   (a) Bulawayo  
   (b) Mutare  
   (c) Harare  
   (d) Gweru  

6. **(T/F)** The Party flag comprises the colours green, yellow, red and black.

7. **(MCQ)** According to the Constitution, black on the Party flag represents:  
   (a) the vegetation and agriculture of Zimbabwe  
   (b) the mineral wealth of Zimbabwe  
   (c) the indigenous people as the sovereign owners and custodians of Zimbabwe  
   (d) the blood of the liberation struggle  

8. **(MCQ)** The Party’s vision is:  
   (a) to become a regional alliance  
   (b) forever to remain the mass revolutionary socialist Party in the emancipation process of the people of Zimbabwe from all forms of oppression  
   (c) to promote foreign investment above all else  
   (d) to establish a theocratic state  

9. **(MCQ)** Among the aims and objectives of the Party is:  
   (a) to preserve and defend the National Sovereignty and Independence of Zimbabwe  
   (b) to abolish private property  
   (c) to establish a monarchy  
   (d) to join a foreign federation  

10. **(T/F)** The Party aims to create conditions for a democratic political and social order with periodic free and fair elections based on universal adult suffrage.

11. **(MCQ)** The Party opposes resolutely:  
    (a) national sovereignty  
    (b) the rule of law  
    (c) tribalism, regionalism, nepotism, corruption and discrimination  
    (d) universal adult suffrage  

12. **(MCQ)** The Party supports:  
    (a) colonialism and racism  
    (b) the worldwide struggle for the complete eradication of imperialism, colonialism and all forms of racism  
    (c) foreign domination of Zimbabwe  
    (d) the abolition of the liberation struggle heritage  

---

### Module 2 – ZANU PF Constitution: Membership (12 questions)

1. **(T/F)** Membership of the Party shall be open to all citizens and residents of Zimbabwe who subscribe to the Constitution, aims, objectives and policies of the Party.

2. **(MCQ)** In order to become a member, a person shall ordinarily make application to:  
   (a) the President and First Secretary  
   (b) the National Disciplinary Committee  
   (c) the local branch nearest to the place he or she is ordinarily resident or working  
   (d) the Central Committee  

3. **(T/F)** In exceptional circumstances, a person may apply for membership through the Secretary for Administration to the Politburo.

4. **(MCQ)** Any person whose membership application has been rejected may appeal to:  
   (a) the Branch Disciplinary Committee  
   (b) the National Consultative Assembly  
   (c) the Central Committee, whose decision shall be final  
   (d) the Provincial Executive Council  

5. **(MCQ)** Every member of the Party has the right:  
   (a) to vote in Party elections in accordance with rules and regulations of the Central Committee  
   (b) to be exempt from paying subscriptions  
   (c) to disregard Party policies  
   (d) to hold office without election  

6. **(T/F)** Every member has the right not to be subjected to arbitrary or vexatious treatment by those in authority over him or her.

7. **(MCQ)** Among the duties of every member is:  
   (a) to pay regular subscriptions  
   (b) to remain anonymous  
   (c) to avoid Party meetings  
   (d) to criticise the Party publicly  

8. **(T/F)** Every member has the duty to conduct himself or herself honestly and honourably and not to bring the Party into disrepute or ridicule.

9. **(MCQ)** The only organ of the Party that has the power to expel a member is:  
   (a) the Branch Disciplinary Committee  
   (b) the District Disciplinary Committee  
   (c) the Provincial Disciplinary Committee  
   (d) the National Disciplinary Committee  

10. **(MCQ)** Any member against whom disciplinary action is intended shall first receive:  
    (a) an oral warning only  
    (b) a prohibition order and notice of charges in writing  
    (c) immediate expulsion  
    (d) a fine without hearing  

11. **(T/F)** A member has the right to be assisted or represented in the conduct of his or her disciplinary case by any member of his or her own choice.

12. **(MCQ)** Disciplinary punishments prescribed by the Constitution include:  
    (a) oral reprimand, written reprimand, fine, suspension or removal from office, and expulsion  
    (b) imprisonment only  
    (c) public shaming only  
    (d) expulsion only, with no other options  

---

### Module 3 – ZANU PF Constitution: Structure & Organs (12 questions)

1. **(MCQ)** The National People's Congress is described in the Constitution as:  
   (a) the principal organ between sessions  
   (b) the supreme organ of the Party  
   (c) the executive committee of the Central Committee  
   (d) an advisory body only  

2. **(T/F)** Congress shall convene in ordinary session once every five years.

3. **(MCQ)** Congress shall:  
   (a) appoint the President and First Secretary without election  
   (b) elect the President and First Secretary and members of the Central Committee  
   (c) be convened by the Provincial Executive Council  
   (d) meet once every year  

4. **(MCQ)** Half of the total membership of Congress shall form:  
   (a) the Presidium  
   (b) a quorum for ordinary sessions  
   (c) the Secretariat  
   (d) the Appeals Committee  

5. **(MCQ)** The Central Committee is:  
   (a) the supreme organ of the Party  
   (b) the highest organ of the Party in-between Congress  
   (c) subordinate to the Provincial Executive Council  
   (d) elected by the Politburo  

6. **(T/F)** The Central Committee shall meet once every three months in ordinary session.

7. **(MCQ)** The Politburo is described as:  
   (a) the supreme organ of the Party  
   (b) the executive committee of the Central Committee  
   (c) the electoral college for Congress  
   (d) the disciplinary body of the Party  

8. **(T/F)** The Politburo shall meet at least once a month in ordinary session.

9. **(MCQ)** Two-thirds of the total membership of the Politburo shall constitute:  
   (a) the Presidium  
   (b) a quorum  
   (c) the Secretariat  
   (d) the majority required for constitutional amendments  

10. **(MCQ)** The National People's Conference shall:  
    (a) elect the President and First Secretary  
    (b) convene once every five years  
    (c) declare the President of the Party elected at Congress as the State Presidential Candidate  
    (d) amend the Party Constitution  

11. **(T/F)** The Provincial Executive Council shall meet at least once every month.

12. **(MCQ)** The principal organs of the Party include:  
    (a) the National People's Congress, the Central Committee, the Provincial Coordinating Committees, and Branch Committees  
    (b) only the Politburo and Secretariat  
    (c) only the Women's League and Youth League  
    (d) only the National Disciplinary Committee  

---

### Module 4 – ZANU PF Constitution: Leagues & Wings

*Question bank to be expanded (min 10 questions). Content: Women's League, Youth League – name, aims, membership, structures. See ZANU PF Constitution Articles 17, 23.*

---

### Module 5 – Constitution of Zimbabwe: Founding Values (12 questions)

1. **(T/F)** The Constitution of Zimbabwe (2013) is the supreme law of Zimbabwe.

2. **(MCQ)** Any law, practice, custom or conduct inconsistent with the Constitution of Zimbabwe is:  
   (a) valid until amended by Parliament  
   (b) invalid to the extent of the inconsistency  
   (c) subject to presidential approval  
   (d) enforceable in provincial courts only  

3. **(MCQ)** According to the Constitution, Zimbabwe is:  
   (a) a federal republic  
   (b) a unitary, democratic and sovereign republic  
   (c) a confederation  
   (d) a constitutional monarchy  

4. **(T/F)** The obligations imposed by the Constitution are binding on every person, including the State and all executive, legislative and judicial institutions and agencies of government at every level.

5. **(MCQ)** Founding values and principles of Zimbabwe include:  
   (a) supremacy of the Constitution and the rule of law  
   (b) disregard for international law  
   (c) rejection of the liberation struggle  
   (d) tribalism and regionalism  

6. **(T/F)** Zimbabwe is founded on recognition of and respect for the liberation struggle.

7. **(MCQ)** The Preamble of the Constitution affirms the people’s desire for:  
   (a) colonialism and domination  
   (b) freedom, justice and equality  
   (c) foreign rule  
   (d) the abolition of democracy  

8. **(MCQ)** The Preamble commits the people to build a nation founded on values of:  
   (a) exclusion and discrimination  
   (b) secrecy and favouritism  
   (c) transparency, equality, freedom, fairness, honesty and the dignity of hard work  
   (d) hereditary rule  

9. **(T/F)** The Constitution recognises the need to entrench democracy, good, transparent and accountable governance and the rule of law.

10. **(MCQ)** Among the founding values is:  
    (a) gender inequality  
    (b) the nation's diverse cultural, religious and traditional values  
    (c) arbitrary rule  
    (d) the supremacy of custom over the Constitution  

11. **(T/F)** The Constitution acknowledges the supremacy of Almighty God, in whose hands our future lies.

12. **(MCQ)** Founding values and principles include:  
    (a) fundamental human rights and freedoms  
    (b) suspension of the rule of law in emergencies  
    (c) denial of equality of human beings  
    (d) rejection of good governance  

---

### Module 6 – Constitution of Zimbabwe: Rights & Citizenship (12 questions)

1. **(MCQ)** Persons are Zimbabwean citizens by:  
   (a) birth only  
   (b) birth, descent or registration  
   (c) registration only  
   (d) descent only  

2. **(T/F)** All Zimbabwean citizens are equally entitled to the rights, privileges and benefits of citizenship and are equally subject to the duties and obligations of citizenship.

3. **(MCQ)** Zimbabwean citizens have the duty:  
   (a) to reject the Constitution  
   (b) to be loyal to Zimbabwe and to observe the Constitution  
   (c) to disregard the national flag  
   (d) to avoid defending Zimbabwe  

4. **(MCQ)** Every Zimbabwean citizen has the duty, to the best of their ability, to:  
   (a) avoid military service  
   (b) defend Zimbabwe and its sovereignty  
   (c) renounce citizenship  
   (d) leave the country  

5. **(T/F)** Chapter 4 (Declaration of Rights) binds the State and all executive, legislative and judicial institutions and agencies of government at every level.

6. **(MCQ)** The State and every person must:  
   (a) ignore the Declaration of Rights  
   (b) respect, protect, promote and fulfil the rights and freedoms set out in Chapter 4  
   (c) limit rights without law  
   (d) suspend rights at will  

7. **(T/F)** Every person has the right to life.

8. **(MCQ)** Every person has the right to freedom of expression, which includes:  
   (a) only the right to remain silent  
   (b) freedom to seek, receive and communicate ideas and other information  
   (c) the right to incite violence  
   (d) the right to defame without limit  

9. **(T/F)** Every Zimbabwean citizen who is of or over eighteen years of age has the right to vote in all elections and referendums, and to do so in secret.

10. **(MCQ)** Every Zimbabwean citizen has the right:  
    (a) to be excluded from elections  
    (b) to form, join and participate in the activities of a political party of their choice  
    (c) to be compelled to join a political party  
    (d) to be denied a passport  

11. **(MCQ)** The Constitution provides that all persons are:  
    (a) subject to different laws based on status  
    (b) equal before the law and have the right to equal protection and benefit of the law  
    (c) exempt from judicial process  
    (d) above the law  

12. **(T/F)** Zimbabwean citizenship is not lost through marriage or the dissolution of marriage.

---

### Module 7 – Constitution of Zimbabwe: Executive & Legislature (12 questions)

1. **(MCQ)** Executive authority in Zimbabwe:  
   (a) derives from the President alone  
   (b) derives from the people of Zimbabwe and must be exercised in accordance with this Constitution  
   (c) derives from Parliament  
   (d) derives from the Judiciary  

2. **(MCQ)** The President is:  
   (a) Head of State only  
   (b) the Head of State and Government and the Commander-in-Chief of the Defence Forces  
   (c) subordinate to the Cabinet  
   (d) appointed by Parliament  

3. **(T/F)** The executive authority of Zimbabwe vests in the President who exercises it, subject to the Constitution, through the Cabinet.

4. **(MCQ)** The President must:  
   (a) ignore the Constitution  
   (b) uphold, defend, obey and respect this Constitution as the supreme law of the nation  
   (c) defer all decisions to the Cabinet  
   (d) rule by decree  

5. **(MCQ)** The Legislature of Zimbabwe consists of:  
   (a) the President only  
   (b) the Cabinet and the President  
   (c) Parliament and the President acting in accordance with Chapter 6  
   (d) the Judiciary and the Senate  

6. **(T/F)** Parliament consists of the Senate and the National Assembly.

7. **(MCQ)** Parliament must:  
   (a) defer to the President on all matters  
   (b) protect this Constitution and promote democratic governance in Zimbabwe  
   (c) dissolve the Judiciary  
   (d) abolish the Declaration of Rights  

8. **(T/F)** The legislative authority of Zimbabwe is derived from the people and is vested in and exercised by the Legislature in accordance with the Constitution.

9. **(MCQ)** The Constitution establishes:  
   (a) a single-party state  
   (b) a multi-party democratic political system  
   (c) a monarchy  
   (d) military rule  

10. **(MCQ)** The Constitution requires observance of:  
    (a) the supremacy of the President over other branches  
    (b) the principle of separation of powers  
    (c) the abolition of political parties  
    (d) the fusion of executive and judicial powers  

11. **(MCQ)** For the purpose of promoting multi-party democracy, an Act of Parliament must provide for:  
    (a) the abolition of opposition parties  
    (b) the funding of political parties  
    (c) a single national party  
    (d) the President to appoint all Members of Parliament  

12. **(T/F)** The Constitution provides for respect for the rights of all political parties.

---

### Module 8 – Constitution of Zimbabwe: Elections

*Question bank to be expanded (min 10 questions). Content: Zimbabwe Ch 7 – electoral systems, ZEC, timing, multi-party democracy, voting.*

---

### Module 9 – Constitution of Zimbabwe: Judiciary & Rule of Law

*Question bank to be expanded (min 10 questions). Content: Zimbabwe Ch 8 – courts, Constitutional Court, judicial independence, rule of law.*

---

### Module 10 – Constitution of Zimbabwe: Provincial & Local Government

*Question bank to be expanded (min 10 questions). Content: Zimbabwe Ch 14 (provincial councils, local authorities), Ch 9 (leadership principles).*

---

## 6. Schema & Backend Changes

### 6.1 Course model

Add optional flags (migration):

- `grants_membership` (boolean, default false): When true, passing the course grants the `member` role.
- Keep `is_mandatory` for UI and ordering (membership course is mandatory for new applicants).

### 6.2 Enrolment completion logic

- **Current**: `Enrolment` has `status` (`enrolled`, `in_progress`, `completed`) and `completed_at`.
- **Rule**: A user completes the course when:
  1. All required lessons are completed (if lesson-level completion is tracked), **or** the course has no lesson-completion requirement, and
  2. They pass the final assessment (any attempt with score ≥ pass_mark).

When both conditions are met:
- Set `Enrolment.status = 'completed'`, `Enrolment.completed_at = now()`.
- If `Course.grants_membership` is true: attach `member` role to the user.

### 6.3 Admin registration

- “Register with Admin” means the user becomes visible in Admin as a member.
- This is achieved by assigning the `member` role; Admin user lists can filter by role `member`.
- Optionally add `membership_granted_at` on `users` or a `member_registrations` table for auditing; not required for MVP if `role` + `enrolment.completed_at` are sufficient.

### 6.4 Lesson completion (optional for MVP)

- If lesson completion is required before taking the assessment, add `lesson_completions` (or similar) and gate the assessment on “all lessons completed”.
- Simpler MVP: allow assessment attempt once enrolled; completion depends only on passing the assessment.

---

## 7. lms-example Alignment Summary

| lms-example | Constitution app |
|-------------|------------------|
| Course | Course |
| Unit (content) | Lesson (inside Module) |
| Unit (quiz) | Assessment |
| Module (grouping) | Module |
| Enrollment | Enrolment |
| Unit completion | Lesson completion (optional) |
| Pass quiz → certificate | Pass assessment → grant member role |
| Student-only registration | Student-only registration (member added on pass) |

---

## 8. Implementation Phases

### Phase 1: Schema & seeding
- Migration: add `grants_membership` to `courses`.
- MembershipCourseSeeder: create course, 10 modules, 25–30 lessons (content from both constitutions), 1 assessment with 100+ questions (minimum 10 per module; MCQ + T/F). Admin can edit any question and answer.
- Seed questions with clear, constitution-grounded wording.

### Phase 2: Completion & membership flow
- When an assessment attempt passes (score ≥ pass_mark):
  - Mark enrolment as completed.
  - If course `grants_membership`: attach `member` role.
- Ensure new registrations receive only `student` (no `member` until pass).

### Phase 3: API & mobile
- Expose membership course via API (course detail, modules, lessons, assessment).
- Mobile: membership course entry point, lesson viewer, assessment taker.
- Show “Become a member” CTA for students; after pass, show “Member” status.

### Phase 4: Admin integration
- Admin: list members (users with `member` role).
- Admin: filter enrolments by membership course, show completion and pass/fail.
- Optional: report of “membership course passed” for audit.

---

## 9. Content Authoring Notes

- Lessons should reference specific articles/sections (e.g. “As set out in Article 5 of the ZANU PF Constitution…”).
- Summarise in plain language where helpful, but preserve key legal/political terms.
- Avoid controversial interpretation; stick to the text.
- Ensure parity of attention between ZANU PF and Zimbabwe constitutions in both lessons and assessment.

---

## 10. Open Decisions

1. **Retake policy**: Unlimited retakes, or limit (e.g. 3 attempts per enrolment)?
2. **Lesson completion**: Must all lessons be marked complete before assessment, or can users go straight to the exam?
3. **Certificate**: Should passing the membership course also generate a certificate (like lms-example)?
4. **Offline**: Should course content and assessment be available offline on mobile?
