---
name: govidverification
description: "Skill for the GovIdVerification area of constitution. 5 symbols across 5 files."
---

# GovIdVerification

5 symbols | 5 files | Cohesion: 80%

## When to Use

- Working with code in `backend/`
- Understanding how GovIdVerificationResult, forBaseUrl, hasVerifiedNationalId work
- Modifying govidverification-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `backend/app/Support/GovIntegrationClient.php` | forBaseUrl |
| `backend/app/Models/User.php` | hasVerifiedNationalId |
| `backend/app/Services/GovIdVerification/GovIdVerificationResult.php` | GovIdVerificationResult |
| `backend/app/Services/GovIdVerification/GovIdVerificationClient.php` | verifyNationalId |
| `backend/app/Http/Controllers/Api/AcademyCourseController.php` | enrol |

## Entry Points

Start here when exploring this area:

- **`GovIdVerificationResult`** (Class) — `backend/app/Services/GovIdVerification/GovIdVerificationResult.php:4`
- **`forBaseUrl`** (Method) — `backend/app/Support/GovIntegrationClient.php:20`
- **`hasVerifiedNationalId`** (Method) — `backend/app/Models/User.php:61`
- **`verifyNationalId`** (Method) — `backend/app/Services/GovIdVerification/GovIdVerificationClient.php:16`
- **`enrol`** (Method) — `backend/app/Http/Controllers/Api/AcademyCourseController.php:65`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `GovIdVerificationResult` | Class | `backend/app/Services/GovIdVerification/GovIdVerificationResult.php` | 4 |
| `forBaseUrl` | Method | `backend/app/Support/GovIntegrationClient.php` | 20 |
| `hasVerifiedNationalId` | Method | `backend/app/Models/User.php` | 61 |
| `verifyNationalId` | Method | `backend/app/Services/GovIdVerification/GovIdVerificationClient.php` | 16 |
| `enrol` | Method | `backend/app/Http/Controllers/Api/AcademyCourseController.php` | 65 |

## Connected Areas

| Area | Connections |
|------|-------------|
| Feature | 1 calls |
| Admin | 1 calls |

## How to Explore

1. `gitnexus_context({name: "GovIdVerificationResult"})` — see callers and callees
2. `gitnexus_query({query: "govidverification"})` — find related execution flows
3. Read key files listed above for implementation details
