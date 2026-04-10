---
name: models
description: "Skill for the Models area of constitution. 38 symbols across 25 files."
---

# Models

38 symbols | 25 files | Cohesion: 68%

## When to Use

- Working with code in `backend/`
- Understanding how User, User, hasRole work
- Modifying models-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `lms-example/app/Models/CertificateTemplate.php` | courses, isPublicPath, getFullPathAttribute, fileExists |
| `lms-example/app/Services/CertificatePdfService.php` | getBackgroundPath, generate, registerScriptFont |
| `lms-example/app/Models/CertificateSignature.php` | getBoardOfFacultyPath, getSupervisorPath, getFacilitatorPath |
| `lms-example/app/Models/User.php` | hasRole, User |
| `backend/app/Services/MembershipService.php` | defaultCertificateExpiry, grantMembershipIfPassed |
| `backend/app/Policies/AdminContentPolicy.php` | presidiumPublish, contentManage |
| `backend/app/Http/Controllers/Admin/PartyController.php` | index, attachSection |
| `lms-example/app/Http/Controllers/SubmissionsController.php` | update, recalculateProgress |
| `backend/app/Http/Controllers/Admin/AcademyController.php` | questionStore, questionUpdate |
| `backend/database/seeders/AdminUserSeeder.php` | run |

## Entry Points

Start here when exploring this area:

- **`User`** (Class) — `lms-example/app/Models/User.php:14`
- **`User`** (Class) — `backend/app/Models/User.php:14`
- **`hasRole`** (Method) — `lms-example/app/Models/User.php:54`
- **`run`** (Method) — `backend/database/seeders/AdminUserSeeder.php:13`
- **`defaultCertificateExpiry`** (Method) — `backend/app/Services/MembershipService.php:17`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `User` | Class | `lms-example/app/Models/User.php` | 14 |
| `User` | Class | `backend/app/Models/User.php` | 14 |
| `hasRole` | Method | `lms-example/app/Models/User.php` | 54 |
| `run` | Method | `backend/database/seeders/AdminUserSeeder.php` | 13 |
| `defaultCertificateExpiry` | Method | `backend/app/Services/MembershipService.php` | 17 |
| `grantMembershipIfPassed` | Method | `backend/app/Services/MembershipService.php` | 31 |
| `registerAdminGates` | Method | `backend/app/Providers/AppServiceProvider.php` | 48 |
| `presidiumPublish` | Method | `backend/app/Policies/AdminContentPolicy.php` | 15 |
| `contentManage` | Method | `backend/app/Policies/AdminContentPolicy.php` | 27 |
| `nextCertificateNumber` | Method | `backend/app/Models/Certificate.php` | 57 |
| `getBackgroundPath` | Method | `lms-example/app/Services/CertificatePdfService.php` | 31 |
| `courses` | Method | `lms-example/app/Models/CertificateTemplate.php` | 13 |
| `isPublicPath` | Method | `lms-example/app/Models/CertificateTemplate.php` | 19 |
| `getFullPathAttribute` | Method | `lms-example/app/Models/CertificateTemplate.php` | 25 |
| `fileExists` | Method | `lms-example/app/Models/CertificateTemplate.php` | 33 |
| `destroy` | Method | `lms-example/app/Http/Controllers/CertificateTemplatesController.php` | 109 |
| `generate` | Method | `lms-example/app/Services/CertificatePdfService.php` | 81 |
| `registerScriptFont` | Method | `lms-example/app/Services/CertificatePdfService.php` | 163 |
| `getBoardOfFacultyPath` | Method | `lms-example/app/Models/CertificateSignature.php` | 19 |
| `getSupervisorPath` | Method | `lms-example/app/Models/CertificateSignature.php` | 26 |

## Execution Flows

| Flow | Type | Steps |
|------|------|-------|
| `Register → Disk` | cross_community | 8 |
| `Register → Path` | cross_community | 8 |
| `Store → Disk` | cross_community | 7 |
| `Store → Path` | cross_community | 7 |
| `Register → Disk` | cross_community | 7 |
| `Register → Path` | cross_community | 7 |
| `Index → Disk` | cross_community | 7 |
| `Index → Path` | cross_community | 7 |
| `PreviewPdf → IsPublicPath` | cross_community | 7 |
| `Update → Disk` | cross_community | 7 |

## Connected Areas

| Area | Connections |
|------|-------------|
| Feature | 7 calls |
| Controllers | 7 calls |
| Admin | 5 calls |
| Services | 1 calls |
| Api | 1 calls |

## How to Explore

1. `gitnexus_context({name: "User"})` — see callers and callees
2. `gitnexus_query({query: "models"})` — find related execution flows
3. Read key files listed above for implementation details
