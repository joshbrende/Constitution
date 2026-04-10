# 30. Core services (cross-cutting)

## 30.1 AdminAccessService

**Class:** `App\Services\AdminAccessService`

- `canAccessSection(?User $user, string $section): bool` — uses `config/admin.php`
- `hasAnyAdminAccess(?User $user): bool`
- `getAccessibleSections(?User $user): array`

## 30.2 DashboardWorkflowService

**Class:** `App\Services\DashboardWorkflowService`

- `getPendingCounts()` — draft/in-review amendments, draft courses
- `getWorkflowPanelsForUser(User)` — copy from `config/role_workflows.php`
- `getAlertLinesForUser(User)` — dashboard banners

## 30.3 CertificatePdfService

**Class:** `App\Services\CertificatePdfService`

- Fills PDF template (`public/certificate-template.pdf` or `certificate.pdf`)
- Fonts: Great Vibes TTF under `storage/app/fonts/`
- `canGenerate()`, `generate($cert)` — used by API and web preview

## 30.4 MembershipService

**Class:** `App\Services\MembershipService`

- `grantMembershipIfPassed(AssessmentAttempt)` — assigns `member` role and certificate flow when user passes assessment per business rules

## 30.5 ProvinceStatsService

**Class:** `App\Services\ProvinceStatsService`

- `getStatsForAllProvinces()` — batched analytics for admin province table
- `getProvinceRankContext($provinceId)` — mobile academy summary

## 30.6 DialogueChannelService

**Class:** `App\Services\DialogueChannelService`

- Batched unread counts and official-reply flags for dialogue API

## 30.7 AuditLogger

**Class:** `App\Services\AuditLogger`

- `log($action, $targetType, $targetId, $metadata, $request)` — writes `audit_logs`

## 30.8 Related

- [17-audit-logs.md](./17-audit-logs.md)  
- [15-certificates-admin.md](./15-certificates-admin.md)  
- [16-analytics.md](./16-analytics.md)  

---

*Last reviewed: documentation generation pass.*
