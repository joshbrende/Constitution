# Security and compliance — stakeholder brief

## Principles

1. **Least privilege** — admins see only what their role and province/district allow.
2. **Accountability** — sensitive actions are audit-logged with tamper-evident chaining.
3. **Transparency** — National ID verification status is not overstated until Gov integration is live.
4. **Defence in depth** — network controls, HTTPS, application rate limits, and session protection.

## Controls implemented

| Control | Description |
|---------|-------------|
| Role-based access | Web admin sections + API token abilities |
| Provincial / district scope | Provincial admins limited geographically |
| Setup wizard lock | `SETUP_ACCESS_TOKEN` required in production |
| Token security (mobile) | Encrypted storage via OS secure store |
| Rate limiting | Auth, assessments, certificate verify |
| Audit trail | Export, verify, optional separate audit database |
| Certificate anti-fraud | Verification codes, revocation, public verify page |
| IDOR / ownership | Policies + owner-scoped binding; push reassignment blocked — [API-SECURITY.md](../API-SECURITY.md) |
| CORS | Deny-by-default in production until origins configured |

## National ID — honest posture

**Today:** Members may be **required** to enter a Zimbabwe ID number; format is validated; **live verification against government systems is not yet connected**.

**Pilot messaging:** “Identity captured for membership records” — not “government verified” until `GOV_ID_PORTAL_BASE_URL` integration is completed.

See [INTEGRATIONS.md](../INTEGRATIONS.md) and [INSTALL-SECURITY.md](../INSTALL-SECURITY.md).

## Data protection

- Terms acceptance recorded (`accepted_terms_at`).
- Legal URLs configurable per deployment.
- National ID masked on payment receipt PDFs.
- Session encryption enabled in production Docker profile.

## Residual risks (managed)

| Risk | Mitigation |
|------|------------|
| Lost mobile device | Secure token storage; recommend device PIN/biometrics |
| Setup window abuse | Setup token + network ACL during install |
| Insider provincial abuse | Audit logs + Presidium oversight |
| Certificate forgery | Public verify + signed tokens + revocation |

## Compliance activities before national scale

1. External penetration test (auth, setup, verify endpoints).
2. Privacy impact assessment aligned with national law.
3. Data residency agreement for hosting location.
4. Incident response drill — [INCIDENT-RESPONSE.md](../INCIDENT-RESPONSE.md).
5. SOC 2 readiness tracking — [SOC2-READINESS-CHECKLIST.md](../SOC2-READINESS-CHECKLIST.md) (Type I / Type II roadmap).

## Audit for oversight bodies

Commands (ICT):

```bash
php artisan audit:verify
php artisan audit:export
```

Provincial chairs do not need shell access — national ICT provides monthly audit summaries.
