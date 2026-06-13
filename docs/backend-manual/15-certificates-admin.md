# 15. Certificates (admin)

## 15.1 Purpose

Manage the **government certificate workflow** (applications, payment confirmation, Presidium approval, print, collection) and maintain issued certificate records (search, revoke, reinstate).

## 15.2 Certificate applications (primary workflow)

**Navigation:** Cert. applications (`admin.certificate-applications.*`)

**Controller:** `App\Http\Controllers\Admin\CertificateApplicationsController`

| Action | Permission | Description |
|--------|------------|-------------|
| Confirm payment | `admin.action.academy_payment_confirm` | After offline fee payment |
| Presidium approve | `admin.action.academy_certificate_presidium_approve` | Creates certificate + PDF job |
| Mark printed | `admin.action.academy_certificate_print` | Physical print complete |
| Ready / collected | `admin.action.academy_certificate_collection` | Collection lifecycle |
| Download PDF | `admin.action.academy_certificate_print` | Admin print only (`print_ready`+) |

Provincial admins: queue filtered by applicant `province_id`.

Full procedures: [`../ACADEMY-CERTIFICATE-WORKFLOW.md`](../ACADEMY-CERTIFICATE-WORKFLOW.md).

## 15.3 Issued certificates (legacy management)

- `admin.certificates.index` — search and list
- `admin.certificates.revoke` — POST
- `admin.certificates.unrevoke` — POST

**Controller:** `App\Http\Controllers\Admin\CertificatesController` — uses `AuditLogger` for revoke/reinstate.

## 15.4 Public verification

- **GET `/verify-certificate`** — `certificate.verify` — public, throttled (`certificate-verify`)

## 15.5 Security deep dive

See **[`../CERTIFICATE-SECURITY.md`](../CERTIFICATE-SECURITY.md)** — verification tokens, numbering, rate limits.

## 15.6 API (students)

Under government workflow, students use **applications** endpoints — not certificate PDF download. See [23-api-academy.md](./23-api-academy.md) and [24-api-certificates.md](./24-api-certificates.md).

---

*Last reviewed: 2026-05-23 — government certificate workflow.*
