# 24. API — Certificates

**Controller:** `App\Http\Controllers\Api\CertificateController`

## Government workflow (default)

When `config('academy.student_certificate_download_enabled')` is **false** (default):

| Method | Path | Behaviour |
|--------|------|-----------|
| GET | `/api/v1/certificates` | Returns `data: []` + `meta.certificates_disabled: true`; points to `/api/v1/academy/applications` |
| POST | `/api/v1/certificates/{certificate}/generate` | **403** — use payment receipt workflow |
| GET | `/api/v1/certificates/{certificate}/pdf` | **403** — certificates collected at party office |

Students track status via [23-api-academy.md](./23-api-academy.md) applications endpoints.

## Legacy student download

When `ACADEMY_STUDENT_CERTIFICATE_DOWNLOAD=true`:

| Method | Path | Throttle | Notes |
|--------|------|----------|-------|
| GET | `/api/v1/certificates/preview` | — | PDF preview |
| GET | `/api/v1/certificates` | — | List user certificates |
| POST | `/api/v1/certificates/{certificate}/generate` | `certificates` | Queue/generate PDF |
| GET | `/api/v1/certificates/{certificate}/pdf` | `certificates` | Download; may return 202 until ready |

Policy: owner only; route binding scoped on API.

## Admin print

Certificate PDF for printing is **admin-only** after Presidium approval — see [15-certificates-admin.md](./15-certificates-admin.md) and `admin.certificate-applications.certificate-pdf`.

Implementation: `CertificatePdfService`, templates under `resources/views/pdf/`.

See also [`../CERTIFICATE-SECURITY.md`](../CERTIFICATE-SECURITY.md) and [`../ACADEMY-CERTIFICATE-WORKFLOW.md`](../ACADEMY-CERTIFICATE-WORKFLOW.md).

---

*Last reviewed: 2026-05-23 — government certificate workflow.*
