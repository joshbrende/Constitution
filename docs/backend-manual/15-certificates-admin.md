# 15. Certificates (admin)

## 15.1 Purpose

Search, verify, **revoke**, and **reinstate** membership certificates issued by the system.

## 15.2 Routes

- `admin.certificates.index` — search and list
- `admin.certificates.revoke` — POST
- `admin.certificates.unrevoke` — POST

**Controller:** `App\Http\Controllers\Admin\CertificatesController` — uses `AuditLogger` for revoke/reinstate.

## 15.3 Public verification

- **GET `/verify-certificate`** — `certificate.verify` — public, throttled (`certificate-verify`)

## 15.4 Security deep dive

See **[`../CERTIFICATE-SECURITY.md`](../CERTIFICATE-SECURITY.md)** — verification tokens, numbering, rate limits.

## 15.5 API

See [22-api-certificates.md](./22-api-certificates.md).

---

*Last reviewed: documentation generation pass.*
