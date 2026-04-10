# 24. API — Certificates

**Controller:** `App\Http\Controllers\Api\CertificateController`

| Method | Path | Throttle | Notes |
|--------|------|----------|-------|
| GET | `/api/v1/certificates/preview` | — | PDF preview |
| GET | `/api/v1/certificates` | — | List user certificates |
| POST | `/api/v1/certificates/{certificate}/generate` | `certificates` | Queue/generate PDF |
| GET | `/api/v1/certificates/{certificate}/pdf` | `certificates` | Download; may return 202 until ready |

Implementation details: `CertificatePdfService`, template files under `public/`.

See also [15-certificates-admin.md](./15-certificates-admin.md) and [`../CERTIFICATE-SECURITY.md`](../CERTIFICATE-SECURITY.md).

---

*Last reviewed: documentation generation pass.*
