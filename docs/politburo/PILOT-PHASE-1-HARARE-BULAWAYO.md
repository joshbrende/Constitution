# Phase 1 pilot — Harare & Bulawayo

**Status:** Approved to start  
**Duration:** 90 days from go-live  
**Provinces:** Harare (metropolitan) + Bulawayo (metropolitan)

Bulawayo is the second pilot province for geographic and organisational balance: two urban centres with strong party structures and ICT liaison capacity.

---

## Go-live checklist (national ICT)

- [ ] Platform install complete (`installed_at` set) — run `PilotGoLiveSeeder` below
- [x] `PILOT_ADMIN_PASSWORD` set in server `.env` (strong, unique — not committed)
- [x] Run: `php artisan db:seed --class=ProvincialPilotSeeder`
- [ ] Run: `php artisan db:seed --class=PilotGoLiveSeeder` (platform settings + Presidium + `installed_at`)
- [ ] Distribute provincial admin credentials **securely** to provincial chairs (not email in plain text)
- [ ] Distribute Presidium approver credentials to national certificate liaison
- [ ] Confirm Presidium approver can access certificate approval queue
- [x] Home banner visible in app (auto-created by seeder)
- [ ] Mobile app pointed at production API URL (or LAN IP for field testing)
- [ ] Member communication issued in both provinces — see [MEMBER-ANNOUNCEMENT-PHASE-1.md](./MEMBER-ANNOUNCEMENT-PHASE-1.md)

---

## Pilot administrator accounts

| Province | Login email | Role | Scope |
|----------|-------------|------|--------|
| **Harare** | `harare.pilot@zanupf.org.zw` | Provincial Admin | Users, members, certificate applications in Harare |
| **Bulawayo** | `bulawayo.pilot@zanupf.org.zw` | Provincial Admin | Users, members, certificate applications in Bulawayo |

Password: value of `PILOT_ADMIN_PASSWORD` in `.env` at seed time. **Change after first login** (recommended: password reset flow).

### National Presidium approver (certificate queue)

| Role | Login email | Scope |
|------|-------------|--------|
| Presidium | `presidium.pilot@zanupf.org.zw` | Certificate application approval (national) |

Same password as provincial pilot admins (`PILOT_ADMIN_PASSWORD`). Override email with `PILOT_PRESIDIUM_EMAIL` in `.env` if needed.

### Provision commands

```bash
# Step 1 — provincial admins + pilot banner (already run if go-live started)
php artisan db:seed --class=ProvincialPilotSeeder --force

# Step 2 — platform settings, Presidium approver, mark install complete
php artisan db:seed --class=PilotGoLiveSeeder --force
```

Optional: set public URL before step 2 (defaults to `http://localhost:8081`):

```env
PILOT_PUBLIC_SITE_URL=http://localhost:8081
```

Site settings recorded:

- `pilot_phase` = `1`
- `pilot_started_at` = ISO timestamp
- `pilot_provinces` = JSON array (province codes + admin emails)

---

## Week 1 — Provincial chair actions

1. **Identify ICT focal person** in each province (name + mobile).
2. **Train provincial admin** on:
   - User/member lists (provincial scope only)
   - Certificate application queue: payment confirmation
   - No Presidium approval (national role only)
3. **Announce to structures:** register → profile (province + national ID) → Academy.
4. **Office readiness:** payment collection point for membership course receipts.

---

## Week 2–8 — Operations

| Cadence | Activity |
|---------|----------|
| Daily | National ICT monitors registrations, failed assessments, queue depth |
| Weekly | 30-min call: Harare + Bulawayo chairs + national ICT |
| Monthly | Presidium certificate approval summary |

### Metrics to track (per province)

- New registrations
- Profiles completed (province + national ID)
- Academy enrolments and pass rate
- Certificate applications by status
- Audit: no cross-province admin access

---

## Week 9–12 — Review

- Compare metrics to [PROVINCIAL-ROLLOUT-PLAN.md](./PROVINCIAL-ROLLOUT-PLAN.md) success criteria
- Chair feedback (training gaps, connectivity, payment friction)
- Politburo recommendation: expand to Phase 2 provinces or extend pilot

---

## Contacts (fill before go-live)

| Role | Harare | Bulawayo |
|------|--------|----------|
| Provincial chair | | |
| ICT focal person | | |
| Provincial admin email | harare.pilot@zanupf.org.zw | bulawayo.pilot@zanupf.org.zw |

| National | Contact |
|----------|---------|
| National ICT lead | |
| Presidium certificate liaison | |

---

## Member messaging (template)

> The ZANU PF Constitution & Academy platform is now live for **Harare** and **Bulawayo**.
>
> 1. Download the mobile app or visit [public site URL]
> 2. Register and complete your profile (province and national ID)
> 3. Enrol in the Academy and study the Constitution
> 4. Complete assessments and follow certificate instructions at your provincial office

---

## Related documents

- [EXECUTIVE-SUMMARY.md](./EXECUTIVE-SUMMARY.md)
- [PROVINCIAL-ROLLOUT-PLAN.md](./PROVINCIAL-ROLLOUT-PLAN.md)
- [ACADEMY-CERTIFICATE-WORKFLOW.md](../ACADEMY-CERTIFICATE-WORKFLOW.md)
