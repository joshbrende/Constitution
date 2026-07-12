# Membership Number & Invite Admission — Implementation Plan

> **For agentic workers:** Execute task-by-task. Steps use checkbox syntax for tracking. Gap fixes from senior review are mandatory (number choke-point on all full-member paths; preserve invite source; null-safe applications; separate from staff invite).

**Goal:** Opaque `membership_number` for full members; system_admin/user_manager can invite or create members who skip the exam but still complete certificate payment → Presidium → issue.

**Architecture:** Columns on `users`; `MembershipNumberService::ensureForFullMember` as single assigner; `member_invitations` + `CertificateApplicationService::createFromInviteAdmission` with nullable `assessment_attempt_id`.

**Tech Stack:** Laravel 12, Sanctum, Blade admin, existing certificate workflow, PWA/mobile profile display.

**Spec:** `docs/superpowers/specs/2026-07-11-membership-number-invite-design.md`

---

### Task 1: Schema + MembershipNumberService + standing hooks

**Files:**
- Create: `backend/database/migrations/2026_07_11_120000_add_membership_number_and_invites.php`
- Create: `backend/app/Enums/MembershipSource.php`
- Create: `backend/app/Services/MembershipNumberService.php`
- Create: `backend/app/Console/Commands/BackfillMembershipNumbers.php`
- Modify: `backend/app/Models/User.php`
- Modify: `backend/app/Services/MembershipStandingService.php`
- Modify: `backend/app/Http/Controllers/Admin/UsersController.php` (standing → member)
- Test: `backend/tests/Feature/MembershipNumberTest.php`

- [ ] Migration: `membership_number` (unique nullable), `membership_admitted_at`, `membership_source` on users; `admission_source` + nullable `assessment_attempt_id` on certificate_applications; `member_invitations` table
- [ ] `ensureForFullMember` assigns once; preserves source; derives from application if needed
- [ ] Hook `markFullMember`, `syncFromRecords`, admin standing update to `member`
- [ ] Backfill command idempotent
- [ ] Tests for assign-once, preserve invite source, admin standing path

### Task 2: Invite admission applications

**Files:**
- Modify: `backend/app/Models/CertificateApplication.php`
- Modify: `backend/app/Services/CertificateApplicationService.php`
- Modify: admin certificate application views (null attempt)
- Test: extend `CertificateApplicationWorkflowTest` or new `MemberInviteAdmissionTest`

- [ ] `createFromInviteAdmission(User, source)` → payment_pending, no attempt, markProvisional
- [ ] Null-safe admin/API labels

### Task 3: Member invite + admin create

**Files:**
- Create: `backend/app/Models/MemberInvitation.php`
- Create: `backend/app/Http/Controllers/Admin/MemberInvitationsController.php`
- Create: `backend/app/Http/Controllers/MemberInvitationAcceptController.php`
- Create: notifications + Blade views
- Modify: `backend/config/permissions.php` (`membership_invite` action → system_admin, user_manager)
- Modify: `backend/routes/web.php`, dashboard nav under Members
- Test: permission 403 for provincial_admin; happy paths

### Task 4: Surfaces + docs

**Files:**
- Modify: MembersController search, members index, users edit, Profile API (already returns user model — ensure fillable/visible)
- Modify: PWA + mobile Profile
- Modify: `docs/MEMBERSHIP-STRATEGY.md`
- Run feature tests in Docker

---

**Execution:** Inline in this session (user asked to proceed).
