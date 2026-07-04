# Incident response — certificate and platform security

## Severity levels

| Level | Example | Response time |
|-------|---------|---------------|
| **P1** | Suspected mass certificate fraud, setup compromise, database breach | Immediate — national ICT + leadership |
| **P2** | Auth bypass, provincial data leak, revoked cert still verifies | < 4 hours |
| **P3** | Single account compromise, spam dialogue | < 24 hours |

## P1 — Certificate fraud or forgery suspected

1. **Preserve evidence** — export audit logs (`php artisan audit:export`).
2. **Revoke** affected certificates in admin (records `revoked_at`).
3. **Notify** Presidium and provincial chairs.
4. **Public communication** if fake certificates circulate — point to verify URL.
5. **Root cause** — PDF template, admin access, or verification cache; document in post-incident report.

## P1 — Setup wizard compromise (pre-install)

1. Block `/setup` at firewall.
2. Do not set `installed_at` until environment is rebuilt if admin was created by attacker.
3. Follow [INSTALL-SECURITY.md](./INSTALL-SECURITY.md).

## P2 — Member account compromise

1. User password reset via admin or self-service.
2. Revoke refresh tokens: user re-login required (delete `refresh_tokens` for user if needed).
3. Review audit log for actions by that user.

## P2 — Provincial admin out of scope

1. Verify `province_id` / `district_id` on admin account.
2. Review `audit_logs` for `admin.*` actions by that user.
3. Correct role assignment via national system administrator.

## Contacts (fill per deployment)

| Role | Contact |
|------|---------|
| National ICT on-call | |
| Database administrator | |
| Presidium liaison | |
| Legal / communications | |

## Post-incident

- Update [GAP-REMEDIATION-LOG.md](./politburo/GAP-REMEDIATION-LOG.md) if new controls added.
- Brief Politburo if member data or certificate integrity affected.
