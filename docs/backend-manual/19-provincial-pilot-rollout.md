# 19. Provincial pilot rollout (Phase 1)

## 19.1 Purpose

Document **operational rollout** for provincial administrators, Presidium certificate approvers, and national ICT during phased go-live. In-app summary: Admin → Help & resources → **Documentation** (section “Phase 1 pilot”).

Politburo-facing runbooks: [`../politburo/PILOT-PHASE-1-HARARE-BULAWAYO.md`](../politburo/PILOT-PHASE-1-HARARE-BULAWAYO.md), [`../politburo/PROVINCIAL-ROLLOUT-PLAN.md`](../politburo/PROVINCIAL-ROLLOUT-PLAN.md).

## 19.2 Phase 1 provinces

| Province | Pilot provincial admin email |
|----------|------------------------------|
| Harare | `harare.pilot@zanupf.org.zw` |
| Bulawayo | `bulawayo.pilot@zanupf.org.zw` |

National Presidium certificate approver (pilot): `presidium.pilot@zanupf.org.zw` (override with `PILOT_PRESIDIUM_EMAIL`).

## 19.3 Provisioning (national ICT)

| Step | Command / action |
|------|------------------|
| Set password | `PILOT_ADMIN_PASSWORD` in `.env` (not committed) |
| Provincial admins + banner | `php artisan db:seed --class=ProvincialPilotSeeder --force` |
| Platform + Presidium + install flag | `php artisan db:seed --class=PilotGoLiveSeeder --force` |

**Site settings written:**

- `pilot_phase`, `pilot_started_at`, `pilot_provinces` (JSON)
- `installed_at` (setup wizard complete)
- Org / legal URLs if missing (`PilotGoLiveSeeder`)

**Config:** `config/pilot.php` — `admin_password`, `public_site_url`, `presidium_email`.

## 19.4 Provincial admin duties

- **Scope:** `AdminScopeService` / `province_id` on user account — Users, Members, Cert. applications filtered to assigned province.
- **Certificate queue:** Confirm offline payment only (`admin.certificate-applications.confirm-payment`).
- **Not permitted:** Presidium approve, platform settings, role CRUD, cross-province data.

Dashboard workflow steps: `config/role_workflows.php` → `provincial_admin`.

## 19.5 Presidium duties (certificates)

- Approve applications after provincial payment confirmation (`admin.certificate-applications.presidium-approve`).
- Separate from constitution amendment approval (Manage Constitution → Amendments).

See [15-certificates-admin.md](./15-certificates-admin.md) and [`../ACADEMY-CERTIFICATE-WORKFLOW.md`](../ACADEMY-CERTIFICATE-WORKFLOW.md).

## 19.6 Member journey (pilot)

1. Register (province optional at signup; required on profile for certification).
2. Complete profile — province + national ID.
3. Academy enrolment → assessment pass.
4. Pay at provincial office → provincial admin confirms.
5. Presidium approves → print → collection.

Member comms template: [`../politburo/MEMBER-ANNOUNCEMENT-PHASE-1.md`](../politburo/MEMBER-ANNOUNCEMENT-PHASE-1.md).

## 19.7 In-app documentation map

| Console page | Route | Audience |
|--------------|-------|----------|
| Documentation | `admin.guide.documentation` | All admins — modules, pilot table, workflows |
| Help | `admin.guide.help` | Quick tasks; provincial / Presidium cards |
| Settings | `admin.guide.settings` | Profile, theme, roles |
| FAQ | `admin.guide.faq` | Shared Q&A (editable by authorised roles) |

Doc version badge: `config/admin_guide.php` → `doc_version` (currently **1.1.0** for pilot update).

## 19.8 Success metrics (indicative)

From provincial rollout plan: profile completion &gt; 70%, certificate applications with confirmed payment, zero provincial scope violations in audit, zero critical security incidents.

---

*Last reviewed: 2026-05-23 — Phase 1 Harare & Bulawayo pilot.*
