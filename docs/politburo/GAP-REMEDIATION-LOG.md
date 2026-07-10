# Gap remediation log — pre-rollout hardening

This records gaps identified in the CTO adoption assessment and their remediation status before provincial pilot.

| Gap | Remediation | Status |
|-----|-------------|--------|
| Setup wizard publicly accessible during install | `EnsureSetupAccess` middleware + `SETUP_ACCESS_TOKEN`; production blocks if unset | **Done** |
| Mobile tokens in plaintext AsyncStorage | `expo-secure-store` with legacy migration | **Done** |
| No mobile CI | `.github/workflows/mobile-ci.yml` | **Done** |
| EAS placeholder URLs / wrong dev port | `eas.json` updated (8081 dev, staging/production URLs) | **Done** |
| Login wall on mobile | Guest constitution browse + banner | **Done** |
| Provincial scope only | District scoping when admin has `district_id` | **Done** |
| Admin route section fall-through | Deny unmapped routes; open guide; controller-gated platform settings | **Done** |
| Docker dev mounts in production | `docker-compose.prod.yml` read-only + storage volume | **Done** |
| SESSION_ENCRYPT not documented | `.env.example` + prod compose | **Done** |
| Missing health/verify tests | `HealthEndpointTest`, `CertificateVerificationTest` | **Done** |
| Politburo documentation | `docs/politburo/*` | **Done** |
| National ID live verification | Gov portal integration | **Planned** (MOU dependent) |
| Penetration test | External assessment | **Planned** |
| SOC 2 Type I / II audit | Independent CPA attestation | **Planned** — see [SOC2-READINESS-CHECKLIST.md](../SOC2-READINESS-CHECKLIST.md) |
| Admin MFA | TOTP or WebAuthn for backend logins | **Planned** |
| Push notifications | Expo push + PWA Web Push (VAPID); ownership-safe register | **Done** (Phase 2) — see [PWA.md](../PWA.md), [API-SECURITY.md](../API-SECURITY.md) |
| PWA member client | `/app/` React PWA sharing `/api/v1` | **Done** — see [PWA.md](../PWA.md) |
| IDOR audit (member APIs) | Push ownership, dialogue report, project like oracle | **Done** — [API-SECURITY.md](../API-SECURITY.md) |
| Branch/cell admin scoping | Extend `AdminScopeService` | **Planned** (Phase 3) |
| SMS/USSD channel | Separate project | **Planned** |
| Multi-language UI | i18n framework | **Planned** |

## Verification commands

```bash
# Backend (Docker)
docker compose exec app php artisan test

# Mobile
cd mobile && npm run lint
```

## Sign-off (fill at pilot gate)

| Role | Name | Date | Signature |
|------|------|------|-----------|
| National ICT lead | | | |
| Security officer | | | |
| Presidium representative | | | |
