---
name: admin
description: "Skill for the Admin area of constitution. 174 symbols across 71 files."
---

# Admin

174 symbols | 71 files | Cohesion: 77%

## When to Use

- Working with code in `backend/`
- Understanding how PresidiumPublication, Controller, StaticPagesController work
- Modifying admin-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `backend/app/Http/Controllers/Admin/ConstitutionController.php` | partsIndex, partEdit, chapterEdit, versionEdit, ConstitutionController (+15) |
| `backend/app/Http/Controllers/Admin/LibraryController.php` | index, categoriesIndex, categoryCreate, categoryEdit, LibraryController (+11) |
| `backend/app/Http/Controllers/Admin/AcademyController.php` | index, indexWithSearch, courseCreate, courseEdit, assessmentsIndex (+6) |
| `backend/app/Http/Controllers/Admin/HomeBannersController.php` | index, create, edit, HomeBannersController, store (+2) |
| `backend/app/Http/Controllers/Admin/DialogueController.php` | index, DialogueController, showThread, storeMessage, inferAttachmentType (+2) |
| `backend/app/Http/Controllers/Admin/PriorityProjectsController.php` | index, PriorityProjectsController, store, update, validateProject |
| `backend/app/Http/Controllers/Admin/PresidiumAdminController.php` | index, PresidiumAdminController, store, update, validateMember |
| `backend/app/Http/Controllers/Admin/PresidiumPublicationsController.php` | index, create, edit, PresidiumPublicationsController |
| `backend/app/Http/Controllers/Admin/PartyOrgansController.php` | index, create, edit, PartyOrgansController |
| `backend/app/Http/Controllers/Admin/PartyLeaguesController.php` | index, create, edit, PartyLeaguesController |

## Entry Points

Start here when exploring this area:

- **`PresidiumPublication`** (Class) — `backend/app/Models/PresidiumPublication.php:7`
- **`Controller`** (Class) — `lms-example/app/Http/Controllers/Controller.php:4`
- **`StaticPagesController`** (Class) — `backend/app/Http/Controllers/Api/StaticPagesController.php:8`
- **`ProvinceController`** (Class) — `backend/app/Http/Controllers/Api/ProvinceController.php:8`
- **`ProfileController`** (Class) — `backend/app/Http/Controllers/Api/ProfileController.php:9`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `PresidiumPublication` | Class | `backend/app/Models/PresidiumPublication.php` | 7 |
| `Controller` | Class | `lms-example/app/Http/Controllers/Controller.php` | 4 |
| `StaticPagesController` | Class | `backend/app/Http/Controllers/Api/StaticPagesController.php` | 8 |
| `ProvinceController` | Class | `backend/app/Http/Controllers/Api/ProvinceController.php` | 8 |
| `ProfileController` | Class | `backend/app/Http/Controllers/Api/ProfileController.php` | 9 |
| `PriorityProjectsController` | Class | `backend/app/Http/Controllers/Api/PriorityProjectsController.php` | 11 |
| `PresidiumController` | Class | `backend/app/Http/Controllers/Api/PresidiumController.php` | 9 |
| `PartyOrgansController` | Class | `backend/app/Http/Controllers/Api/PartyOrgansController.php` | 8 |
| `PartyController` | Class | `backend/app/Http/Controllers/Api/PartyController.php` | 11 |
| `LibraryController` | Class | `backend/app/Http/Controllers/Api/LibraryController.php` | 12 |
| `HomeBannersController` | Class | `backend/app/Http/Controllers/Api/HomeBannersController.php` | 8 |
| `HealthController` | Class | `backend/app/Http/Controllers/Api/HealthController.php` | 10 |
| `DialogueController` | Class | `backend/app/Http/Controllers/Api/DialogueController.php` | 19 |
| `ConstitutionOfficialController` | Class | `backend/app/Http/Controllers/Api/ConstitutionOfficialController.php` | 9 |
| `CertificateController` | Class | `backend/app/Http/Controllers/Api/CertificateController.php` | 14 |
| `AppConfigController` | Class | `backend/app/Http/Controllers/Api/AppConfigController.php` | 9 |
| `AcademyCourseController` | Class | `backend/app/Http/Controllers/Api/AcademyCourseController.php` | 15 |
| `AcademyAssessmentController` | Class | `backend/app/Http/Controllers/Api/AcademyAssessmentController.php` | 17 |
| `AcademyAchievementsController` | Class | `backend/app/Http/Controllers/Api/AcademyAchievementsController.php` | 15 |
| `UsersController` | Class | `backend/app/Http/Controllers/Admin/UsersController.php` | 11 |

## Execution Flows

| Flow | Type | Steps |
|------|------|-------|
| `Register → Disk` | cross_community | 8 |
| `Register → Path` | cross_community | 8 |
| `Register → Disk` | cross_community | 7 |
| `Register → Path` | cross_community | 7 |
| `Register → Roles` | cross_community | 7 |
| `Login → Disk` | cross_community | 7 |
| `Login → Path` | cross_community | 7 |
| `Refresh → Disk` | cross_community | 7 |
| `Refresh → Path` | cross_community | 7 |
| `ForgotPassword → Disk` | cross_community | 7 |

## Connected Areas

| Area | Connections |
|------|-------------|
| Feature | 14 calls |
| Api | 3 calls |
| Controllers | 3 calls |
| Models | 1 calls |
| Support | 1 calls |

## How to Explore

1. `gitnexus_context({name: "PresidiumPublication"})` — see callers and callees
2. `gitnexus_query({query: "admin"})` — find related execution flows
3. Read key files listed above for implementation details
