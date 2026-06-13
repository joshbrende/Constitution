# Academy certificate workflow — operator runbook

Government-led membership certificate process: exam pass → payment receipt → offline payment → admin confirmation → Presidium approval → print → collection.

**Audience:** Academy managers, provincial administrators, Presidium reviewers, finance office staff, and system administrators.

**Related:** [`RBAC-MATRIX.md`](./RBAC-MATRIX.md) · [`AUDIT-LOGGING.md`](./AUDIT-LOGGING.md) · [`backend-manual/15-certificates-admin.md`](./backend-manual/15-certificates-admin.md)

---

## Overview

Students **never download certificates from the mobile app**. After passing a membership assessment they receive a **payment receipt** (PDF + reference codes). They pay the fee at a ZANU PF office. An administrator confirms payment in the dashboard; Presidium approves printing; staff print the certificate and mark it ready for physical collection.

| Step | Who | System status | Student sees (app) |
|------|-----|---------------|-------------------|
| 1. Exam passed | System | `payment_pending` | Portal message + receipt |
| 2. Pay at office | Student (offline) | `payment_pending` | Awaiting payment confirmation |
| 3. Payment confirmed | Academy / provincial admin | `presidium_pending` | Payment received; member role attached |
| 4. Presidium approval | Presidium | `print_ready` | Certificate being prepared |
| 5. Print | Academy manager | `printed` | Being prepared |
| 6. Ready for collection | Academy / provincial admin | `ready_for_collection` | Collect at office |
| 7. Collected | Admin + student | `collected` | Complete |

Default membership fee: **USD 25.00** (configurable per course). Member role is granted **after payment confirmed** (not on exam pass alone).

---

## Configuration

File: `backend/config/academy.php`

| Key | Purpose |
|-----|---------|
| `grant_member_role_on` | `payment_confirmed` (default) or `exam_pass` (legacy) |
| `default_membership_fee_amount` | Seeder default (25.00 USD) |
| `payment_offices` | Static list on receipts (name, address, province filter) |
| `default_payment_instructions` | Receipt / portal text when course has no override |
| `student_certificate_download_enabled` | `false` = government workflow (students use applications API) |

Course-level fields (admin **Academy → course form**):

- `certificate_fee_amount` — required when `grants_membership` is true
- `certificate_fee_currency` — ISO code (default USD)
- `payment_office_instructions` — optional override for receipt text

After changing permissions: `docker compose exec app php artisan permissions:sync` (from repo root).

---

## Payment offices (v1)

Offices are defined in `config/academy.php` → `payment_offices`. Receipts show offices matching the student's **province** when `province_codes` is set; a fallback entry with `province_codes: null` appears for all provinces.

**Operator action:** Update `payment_offices` in config and redeploy. Document local provincial addresses with your Provincial Administrator until per-province DB overrides exist.

---

## Admin dashboard — certificate applications

**Navigation:** Admin → **Cert. applications** (under Certificates section).

**URL pattern:** `/admin/certificate-applications`

### Queue tabs

| Tab | Statuses shown |
|-----|----------------|
| Payment pending | `payment_pending` |
| Awaiting Presidium | `presidium_pending` |
| Ready to print | `print_ready` |
| Awaiting collection | `printed`, `ready_for_collection` |
| Completed | `collected` |

**Provincial admin:** List is scoped to applicants whose `users.province_id` matches the admin's province.

### Actions (application detail page)

| Action | Who | Notes |
|--------|-----|-------|
| Confirm payment | `academy_payment_confirm` | Enter teller / gov reference note; attaches member role |
| Presidium approve | `academy_certificate_presidium_approve` | Creates `Certificate` record; queues PDF job |
| Mark printed | `academy_certificate_print` | After physical print |
| Mark ready for collection | `academy_certificate_collection` | Set collection office name |
| Mark collected | `academy_certificate_collection` | Student collected certificate |
| Download certificate PDF | `academy_certificate_print` | Only when `print_ready`+ and Presidium approved |

---

## Finance office procedure (offline payment)

1. Student presents **payment receipt** (app PDF or printed copy) showing:
   - Receipt number (`ZP-REC-YYYY-…`)
   - Payment reference code (office lookup)
   - Exact fee amount and currency
2. Collect payment; issue official payment slip / teller reference.
3. Student keeps the slip; no system access required at the office.
4. Academy or provincial admin confirms payment in the dashboard using the receipt number and optional teller reference note.

---

## Presidium procedure

1. Open **Cert. applications** → **Awaiting Presidium** tab (national scope).
2. Verify payment was confirmed and applicant details.
3. **Presidium approve** — optional note; system creates certificate record and starts PDF generation.
4. Academy manager prints from admin PDF download when status is **Ready to print**.

Presidium users **cannot** confirm payments (separation of duties).

---

## Student mobile app

- **Assessment pass** → “View payment receipt” (not “View certificates”).
- **Academy status** — list of applications and progress.
- **Portal messages** — email + in-app notifications at each step.
- **Receipt PDF** — download/share only; not the membership certificate.

API: `GET /api/v1/academy/applications`, `…/receipt.pdf`, `GET /api/v1/academy/summary` (`portal_messages`).

Student certificate endpoints return empty / 403 when `student_certificate_download_enabled` is false.

---

## Notifications

Each transition sends **mail** + **database** notification to the student (portal messages in app summary).

| Event | Notification |
|-------|----------------|
| Exam pass | Exam passed – payment required |
| Payment confirmed | Payment received |
| Presidium approved | Certificate approved for printing |
| Ready for collection | Your certificate is ready for collection |
| Collected | Certificate collection confirmed |

Dev mail: `MAIL_MAILER=log` → check `storage/logs/laravel.log`.

---

## Audit events

| Action | When |
|--------|------|
| `academy.application.created` | Exam pass creates application |
| `academy.application.payment_confirmed` | Admin confirms payment |
| `academy.application.presidium_approved` | Presidium approves |
| `academy.application.printed` | Marked printed |
| `academy.application.ready_for_collection` | Ready for pickup |
| `academy.application.collected` | Student collected |
| `membership.granted` | Member role attached (source: `payment_confirmed`) |

Query examples: [`AUDIT-LOGGING.md`](./AUDIT-LOGGING.md).

---

## Troubleshooting

| Issue | Check |
|-------|--------|
| No application after pass | Course must `grants_membership` + fee > 0; check logs |
| Admin cannot confirm payment | Role has `admin.action.academy_payment_confirm`; run `permissions:sync` |
| Presidium cannot see application | Status must be `presidium_pending`; provincial filter N/A for Presidium |
| PDF download fails | TCPDF installed; certificate linked; status `print_ready`+ |
| Student sees old “certificates” flow | Mobile app updated; API `certificates_disabled` meta |
| Duplicate application | One application per user+course (idempotent on re-pass) |

---

## Rollout / legacy certificates

- Certificates issued **before** the workflow remain in the database; student download is disabled by default (`student_certificate_download_enabled=false`).
- Optional backfill for users who passed before rollout: artisan command `academy:backfill-applications` (if implemented for production one-off).
- Re-seed dev membership course fee: `MembershipCourseSeeder` uses `config/academy.php` default.

---

*Last updated: 2026-05-23 — Academy government certificate workflow (Phases 1–6).*
