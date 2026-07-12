# Step 2.4 League Course Auto-Grant — Implementation Plan

> **For agentic workers:** Execute task-by-task. Steps use checkbox syntax for tracking.

**Goal:** Open Youth/Women/Veterans course enrolment to paid members with branch admission; activate matching wing membership when the league certificate is issued.

**Architecture:** Reinterpret league `course.audience` as pathway/grant target. `CourseAccessService` gates league audiences on `hasMemberPrivileges` (not wing rows). `CertificateApplicationService::presidiumApprove` calls `WingMembershipService::ensureActive` for league audiences after `markFullMember`.

**Tech Stack:** Laravel services, existing Feature tests, Docker `constitution-app`.

---

### Task 1: Enrolment — open league audience

**Files:**
- Modify: `backend/app/Services/CourseAccessService.php`
- Modify: `backend/tests/Feature/CourseAccessTest.php`

- [ ] Replace `test_youth_course_blocked_for_wrong_wing…` with open-enrolment + member-privilege denial tests; attach `member` role where enrolment should succeed
- [ ] Change league branch of `userMatchesAudience` to `hasMemberPrivileges`
- [ ] Run `CourseAccessTest`

### Task 2: Grant wing on certificate issue

**Files:**
- Modify: `backend/app/Services/WingMembershipService.php` (optional metadata on activate audit)
- Modify: `backend/app/Services/CertificateApplicationService.php`
- Create/Modify: `backend/tests/Feature/LeagueCourseAutoGrantTest.php` (or extend WingMembershipTest)

- [ ] Failing test: presidium approve youth app → active youth membership
- [ ] Implement grant + `syncPrimaryWingColumn` + audit source
- [ ] Run grant + course access tests

### Task 3: Docs

- [ ] Update `docs/MEMBERSHIP-STRATEGY.md` Step 2.4
- [ ] Mark spec status implementing/done
