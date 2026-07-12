# Membership strategy — ZANU PF platform

This document defines how **party membership**, **organs**, **geography**, and **academy** fit together in the Constitution platform. It is the authoritative model for dashboard reporting, access control, and future cadre pathways.

---

## 1. Four dimensions (not one “level” number)

| Dimension | Party meaning | System field(s) |
|-----------|---------------|-----------------|
| **Membership standing** | Legal admission status | `users.membership_standing` |
| **Membership number** | Party identity (full members) | `users.membership_number` |
| **Wing / league** | Youth, Women's, Veterans, main | `users.wing` (primary) + `memberships` rows (parallel) |
| **Geography** | Province → district → branch → cell | `province_id`, `district_id`, `branch_id`, `cell_id` |
| **Cadre / academy** | Courses and certificates completed | `enrolments`, `certificates`, `certificate_applications` |

Leagues are **parallel wings**, not ranks above ordinary membership. Presidium, Politburo, and PEC are **offices**, assigned by admins — never auto-promoted by exams alone.

---

## 2. Membership standing lifecycle

| Standing | When set | App meaning |
|----------|----------|-------------|
| **applicant** | Registration | `student` role; academy + constitution access |
| **provisional** | Membership exam passed **or** invite/admin admission (application created) | Awaiting provincial payment |
| **member** | Certificate issued (Presidium approval workflow) | **Full member** — appears in Admin → Members; opaque **membership number** assigned |
| **suspended** | Admin action | Profile read-only API; academy/dialogue blocked; membership number retained |

**Membership number** (`users.membership_number`): opaque ID (e.g. `ZPF-M7K2Q9`) assigned once when standing becomes full member (exam path, invite path, admin standing edit, or backfill). Separate from academy `certificate_number`. Source: `academy` \| `invite` \| `admin_created`.

**Invite-only admission** (system_admin / user_manager): email invite or admin create skips the exam; still requires certificate fee → Presidium → issue before full member + number.

**Member role (`member`)** is attached when **payment is confirmed** (default `grant_member_role_on=payment_confirmed`). This unlocks member library, member dialogue posting, and league course access before the physical certificate is issued.

**Full member standing** is set when a **certificate is issued** — this is the party register definition used in Admin → Members and analytics “Full members (certificated)”.

---

## 3. Privileges by standing / role

| Capability | Applicant (`student`) | After payment (`member` role) | Full member | Suspended |
|------------|----------------------|------------------------------|-------------|-----------|
| Academy membership course | Yes | — | — | No |
| League courses (by wing) | No | Yes | Yes | No |
| Library: public | Yes | Yes | Yes | No |
| Library: member | No | Yes | Yes | No |
| Dialogue: read open channels | Yes | Yes | Yes | No |
| Dialogue: post | No | Yes | Yes | No |
| Admin dashboard | Only if staff role assigned | | | |

Staff roles (`academy_manager`, `provincial_admin`, etc.) are a **separate plane** from party membership.

---

## 4. Wing assignment

Constitutionally, league membership is through the **branch**. In the app:

- **Parallel memberships (Phase 2.1):** table `memberships` holds active/ended rows per wing. Full members always have `main`. Admins can add Youth / Women / Veterans (Step 2.2 UI; Step 2.1 syncs via Admin → Users → wing field).
- One shared **membership number** on `users` covers all wings.
- Provincial / user managers set primary `wing` on **Admin → Users → Edit** after branch verification (legacy field kept in sync).
- **Branch admission** (`branch_admitted_at`) is confirmed on the same screen after the provincial register is checked offline. League courses (`youth`, `women`, `veterans` audiences) require this when `ACADEMY_REQUIRE_BRANCH_ADMISSION=true` (default).
- Values: `main`, `youth`, `women`, `veterans` (see `config/academy.php` → `user_wings`).
- **League course `audience`** = pathway / grant target (Step 2.4): any member with privileges may enrol (after membership course + branch admission); issuing the league certificate activates that wing in `memberships`. Primary `users.wing` still follows sync rules and does not force-overwrite an active primary.

### Cadre designation

- **Cadre designees** (`cadre_designated_at`) are assigned by administrators — never by academy exams.
- Unlocks **leadership** library documents (alongside Presidium / System Admin roles).

---

## 5. Reporting definitions (aligned)

| Metric | Definition |
|--------|------------|
| **Full members** | `membership_standing = member` (certificate issued) |
| **Provisional** | Exam passed, payment or certificate pending |
| **Assessment passes** | Users with ≥1 passing graded attempt (academy KPI) |
| **Admin → Members** | Full members only |

---

## 6. Implementation map

| Component | Path |
|-----------|------|
| Standing enum | `app/Enums/MembershipStanding.php` |
| Standing service | `app/Services/MembershipStandingService.php` |
| Transitions on exam / cert | `CertificateApplicationService`, `MembershipService` |
| Members admin | `MembersController` |
| User profile admin | `UsersController`, `admin/users/edit.blade.php` |
| League courses seeder | `database/seeders/LeagueCoursesSeeder.php` |
| Branch admission gate | `CourseAccessService`, `config/academy.php` |
| Cadre library access | `LibraryDocumentPolicy` |
| API summary fields | `AcademyCourseController::summary()` |
| Member directory | `MemberDirectoryController`, ability `members:read` |
| Student vs member abilities | `config/permissions.php` |

---

## 7. Phased roadmap

- **Phase A–B (done):** Standing field, unified Members register, student vs member API abilities, admin wing/province/standing.
- **Phase C (done):** Youth / Women's / Veterans academy pathways seeded (`YOUTH-101`, `WOMEN-101`, `VETERANS-101`) with per-course certificates.
- **Phase D (done):** Branch admission confirmation on Admin → Users → Edit; league enrolment gate via `CourseAccessService`.
- **Phase E (done):** Cadre designation (admin-assigned) unlocking leadership library documents.
- **Phase 2 Step 2.1 (done):** `memberships` table; auto `main` on full member; legacy wing sync.
- **Phase 2 Step 2.2 (done):** Admin → Users → Edit league checkboxes (Youth/Women/Veterans) + primary wing.
- **Phase 2 Step 2.3 (done):** Profile/API list memberships + `active_wings`; course access uses all active memberships.
- **Phase 2 Step 2.4 (done):** League pathways open to paid members (branch admission still required); certificate issue activates matching wing.
- **Phase 3 (done):** National full-member directory (API + PWA + mobile); minimal fields; full standing required.

---

*Related: [`membership-course-plan.md`](./membership-course-plan.md), [`ACADEMY-CERTIFICATE-WORKFLOW.md`](./ACADEMY-CERTIFICATE-WORKFLOW.md), [`RBAC-MATRIX.md`](./RBAC-MATRIX.md), [`superpowers/specs/2026-07-11-membership-number-invite-design.md`](./superpowers/specs/2026-07-11-membership-number-invite-design.md)*
