# Phase 1 — Membership number & invite-only admission

**Date:** 2026-07-11  
**Status:** Approved design — gap fixes incorporated; implementing Phase 1  
**Related:** [`../../MEMBERSHIP-STRATEGY.md`](../../MEMBERSHIP-STRATEGY.md), [`../../ACADEMY-CERTIFICATE-WORKFLOW.md`](../../ACADEMY-CERTIFICATE-WORKFLOW.md)

## Goal

Give every **full member** a stable opaque **membership number**, and allow **system_admin** / **user_manager** to admit selected people **without the academy exam**, while still requiring the existing **certificate fee → Presidium → certificate** path before full membership and number issuance.

## Non-goals (later phases)

- Multi-membership / wing levels as separate membership rows (Phase 2)
- Public or member-facing searchable directory (Phase 3)
- Regenerating, transferring, or customizing membership numbers
- Changing the staff/backend invitation flow

## Decisions

| Topic | Choice |
|-------|--------|
| Number timing | Only when standing becomes **full member** |
| Number format | Opaque, e.g. `ZPF-M7K2Q9` (not sequential) |
| Storage | Columns on `users` (Approach 1) — seam for Phase 2 `memberships` table later |
| Invite paths | **Both** email invite link and admin create-account |
| Who may invite/create | **system_admin** and **user_manager** only |
| Academy bypass | **Exam only** — payment/Presidium/certificate still required |
| Separate from | Academy `certificate_number` (unchanged) |

## Architecture

```
Admin (system_admin | user_manager)
  ├─ Email invite  → member_invitations → accept page → User + CertificateApplication (no attempt)
  └─ Admin create  → User + CertificateApplication (no attempt)
                              ↓
                    payment → presidium → certificate issue
                              ↓
              MembershipStandingService::markFullMember
                              ↓
              MembershipNumberService::assignIfMissing (once)
```

Academy exam path unchanged until `markFullMember`; then it also receives a membership number.

## Data model

### `users` (new columns)

| Column | Type | Notes |
|--------|------|--------|
| `membership_number` | `string`, nullable, **unique** | Opaque party ID; null until full member |
| `membership_admitted_at` | `timestamp`, nullable | When number / full membership granted |
| `membership_source` | `string`, nullable | `academy` \| `invite` \| `admin_created` |

### `member_invitations` (new table)

Mirror staff invite pattern (`BackendUserInvitation`):

| Column | Notes |
|--------|--------|
| `email` | Unique among open (non-accepted) invites |
| `token_hash` | SHA-256 of plain token |
| `name`, `surname` | Optional prefill |
| `province_id`, `wing` | Optional placement |
| `national_id` | Optional at invite; required at accept if platform requires national ID |
| `invited_by_user_id` | Actor |
| `expires_at` | Default 7 days |
| `accepted_at` | Null until used |
| `revoked_at` | Optional revoke |

### `certificate_applications` (changes)

| Change | Notes |
|--------|--------|
| `assessment_attempt_id` | **Nullable** (invite / admin_created have no attempt) |
| `admission_source` | `exam` \| `invite` \| `admin_created` (default `exam` for existing rows) |

## Number issuance

**Service:** `MembershipNumberService::ensureForFullMember(User $user): void`

- Format: `ZPF-` + uppercase alphanumeric excluding ambiguous `0O1IL`, ~6 chars after prefix (e.g. `ZPF-M7K2Q9`).
- **Choke point:** call whenever a user is or becomes a full member — not only when standing *transitions*. Cover:
  - `MembershipStandingService::markFullMember`
  - Admin Users edit when standing set to `member`
  - `syncFromRecords` when resolved standing is `member`
  - Idempotent backfill command
- If number already set, no-op (never regenerate).
- Set `membership_admitted_at` only when first assigning the number.
- Preserve existing `membership_source` (`invite` / `admin_created`); if null at first assign, derive from latest certificate application `admission_source`, else `academy`.
- Uniqueness: generate → unique index; retry on collision.
- Never clear on suspend.

**Backfill:** `php artisan membership:backfill-numbers` for existing `membership_standing = member` with null number:

- Assign opaque number
- `membership_admitted_at` = earliest related certificate `issued_at`, else `users.updated_at`, else `now()`
- `membership_source` as above

## Invite & admin-create flows

### Permissions

- Gate / ability: `admin.action` or dedicated check: only `system_admin` and `user_manager`.
- UI: Members (or Users) subsection — not available to provincial_admin / content_editor via this gate even if they can open Users.

### Membership course resolution

Use the single published course with `grants_membership = true` (platform already enforces at most one such course). Require a positive `certificate_fee_amount`. If missing, invite/create fails with a clear admin error.

### Path A — Email invite

1. Admin submits email (+ optional name, surname, province, wing, national_id).
2. Reject if user email exists or a valid open invite exists.
3. Store hashed token; email link to web accept page (Blade, guest).
4. Accept: validate token, password + terms (+ national_id if required); create user:
   - roles: `student` only
   - standing: **provisional**
   - `membership_source = invite`
   - **no** `membership_number` yet
5. Create certificate application: `payment_pending`, `admission_source = invite`, `assessment_attempt_id = null`, fee from membership course.
6. Existing workflow continues; on certificate issue → `markFullMember` → number assigned.

### Path B — Admin create

1. Admin submits name, surname, email, national_id (when required), province, optional wing.
2. Create user immediately; send password-setup or temporary credentials email.
3. Same provisional + payment-pending application with `admission_source = admin_created` / `membership_source = admin_created`.

**Separate from staff provisioning:** Member invite/create must not reuse “Invite backend user” / `store-backend` UI. Distinct routes, copy, and notifications so admins cannot confuse dashboard staff with party members.

**Post-accept UX:** Invite/admin-created users must see the same academy payment / application status surfaces as exam passers. Those endpoints and UIs must tolerate `assessment_attempt_id = null` (label as invite admission; no score).

### Guards

- Email uniqueness
- Zimbabwe national ID rule when `require_national_id`
- Fee configured on membership course
- Invite: 7-day expiry, single use; re-invite allowed after expiry/revoke if no account
- Audit actions: `membership.invite_sent`, `membership.invite_accepted`, `membership.admin_created`, `membership.number_assigned`

## Surfaces

| Surface | Behaviour |
|---------|-----------|
| Admin → Members | Show membership number; search by number |
| Admin → Users edit | Show number, source, admitted_at (read-only) |
| API profile | Expose `membership_number`, `membership_admitted_at`, `membership_source` when set |
| PWA / mobile profile | Read-only display when present |
| Certificate PDF / public verify | Unchanged (still uses `certificate_number`) |

## Compatibility with current membership strategy

Aligns with [`MEMBERSHIP-STRATEGY.md`](../../MEMBERSHIP-STRATEGY.md):

- Standing lifecycle unchanged: applicant → provisional → member
- `member` role still attached on **payment confirmed** (not on invite accept)
- Full member standing still on **certificate issued**
- Membership number is an additional **identity** attribute of full members, not a new standing

Update `MEMBERSHIP-STRATEGY.md` in the same implementation PR to document `membership_number` and invite admission source.

## Testing

| Case | Expect |
|------|--------|
| Exam path reaches `markFullMember` | Number assigned once; source `academy` |
| Second `markFullMember` | Number unchanged |
| Invite accept | Provisional; application payment_pending; no attempt; no number yet |
| Payment → issue after invite | Full member + number; source `invite` |
| Admin create | Same as invite with source `admin_created` |
| provincial_admin tries invite | 403 |
| Backfill command | Idempotent; all full members have unique numbers |
| Profile API before full member | `membership_number` null/absent |
| Suspended full member | Number retained |

## Implementation sketch (for planning)

1. Migrations: user columns; `member_invitations`; nullable attempt + `admission_source` on applications  
2. `MembershipNumberService` + hook in `markFullMember`  
3. Backfill command  
4. `CertificateApplicationService::createFromInviteAdmission(User, Course, source)`  
5. Admin controllers/views + accept route/notification  
6. Profile API + PWA/mobile display  
7. Permission config + feature tests  
8. Docs: MEMBERSHIP-STRATEGY + backend-manual auth/members

## Roadmap reminder

| Phase | Scope |
|-------|--------|
| **1 (this spec)** | Membership number + invite/admin bypass exam |
| **2** | Multi-membership / levels (ZANU PF wings & structures) |
| **3** | Full member directory for member-to-member discovery |
