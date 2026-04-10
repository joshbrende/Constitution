---
name: seeders
description: "Skill for the Seeders area of constitution. 28 symbols across 22 files."
---

# Seeders

28 symbols | 22 files | Cohesion: 80%

## When to Use

- Working with code in `lms-example/`
- Understanding how run, run, moduleQuestions work
- Modifying seeders-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `lms-example/database/seeders/ModuleQuizzesSeeder.php` | moduleQuestions, run, defaultQuestions |
| `backend/database/seeders/MembershipCourseSeeder.php` | run, seedQuestions, getQuestionsByModule |
| `lms-example/database/seeders/SingleCourseSeeder.php` | run, extractTitle, extractDescription |
| `lms-example/database/seeders/RefreshModule1SectionsSeeder.php` | run |
| `lms-example/database/seeders/RefreshModule1IntroductionSeeder.php` | run |
| `lms-example/database/seeders/Module9SectionsSeeder.php` | run |
| `lms-example/database/seeders/Module8SectionsSeeder.php` | run |
| `lms-example/database/seeders/Module7SectionsSeeder.php` | run |
| `lms-example/database/seeders/Module6SectionsSeeder.php` | run |
| `lms-example/database/seeders/Module5SectionsSeeder.php` | run |

## Entry Points

Start here when exploring this area:

- **`run`** (Method) — `lms-example/database/seeders/RefreshModule1SectionsSeeder.php:16`
- **`run`** (Method) — `lms-example/database/seeders/RefreshModule1IntroductionSeeder.php:15`
- **`moduleQuestions`** (Method) — `lms-example/database/seeders/ModuleQuizzesSeeder.php:17`
- **`run`** (Method) — `lms-example/database/seeders/ModuleQuizzesSeeder.php:83`
- **`defaultQuestions`** (Method) — `lms-example/database/seeders/ModuleQuizzesSeeder.php:174`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `run` | Method | `lms-example/database/seeders/RefreshModule1SectionsSeeder.php` | 16 |
| `run` | Method | `lms-example/database/seeders/RefreshModule1IntroductionSeeder.php` | 15 |
| `moduleQuestions` | Method | `lms-example/database/seeders/ModuleQuizzesSeeder.php` | 17 |
| `run` | Method | `lms-example/database/seeders/ModuleQuizzesSeeder.php` | 83 |
| `defaultQuestions` | Method | `lms-example/database/seeders/ModuleQuizzesSeeder.php` | 174 |
| `run` | Method | `lms-example/database/seeders/Module9SectionsSeeder.php` | 24 |
| `run` | Method | `lms-example/database/seeders/Module8SectionsSeeder.php` | 24 |
| `run` | Method | `lms-example/database/seeders/Module7SectionsSeeder.php` | 24 |
| `run` | Method | `lms-example/database/seeders/Module6SectionsSeeder.php` | 23 |
| `run` | Method | `lms-example/database/seeders/Module5SectionsSeeder.php` | 22 |
| `run` | Method | `lms-example/database/seeders/Module4SectionsSeeder.php` | 22 |
| `run` | Method | `lms-example/database/seeders/Module3SectionsSeeder.php` | 22 |
| `run` | Method | `lms-example/database/seeders/Module2SectionsSeeder.php` | 22 |
| `run` | Method | `lms-example/database/seeders/Module1SectionsSeeder.php` | 13 |
| `run` | Method | `lms-example/database/seeders/Module12SectionsSeeder.php` | 23 |
| `run` | Method | `lms-example/database/seeders/Module11SectionsSeeder.php` | 24 |
| `run` | Method | `lms-example/database/seeders/Module10SectionsSeeder.php` | 24 |
| `toHtml` | Method | `lms-example/app/Services/StepContentParser.php` | 16 |
| `units` | Method | `lms-example/app/Models/Course.php` | 47 |
| `handle` | Method | `lms-example/app/Console/Commands/PerformanceSyncDocsCommand.php` | 16 |

## Execution Flows

| Flow | Type | Steps |
|------|------|-------|
| `Run → Disk` | cross_community | 6 |
| `Run → Path` | cross_community | 6 |
| `Run → Disk` | cross_community | 6 |
| `Run → Path` | cross_community | 6 |
| `Run → Disk` | cross_community | 6 |
| `Run → Path` | cross_community | 6 |
| `Run → Disk` | cross_community | 6 |
| `Run → Path` | cross_community | 6 |
| `Run → Disk` | cross_community | 6 |
| `Run → Path` | cross_community | 6 |

## Connected Areas

| Area | Connections |
|------|-------------|
| Feature | 14 calls |
| Models | 1 calls |
| Controllers | 1 calls |

## How to Explore

1. `gitnexus_context({name: "run"})` — see callers and callees
2. `gitnexus_query({query: "seeders"})` — find related execution flows
3. Read key files listed above for implementation details
