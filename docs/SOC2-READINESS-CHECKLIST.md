# SOC 2 readiness checklist

**Purpose:** Map the ZANU PF Constitution & Academy platform to AICPA Trust Service Criteria (TSC) and record readiness for an external SOC 2 engagement.

**Important:** This document is **not** a SOC 2 report. Certification requires an independent CPA firm audit (Type I = design at a point in time; Type II = operating effectiveness over 6–12 months).

**Current posture:** **SOC 2–oriented** — strong application controls; organizational and assurance evidence gaps remain.

**Last updated:** 2026-05-23

---

## Recommended audit scope

| TSC category | Include? | Rationale |
|--------------|----------|-----------|
| **Security** (CC1–CC9) | **Yes** | Core — auth, RBAC, audit, hardening |
| **Availability** | **Yes** | Health endpoint, queue worker, ops runbook |
| **Processing integrity** | **Yes** | Certificate workflow, assessment scoring |
| **Confidentiality** | **Yes** | National ID, PII, provincial scope |
| **Privacy** | **Partial / optional** | Member data; formal privacy program not yet complete |

---

## Summary scorecard

| Status | Count | Meaning |
|--------|-------|---------|
| **Done** | 28 | Implemented with evidence in repo or ops docs |
| **Partial** | 22 | Designed or documented; needs process evidence or completion |
| **Missing** | 14 | Not implemented or no formal policy |

Use the detailed tables below to drive remediation. Target **Partial → Done** before Type I; operate controls **6–12 months** before Type II.

---

## CC1 — Control environment

| ID | Control | Status | Evidence / location | Gap / next action |
|----|---------|--------|---------------------|-------------------|
| CC1.1 | Leadership commitment to security | **Partial** | [SECURITY-AND-COMPLIANCE.md](./politburo/SECURITY-AND-COMPLIANCE.md), Politburo pack | Formal security policy signed by leadership |
| CC1.2 | Board / oversight accountability | **Partial** | Politburo briefing pack | Named security officer + reporting cadence |
| CC1.3 | Organizational structure & roles | **Done** | [RBAC-MATRIX.md](./RBAC-MATRIX.md), `config/admin.php`, `RoleSeeder` | — |
| CC1.4 | Competence & training | **Missing** | In-app admin docs v1.1.0 | Admin security training records; onboarding checklist |
| CC1.5 | Accountability for security | **Partial** | Role workflows, audit logs | Job descriptions linking roles to access |

---

## CC2 — Communication and information

| ID | Control | Status | Evidence / location | Gap / next action |
|----|---------|--------|---------------------|-------------------|
| CC2.1 | Internal security communication | **Partial** | [INCIDENT-RESPONSE.md](./INCIDENT-RESPONSE.md), in-app Help | Fill incident contacts; security bulletin process |
| CC2.2 | External communication (members) | **Partial** | Legal URLs, static pages, member announcement template | Privacy notice aligned with national law |
| CC2.3 | System documentation | **Done** | `docs/backend-manual/`, in-app Documentation v1.1.0 | Keep doc version bumped on material changes |

---

## CC3 — Risk assessment

| ID | Control | Status | Evidence / location | Gap / next action |
|----|---------|--------|---------------------|-------------------|
| CC3.1 | Risk identification | **Partial** | [GAP-REMEDIATION-LOG.md](./politburo/GAP-REMEDIATION-LOG.md), SECURITY brief residual risks | Formal annual risk register |
| CC3.2 | Fraud risk (certificates) | **Partial** | [CERTIFICATE-SECURITY.md](./CERTIFICATE-SECURITY.md), verify endpoint, revocation | External pen test |
| CC3.3 | Change-related risk | **Partial** | CI workflows, CHANGELOG | Formal change advisory board / CAB lite for prod |

---

## CC4 — Monitoring activities

| ID | Control | Status | Evidence / location | Gap / next action |
|----|---------|--------|---------------------|-------------------|
| CC4.1 | Ongoing monitoring | **Partial** | `GET /api/v1/health`, `ops:queue-health`, Slack webhook config | SIEM or centralized log aggregation |
| CC4.2 | Deficiency evaluation | **Partial** | GAP-REMEDIATION-LOG | Quarterly control review minutes |
| CC4.3 | Audit log review | **Partial** | `audit:verify`, `audit:export`, admin Audit logs UI | Scheduled monthly audit review + sign-off |

**Commands:**

```bash
php artisan audit:verify
php artisan audit:export
php artisan ops:queue-health --json
curl -s https://your-domain/api/v1/health
```

---

## CC5 — Control activities

| ID | Control | Status | Evidence / location | Gap / next action |
|----|---------|--------|---------------------|-------------------|
| CC5.1 | Policies drive control deployment | **Partial** | PRODUCTION-HARDENING, INSTALL-SECURITY | Publish policy pack (access, change, backup) |
| CC5.2 | Technology controls | **Done** | RBAC, rate limits, CORS, setup token, district scope | — |
| CC5.3 | Deployment of controls via procedures | **Partial** | Setup wizard, seeders, OPS-RUNBOOK | Standard operating procedures (SOPs) with owners |

---

## CC6 — Logical and physical access

| ID | Control | Status | Evidence / location | Gap / next action |
|----|---------|--------|---------------------|-------------------|
| CC6.1 | Logical access provisioning | **Done** | Admin → Users, invite flow, `BackendUserInvitation` | — |
| CC6.2 | Authentication | **Partial** | Sanctum API tokens, web session, secure store (mobile) | **MFA for admin accounts** |
| CC6.3 | Authorization (least privilege) | **Done** | `EnsureAdminSection`, `AdminScopeService`, token abilities | — |
| CC6.4 | Access removal | **Partial** | User edit / role removal | Offboarding SOP + quarterly access review |
| CC6.5 | Physical access | **Missing** | Hosting provider dependent | Data centre / cloud SOC report from host |
| CC6.6 | Encryption in transit | **Partial** | TLS documented in PRODUCTION-HARDENING | Enforce HTTPS-only in production |
| CC6.7 | Encryption at rest | **Partial** | `SESSION_ENCRYPT`, DB depends on host | DB encryption at rest (MySQL/hosting); backup encryption |

---

## CC7 — System operations

| ID | Control | Status | Evidence / location | Gap / next action |
|----|---------|--------|---------------------|-------------------|
| CC7.1 | Infrastructure & capacity | **Partial** | Docker prod profile, LOAD-BALANCER.md | Capacity planning doc |
| CC7.2 | Backup & recovery | **Partial** | RECONSTRUCTION.md, OPS-RUNBOOK rollback | **Tested restore** with dated evidence |
| CC7.3 | Recovery testing | **Missing** | RECONSTRUCTION.md (theory) | Annual DR drill + report |
| CC7.4 | Environment separation | **Done** | dev/staging/prod in eas.json, docker-compose.prod.yml | — |
| CC7.5 | Incident response | **Partial** | INCIDENT-RESPONSE.md | Fill contacts; **run tabletop drill** |
| CC7.6 | Vulnerability management | **Partial** | Dependabot, CodeQL, Semgrep CI | External pen test; patch SLA |

**CI security workflows:** `.github/workflows/backend-tests.yml`, `security-scan.yml`, `codeql.yml`, `semgrep.yml`, `mobile-ci.yml`

---

## CC8 — Change management

| ID | Control | Status | Evidence / location | Gap / next action |
|----|---------|--------|---------------------|-------------------|
| CC8.1 | Change authorization | **Partial** | Git + PR workflow (recommended) | Prod deploy approval record |
| CC8.2 | Testing before release | **Done** | `php artisan test`, feature tests, mobile CI | — |
| CC8.3 | Emergency changes | **Missing** | OPS-RUNBOOK rollback | Emergency change procedure |
| CC8.4 | Migration safety | **Partial** | OPS-RUNBOOK “backup before migrate” | Enforce in deploy checklist |

---

## CC9 — Risk mitigation (vendors & subprocessors)

| ID | Control | Status | Evidence / location | Gap / next action |
|----|---------|--------|---------------------|-------------------|
| CC9.1 | Vendor inventory | **Missing** | — | List: hosting, SMTP, Expo/EAS, Apple/Google |
| CC9.2 | Vendor SOC reports | **Missing** | — | Collect host/SMTP provider SOC 2 or equivalent |
| CC9.3 | Data residency | **Missing** | SECURITY brief mentions need | Written hosting location + legal agreement |

---

## Availability (A1)

| ID | Control | Status | Evidence / location | Gap / next action |
|----|---------|--------|---------------------|-------------------|
| A1.1 | Uptime monitoring | **Partial** | `/api/v1/health`, queue health command | External uptime monitor + alerting |
| A1.2 | Queue / async processing | **Done** | Queue worker in Docker, failed job thresholds | — |
| A1.3 | Scheduled tasks | **Partial** | OPS-RUNBOOK cron | Verify cron on production with evidence |
| A1.4 | Incident communication | **Partial** | INCIDENT-RESPONSE severity table | Member-facing status page (optional) |

---

## Processing integrity (PI1)

| ID | Control | Status | Evidence / location | Gap / next action |
|----|---------|--------|---------------------|-------------------|
| PI1.1 | Certificate workflow integrity | **Done** | State machine, staged approvals, audit events | — |
| PI1.2 | Assessment scoring | **Done** | Academy admin, attempt audit events | — |
| PI1.3 | Input validation | **Done** | Laravel validation, INPUT-SANITIZATION.md, mobile Zod (`mobile/src/lib/validation.js`) | — |
| PI1.4 | Error handling | **Partial** | API error envelopes | Optional Sentry (`SENTRY_LARAVEL_DSN` in operations.php comment) |

**Key audit events:** `academy.application.*`, `certificate.revoked`, constitution version approvals — see [AUDIT-LOGGING.md](./AUDIT-LOGGING.md).

---

## Confidentiality (C1)

| ID | Control | Status | Evidence / location | Gap / next action |
|----|---------|--------|---------------------|-------------------|
| C1.1 | PII access limitation | **Done** | Provincial scope, PII view audit event | — |
| C1.2 | National ID handling | **Partial** | Masked on receipt PDF; format validation | Gov verification not live — honest messaging |
| C1.3 | Confidential data in logs | **Partial** | Audit metadata design | Log review policy — no secrets in logs |
| C1.4 | Mobile token protection | **Done** | expo-secure-store | — |
| C1.5 | Mobile client form validation | **Done** | Zod + sanitize before API (`mobile/src/lib/`) | — |

---

## Privacy (P1–P8) — if Privacy TSC in scope

| ID | Control | Status | Evidence / location | Gap / next action |
|----|---------|--------|---------------------|-------------------|
| P1 | Privacy notice | **Partial** | Terms, privacy static pages, `accepted_terms_at` | **Privacy impact assessment (PIA)** |
| P2 | Choice & consent | **Partial** | Registration terms acceptance | Cookie/consent if analytics added |
| P3 | Collection limitation | **Done** | Profile fields documented | — |
| P4 | Use limitation | **Partial** | Role-based access | Data use policy for provincial admins |
| P5 | Access / correction | **Partial** | Profile API, admin user edit | Member data request procedure |
| P6 | Disclosure | **Missing** | — | Subprocessor list in privacy policy |
| P7 | Quality | **Partial** | National ID format validation | Gov ID integration when MOU signed |
| P8 | Monitoring & enforcement | **Missing** | — | Privacy officer + breach notification SOP |

---

## Application controls — quick reference

| Control | Status | Code / config |
|---------|--------|---------------|
| RBAC (web + API) | **Done** | `config/admin.php`, `TokenAbilityService`, `EnsureAdminSection` |
| Provincial / district scope | **Done** | `AdminScopeService`, `config/scoping.php` |
| Audit hash chain | **Done** | `config/audit.php`, `AuditLogger`, `audit:verify` |
| Separate audit DB | **Partial** | `AUDIT_DB_CONNECTION` — optional, must be configured in prod |
| Setup wizard lock | **Done** | `EnsureSetupAccess`, `SETUP_ACCESS_TOKEN` |
| Rate limiting | **Done** | Auth, assessments, certificate verify |
| CORS deny-by-default (prod) | **Done** | `config/cors.php` |
| Session encryption | **Partial** | `SESSION_ENCRYPT` — enable in production |
| Certificate anti-fraud | **Done** | Public verify, revocation, audit trail |
| Guest browse (minimal exposure) | **Done** | Mobile guest context |
| Production Docker hardening | **Done** | `docker-compose.prod.yml` read-only code |

---

## Roadmap to SOC 2

### Phase A — Readiness (0–3 months)

| Priority | Action | Owner |
|----------|--------|-------|
| P0 | Engage SOC 2 readiness consultant or auditor for scoping | National ICT |
| P0 | **External penetration test** (auth, setup, verify, admin) | Security |
| P0 | **Privacy impact assessment** | Legal + ICT |
| P1 | Implement **MFA** for all backend admin accounts | Development |
| P1 | Publish policy pack: access, change, backup, incident | ICT + legal |
| P1 | Fill incident contacts; run **tabletop IR drill** | ICT |
| P1 | Vendor/subprocessor inventory + host SOC report | ICT |
| P2 | Configure `AUDIT_DB_CONNECTION` + archive to cold storage | Ops |
| P2 | Centralized logging / SIEM (or managed service) | Ops |

### Phase B — Type I (design)

| Step | Action |
|------|--------|
| 1 | Remediate **Missing** and critical **Partial** items |
| 2 | Collect design evidence (screenshots, configs, policies) |
| 3 | Auditor issues **SOC 2 Type I** report |

### Phase C — Type II (operating effectiveness)

| Step | Action |
|------|--------|
| 1 | Operate controls for **6–12 months** |
| 2 | Monthly: access review, audit log sample, backup verify |
| 3 | Quarterly: control owner attestation |
| 4 | Auditor issues **SOC 2 Type II** report |

---

## Evidence collection template

For each **Done** control, retain:

| Evidence type | Example |
|---------------|---------|
| Configuration | Redacted `.env` keys, `config/*.php` exports |
| Procedure | Dated SOP PDF or markdown with owner |
| Operation | Cron screenshot, queue worker logs, health check alerts |
| Review | Signed access review spreadsheet (quarterly) |
| Test | Pen test report, restore test log, IR drill minutes |

Store evidence in a dedicated compliance folder (not in git if it contains PII).

---

## Sign-off (readiness gate)

| Role | Name | Date | Ready for Type I scoping? |
|------|------|------|-------------------------|
| National ICT lead | | | ☐ Yes ☐ No |
| Security officer | | | ☐ Yes ☐ No |
| Legal / privacy | | | ☐ Yes ☐ No |
| Presidium representative | | | ☐ Informed |

---

## Related documents

| Document | Topic |
|----------|--------|
| [SECURITY-AND-COMPLIANCE.md](./politburo/SECURITY-AND-COMPLIANCE.md) | Stakeholder security brief |
| [GAP-REMEDIATION-LOG.md](./politburo/GAP-REMEDIATION-LOG.md) | Pre-pilot hardening |
| [AUDIT-LOGGING.md](./AUDIT-LOGGING.md) | Audit operations |
| [PRODUCTION-HARDENING.md](./PRODUCTION-HARDENING.md) | Go-live checklist |
| [INCIDENT-RESPONSE.md](./INCIDENT-RESPONSE.md) | Incident playbooks |
| [INSTALL-SECURITY.md](./INSTALL-SECURITY.md) | Setup security |
| [CERTIFICATE-SECURITY.md](./CERTIFICATE-SECURITY.md) | Certificate fraud controls |
| [19-provincial-pilot-rollout.md](./backend-manual/19-provincial-pilot-rollout.md) | Pilot operations |

---

*This checklist should be reviewed after each major release and before Politburo phase-transition decisions.*
