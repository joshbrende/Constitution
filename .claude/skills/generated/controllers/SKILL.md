---
name: controllers
description: "Skill for the Controllers area of constitution. 136 symbols across 45 files."
---

# Controllers

136 symbols | 45 files | Cohesion: 59%

## When to Use

- Working with code in `lms-example/`
- Understanding how Tag, Badge, run work
- Modifying controllers-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `lms-example/app/Http/Controllers/CourseController.php` | enrollBulkStore, enroll, attendance, exportAttendance, index (+12) |
| `lms-example/app/Http/Controllers/LearnController.php` | show, storeAttendance, isDay1OpeningUnit, curriculumOrderedUnitIds, completeUnit (+7) |
| `lms-example/app/Http/Controllers/TagController.php` | ensureAdmin, index, create, store, edit (+2) |
| `lms-example/app/Http/Controllers/BadgeController.php` | ensureAdmin, index, create, store, edit (+2) |
| `lms-example/app/Http/Controllers/FacilitatorDashboardController.php` | index, stats, results, exportResults, quizStats (+1) |
| `lms-example/app/Http/Controllers/FacilitatorChatController.php` | instructorPage, index, store, update, ensureAccess (+1) |
| `lms-example/app/Http/Controllers/CertificateSignaturesController.php` | index, store, preview, facilitatorForm, storeFacilitator (+1) |
| `lms-example/app/Models/User.php` | isAdmin, isInstructor, isFacilitator, canEditCourses, canEditCourse |
| `lms-example/app/Http/Controllers/UnitController.php` | edit, update, refreshFromFile, editQuiz, updateQuiz |
| `backend/app/Http/Controllers/AuthController.php` | issueAccessToken, issueRefreshToken, register, login, refresh |

## Entry Points

Start here when exploring this area:

- **`Tag`** (Class) — `lms-example/app/Models/Tag.php:7`
- **`Badge`** (Class) — `lms-example/app/Models/Badge.php:7`
- **`run`** (Method) — `lms-example/database/seeders/Day2Day3Seeder.php:11`
- **`fromHtml`** (Method) — `lms-example/app/Services/StepContentParser.php:45`
- **`awardPoints`** (Method) — `lms-example/app/Services/GamificationService.php:10`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `Tag` | Class | `lms-example/app/Models/Tag.php` | 7 |
| `Badge` | Class | `lms-example/app/Models/Badge.php` | 7 |
| `run` | Method | `lms-example/database/seeders/Day2Day3Seeder.php` | 11 |
| `fromHtml` | Method | `lms-example/app/Services/StepContentParser.php` | 45 |
| `awardPoints` | Method | `lms-example/app/Services/GamificationService.php` | 10 |
| `ensureBadge` | Method | `lms-example/app/Services/GamificationService.php` | 19 |
| `checkBadges` | Method | `lms-example/app/Services/GamificationService.php` | 38 |
| `isCorrectAnswer` | Method | `lms-example/app/Models/Question.php` | 32 |
| `show` | Method | `lms-example/app/Http/Controllers/LearnController.php` | 26 |
| `storeAttendance` | Method | `lms-example/app/Http/Controllers/LearnController.php` | 190 |
| `isDay1OpeningUnit` | Method | `lms-example/app/Http/Controllers/LearnController.php` | 285 |
| `curriculumOrderedUnitIds` | Method | `lms-example/app/Http/Controllers/LearnController.php` | 341 |
| `completeUnit` | Method | `lms-example/app/Http/Controllers/LearnController.php` | 366 |
| `nextUnitIdFromOrder` | Method | `lms-example/app/Http/Controllers/LearnController.php` | 406 |
| `submitQuiz` | Method | `lms-example/app/Http/Controllers/LearnController.php` | 415 |
| `recalculateProgress` | Method | `lms-example/app/Http/Controllers/LearnController.php` | 495 |
| `enrollBulkStore` | Method | `lms-example/app/Http/Controllers/CourseController.php` | 318 |
| `enroll` | Method | `lms-example/app/Http/Controllers/CourseController.php` | 546 |
| `search` | Method | `backend/app/Http/Controllers/SectionController.php` | 9 |
| `extractSnippet` | Method | `backend/app/Http/Controllers/SectionController.php` | 53 |

## Execution Flows

| Flow | Type | Steps |
|------|------|-------|
| `Register → Disk` | cross_community | 8 |
| `Register → Path` | cross_community | 8 |
| `Show → Disk` | cross_community | 7 |
| `Show → Path` | cross_community | 7 |
| `Store → Disk` | cross_community | 7 |
| `Store → Path` | cross_community | 7 |
| `Register → Disk` | cross_community | 7 |
| `Register → Path` | cross_community | 7 |
| `Index → Disk` | cross_community | 7 |
| `Index → Path` | cross_community | 7 |

## Connected Areas

| Area | Connections |
|------|-------------|
| Admin | 38 calls |
| Feature | 22 calls |
| Models | 4 calls |
| Seeders | 3 calls |
| Services | 2 calls |

## How to Explore

1. `gitnexus_context({name: "Tag"})` — see callers and callees
2. `gitnexus_query({query: "controllers"})` — find related execution flows
3. Read key files listed above for implementation details
