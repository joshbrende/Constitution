---
name: services
description: "Skill for the Services area of constitution. 42 symbols across 18 files."
---

# Services

42 symbols | 18 files | Cohesion: 74%

## When to Use

- Working with code in `backend/`
- Understanding how getAmendmentPipelineCounts, getAmendmentStatusHintsForUser, canAccessSection work
- Modifying services-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `backend/app/Services/DialogueChannelService.php` | channelsForUser, unreadCountsByThread, hasOfficialReplyByChannel, mapChannelsWithoutUser, mapChannelsWithCounts |
| `backend/app/Services/AmendmentOfficialPdfService.php` | disk, path, absoluteUrl, publicPath, urlForRequest |
| `backend/app/Services/AdminAccessService.php` | canAccessSection, getAccessibleSections, getAllAdminRoleSlugs, hasAnyAdminAccess |
| `backend/app/Services/CertificatePdfService.php` | getTemplatePath, drawArchedText, registerGreatVibesFont, generate |
| `lms-example/app/Services/CertificatePdfService.php` | backgroundExists, getDefaultBackgroundPath, supportsBackgroundPdf |
| `lms-example/app/Http/Controllers/CertificateController.php` | previewPdf, makeSampleCertificate, downloadPdf |
| `backend/app/Services/DashboardWorkflowService.php` | getPendingCounts, getWorkflowPanelsForUser, getAlertLinesForUser |
| `backend/app/Services/RoleWorkflowService.php` | getAmendmentPipelineCounts, getAmendmentStatusHintsForUser |
| `backend/app/Http/Middleware/EnsureAdminSection.php` | handle, inferSection |
| `backend/app/Http/Controllers/Admin/AdminGuideController.php` | documentation, help |

## Entry Points

Start here when exploring this area:

- **`getAmendmentPipelineCounts`** (Method) — `backend/app/Services/RoleWorkflowService.php:18`
- **`getAmendmentStatusHintsForUser`** (Method) — `backend/app/Services/RoleWorkflowService.php:61`
- **`canAccessSection`** (Method) — `backend/app/Services/AdminAccessService.php:24`
- **`getAccessibleSections`** (Method) — `backend/app/Services/AdminAccessService.php:60`
- **`handle`** (Method) — `backend/app/Http/Middleware/EnsureAdminSection.php:36`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `getAmendmentPipelineCounts` | Method | `backend/app/Services/RoleWorkflowService.php` | 18 |
| `getAmendmentStatusHintsForUser` | Method | `backend/app/Services/RoleWorkflowService.php` | 61 |
| `canAccessSection` | Method | `backend/app/Services/AdminAccessService.php` | 24 |
| `getAccessibleSections` | Method | `backend/app/Services/AdminAccessService.php` | 60 |
| `handle` | Method | `backend/app/Http/Middleware/EnsureAdminSection.php` | 36 |
| `inferSection` | Method | `backend/app/Http/Middleware/EnsureAdminSection.php` | 51 |
| `documentation` | Method | `backend/app/Http/Controllers/Admin/AdminGuideController.php` | 41 |
| `help` | Method | `backend/app/Http/Controllers/Admin/AdminGuideController.php` | 68 |
| `getTemplatePath` | Method | `backend/app/Services/CertificatePdfService.php` | 40 |
| `drawArchedText` | Method | `backend/app/Services/CertificatePdfService.php` | 63 |
| `registerGreatVibesFont` | Method | `backend/app/Services/CertificatePdfService.php` | 99 |
| `generate` | Method | `backend/app/Services/CertificatePdfService.php` | 126 |
| `signedVerificationToken` | Method | `backend/app/Models/Certificate.php` | 96 |
| `hasValidVerificationToken` | Method | `backend/app/Models/Certificate.php` | 116 |
| `show` | Method | `backend/app/Http/Controllers/CertificateVerificationController.php` | 16 |
| `backgroundExists` | Method | `lms-example/app/Services/CertificatePdfService.php` | 56 |
| `getDefaultBackgroundPath` | Method | `lms-example/app/Services/CertificatePdfService.php` | 63 |
| `supportsBackgroundPdf` | Method | `lms-example/app/Services/CertificatePdfService.php` | 73 |
| `previewPdf` | Method | `lms-example/app/Http/Controllers/CertificateController.php` | 15 |
| `makeSampleCertificate` | Method | `lms-example/app/Http/Controllers/CertificateController.php` | 44 |

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
| Admin | 7 calls |
| Models | 5 calls |
| Controllers | 4 calls |
| Api | 2 calls |
| Feature | 1 calls |
| Policies | 1 calls |

## How to Explore

1. `gitnexus_context({name: "getAmendmentPipelineCounts"})` — see callers and callees
2. `gitnexus_query({query: "services"})` — find related execution flows
3. Read key files listed above for implementation details
