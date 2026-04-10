# Features Gap: Laravel LMS vs WPLMS Example

Comparison of **wplms-example** (WPLMS) with the current **Laravel LMS**. Items marked ✅ exist; ❌ missing; ⚠️ partial.

---

## 1. Gamification

| Feature | WPLMS | Laravel LMS |
|--------|--------|-------------|
| **Points system** | Yes – "Gamification & Points", site-wide points | ❌ No |
| **Badges** | Yes – course badges, assign to students, badge count in stats | ❌ No |
| **Leaderboard** | Yes – "Stats & Leaderboard visibility" | ❌ No |
| **Achievements** | BadgeOS-style (achievement-type) | ❌ No |

**References:** `wplms-example/includes/init.php`, `course/single/stats.php`, `languages/vibe-en_US.po` (Gamification & Points, Leaderboard, Badges).

---

## 2. Knowledge Tests / Quizzes

| Feature | WPLMS | Laravel LMS |
|--------|--------|-------------|
| **Multiple choice** | ✅ | ✅ |
| **True/False** | ✅ | ⚠️ Seeder has it; learn UI may not render |
| **Essay** | ✅ `answer-essay.php` | ❌ |
| **Fill-in-blank** | ✅ `answer-fillblank.php` | ❌ |
| **Match** | ✅ `answer-match.php` | ❌ |
| **Sort** | ✅ `answer-sort.php` | ❌ |
| **Short text** | ✅ `answer-text.php` | ❌ |
| **Passing score** | ✅ | ✅ (e.g. 70%) |
| **Quiz retakes** | ✅ | ✅ (`attempt_number`) |
| **Randomize questions** | ✅ | ⚠️ DB column `randomize_questions` exists, not used |
| **Show correct/incorrect in results** | ✅ | ⚠️ Partial (session message; no per-question breakdown in UI) |
| **Per-quiz stats** | ✅ Average marks per quiz in course stats | ⚠️ Facilitator "Results" has attempts; no per-quiz averages |

**References:** `answer-*.php`, `single-quiz.php`, `course/single/stats.php`, `LearnController`, `courses/learn.blade.php`.

---

## 3. Assessments (Assignments)

| Feature | WPLMS | Laravel LMS |
|--------|--------|-------------|
| **Assignment unit type** | ✅ In curriculum | ✅ Unit type `assignment` exists |
| **Upload** | ✅ `assignment-upload.php` | ❌ |
| **Text/textarea** | ✅ `assignment-textarea.php` | ❌ |
| **Submit assignment** | ✅ | ❌ |
| **Instructor grading** | ✅ Score, feedback | ❌ |
| **Submission status** | submitted / graded / returned | ❌ |
| **Submissions tab (course admin)** | ✅ Quiz + Course tabs | ⚠️ Quiz results only; no assignment submissions |
| **Due date, duration, max points** | ✅ | ✅ DB (`assignments`) |
| **Formative / summative** | ✅ | ✅ DB (`assessment_type`) |
| **Assignment locking** | ✅ | ❌ |

**Current state:** Learn view shows *"Assignments are not yet supported."* Tables `assignments` and `assignment_submissions` exist.

**References:** `assignment-upload.php`, `assignment-textarea.php`, `course/single/submissions.php`, `schema.txt`.

---

## 4. Certificates

| Feature | WPLMS | Laravel LMS |
|--------|--------|-------------|
| **Certificate on completion** | ✅ | ❌ |
| **Template** (layout, background, size) | ✅ | ❌ |
| **PDF / Print / Download** | ✅ | ❌ |
| **Certificate code validation** | ✅ | ❌ |
| **Assign to students (course admin)** | ✅ | ❌ |
| **DB storage** | ✅ | ✅ `certificates` table exists |

**References:** `single-certificate.php`, `course/single/stats.php` (badge/certificate), `schema.txt`.

---

## 5. Course Reviews & Ratings

| Feature | WPLMS | Laravel LMS |
|--------|--------|-------------|
| **Star ratings (1–5)** | ✅ | ❌ |
| **Average rating, count** | ✅ | ❌ |
| **Rating breakup** (e.g. 5★: n, 4★: n…) | ✅ | ❌ |
| **Written reviews** | ✅ | ❌ |
| **"Write a Review" (enrolled users)** | ✅ | ❌ |
| **DB storage** | ✅ | ✅ `course_reviews` table exists |

**References:** `course-review.php`, `includes/ratings.php`, `schema.txt`.

---

## 6. Course Stats & Admin (WPLMS-style)

| Feature | WPLMS | Laravel LMS |
|--------|--------|-------------|
| **Total students** | ✅ | ✅ (enrollments) |
| **Average % (course)** | ✅ | ⚠️ Via progress; not "average marks" |
| **Passed count** | ✅ | ✅ (quiz passed) |
| **Badge count** | ✅ | ❌ |
| **Per-quiz average marks** | ✅ | ❌ |
| **"Calculate" stats** | ✅ | ❌ |
| **Quiz submissions tab** | ✅ | ⚠️ Facilitator "Results" covers attempts |
| **Assignment submissions tab** | ✅ | ❌ |

**References:** `course/single/stats.php`, `course/single/submissions.php`, `FacilitatorDashboardController`, `AdminDashboardController`.

---

## 7. Other

| Feature | WPLMS | Laravel LMS |
|--------|--------|-------------|
| **Activity stream** | ✅ Course activity, assignments | ❌ |
| **Forums (per course)** | ✅ BBPress | ❌ |
| **Paid courses / WooCommerce** | ✅ | ❌ |
| **Events / Calendar** | ✅ EventON | ❌ |
| **TinCan / xAPI / LRS** | ✅ | ❌ |
| **Learning maps** | ✅ | ⚠️ DB table exists, not used |

---

## Suggested implementation order

1. **Assignments** – Use existing `assignments` / `assignment_submissions`. Add upload + text submission, grading, feedback, and a submissions view for facilitators.
2. **Certificates** – Use `certificates`. Issue on course completion, simple template, optional PDF.
3. **Course reviews & ratings** – Use `course_reviews`. Star rating + optional review text on course show page.
4. **Quiz enhancements** – True/False UI, randomize, show correct/incorrect per question in results.
5. **Gamification** – Points (e.g. per unit/quiz), badges, simple leaderboard (optional).

---

## DB tables already present (Laravel LMS)

- `assignments`, `assignment_submissions`
- `certificates`
- `course_reviews`
- `learning_maps`
- `quizzes`, `questions`, `quiz_attempts`

These can be wired to UI and business logic without new migrations for the features above.
