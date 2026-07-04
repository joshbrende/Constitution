# Integrations (government ICT / internal network)

This deployment is intended for **internal government servers** (controlled network). To comply with internal ICT policies, external integrations must be explicit, allowlisted, and auditable.

## Principles
- **Deny-by-default outbound**: the backend must only call approved internal services.
- **Single integration entrypoint**: do not call other services directly from controllers.
- **Traceability**: include `X-Request-Id` on outbound calls and log it.
- **Least privilege**: use service identities/credentials with minimal scope.

## Approved patterns (pick per service)
- **API gateway** (preferred where available): gateway terminates auth, enforces policy.
- **Direct internal service calls**: private network + allowlisted hosts; ideally **mTLS**.
- **Message bus**: async integration where appropriate (audit-heavy workflows).

## How to add a new internal service
1. Add the service base URL to env/config (never hardcode).
2. Add its host to the outbound allowlist.
3. Implement calls through the integration wrapper:
   - timeouts
   - retries (if safe)
   - consistent headers (`X-Request-Id`)
4. Add audit logging if the call is privileged or affects records.

## Configuration (backend)
- `INTEGRATION_ALLOWLIST_HOSTS`
  - Comma-separated hosts allowed for outbound calls.
  - Example: `idm.gov.local,gateway.gov.local,hr.gov.local`

## Implementation in this repo
- Backend integration wrapper:
  - `backend/app/Support/GovIntegrationClient.php`
- National ID verification client (stub until Gov portal MOU):
  - `backend/app/Services/GovIdVerificationClient.php`
- Config:
  - `backend/config/integrations.php`

## National ID verification — current status

| Capability | Status |
|------------|--------|
| Collect Zimbabwe ID on profile | **Live** |
| Format validation (`ZimbabweNationalIdRule`) | **Live** |
| Require ID before assessments | **Live** (site setting) |
| Live verification against government portal | **Not implemented** — returns `unavailable` |

**Stakeholder messaging:** Do not describe membership as “government ID verified” until `GOV_ID_PORTAL_BASE_URL` is configured and `GovIdVerificationClient` is implemented against the signed MOU API.

Env flags:
- `GOV_ID_PORTAL_BASE_URL`
- `GOV_ID_ENFORCE_MEMBERSHIP_VERIFICATION` (default false)

## Academy payment — current status

| Capability | Status |
|------------|--------|
| Exam pass → payment receipt PDF | **Live** |
| Structured receipt number (`ZPF-REC-{YYYY}-{PROV}-{SEQ}`) | **Live** |
| Payment reference + public verify page (`/verify-receipt`) | **Live** |
| Offline payment at party office | **Live** (operational procedure) |
| Admin confirms payment + teller note | **Live** (`payment_reference_note`) |
| Live integration with Treasury / fiscal gateway | **Not implemented** |

### How payments work today (v1)

1. Student passes assessment → system issues receipt with **receipt number**, **payment reference**, and **public verify URL**.
2. Student pays **cash at a ZANU PF office** (offline).
3. Finance staff can verify the receipt on `/verify-receipt` before accepting payment.
4. Provincial / academy admin enters **Confirm payment** in the dashboard and records an optional **teller / government reference** in `payment_reference_note`.
5. Workflow continues: Presidium → print → collection.

No automatic bank or mobile-money settlement is performed by this platform in v1.

### What to integrate for government electronic payments (future)

National ICT must sign an **MOU** with the paying authority and implement a single client (same pattern as `GovIdVerificationClient`):

| Integration option | Typical provider | Use when |
|--------------------|------------------|----------|
| **Treasury / IFMIS receipting** | Ministry of Finance, Accountant-General | Official government fee collection with fiscal receipt |
| **ZIMRA fiscalisation** | ZIMRA FDMS / fiscal devices | VAT/fiscal tax invoices required for fees |
| **National payment gateway** | Government API gateway (MoICT) | Centralised card/bank rails for ministries |
| **Mobile money merchant** | EcoCash / OneMoney / InnBucks (via approved aggregator) | Members pay digitally; webhook confirms settlement |
| **Bank EFT / RTGS reference** | Commercial bank API | Corporate or bulk settlement with reference matching |

**Recommended architecture:**

```
Mobile app / Receipt PDF
        ↓
Laravel (CertificateApplicationService)
        ↓
GovPaymentClient (new) → allowlisted gateway host
        ↓
Webhook: payment.settled → auto confirmPayment() + audit log
```

**Env placeholders (not yet in code):**

- `GOV_PAYMENT_GATEWAY_BASE_URL`
- `GOV_PAYMENT_MERCHANT_ID`
- `GOV_PAYMENT_WEBHOOK_SECRET`

Until integration is live, continue **offline confirmation** and record external references in `payment_reference_note`.

Operator runbook: [ACADEMY-CERTIFICATE-WORKFLOW.md](./ACADEMY-CERTIFICATE-WORKFLOW.md)

