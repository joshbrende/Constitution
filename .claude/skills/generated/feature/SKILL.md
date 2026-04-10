---
name: feature
description: "Skill for the Feature area of constitution. 121 symbols across 56 files."
---

# Feature

121 symbols | 56 files | Cohesion: 64%

## When to Use

- Working with code in `backend/`
- Understanding how CourseDetailScreen, ConstitutionListScreen, getAmendment3OfficialPdfMeta work
- Modifying feature-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `backend/tests/Feature/LibraryApiAccessTest.php` | LibraryApiAccessTest, seedDocuments, test_guest_list_excludes_member_and_leadership_documents, test_authenticated_member_sees_public_and_member_not_leadership, test_guest_show_member_document_returns_403 (+2) |
| `backend/tests/Feature/CertificateAdminTest.php` | CertificateAdminTest, adminUser, certificateFixture, test_certificate_search_mode_filter_works, test_revoke_and_reinstate_write_audit_logs_and_metadata |
| `lms-example/tests/Feature/NotificationsTest.php` | test_mark_all_read_marks_all_unread_notifications_as_read, test_notifications_index_renders_for_authenticated_user, test_read_and_go_marks_notification_as_read_and_redirects_to_action_url, NotificationsTest |
| `lms-example/tests/Feature/NotesTest.php` | createStudentUser, test_guest_cannot_save_notes, test_student_can_create_and_clear_note_for_unit, NotesTest |
| `lms-example/tests/Feature/FacilitatorLearnersViewTest.php` | createFacilitatorUser, test_facilitator_can_view_learners_and_at_risk_flag, test_non_facilitator_cannot_view_learners, FacilitatorLearnersViewTest |
| `lms-example/tests/Feature/CourseEvaluationTest.php` | createStudentUser, test_student_must_complete_course_before_evaluating, test_student_can_submit_and_update_evaluation_after_completion, CourseEvaluationTest |
| `lms-example/tests/Feature/AdminAnalyticsTest.php` | createAdminUser, test_admin_sees_course_analytics, test_non_admin_cannot_view_analytics, AdminAnalyticsTest |
| `backend/tests/Feature/CertificateApiAuthorizationTest.php` | certificateForUser, test_user_cannot_generate_another_users_certificate, test_owner_can_generate_own_certificate, CertificateApiAuthorizationTest |
| `backend/tests/Feature/AuthApiTest.php` | test_login_rejects_invalid_credentials_with_422, test_login_returns_tokens_for_valid_credentials, AuthApiTest, test_register_assigns_student_role_and_returns_201 |
| `backend/app/Http/Controllers/Admin/AcademyBadgesAdminController.php` | create, store, update, validateBadge |

## Entry Points

Start here when exploring this area:

- **`CourseDetailScreen`** (Function) — `mobile/src/screens/CourseDetailScreen.js:20`
- **`ConstitutionListScreen`** (Function) — `mobile/src/screens/ConstitutionListScreen.js:36`
- **`getAmendment3OfficialPdfMeta`** (Function) — `mobile/src/api/officialConstitutionApi.js:6`
- **`getCourse`** (Function) — `mobile/src/api/academyApi.js:22`
- **`getEnrolment`** (Function) — `mobile/src/api/academyApi.js:32`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `SupportQuestionSubmitted` | Class | `backend/app/Mail/SupportQuestionSubmitted.php` | 9 |
| `TestCase` | Class | `lms-example/tests/TestCase.php` | 6 |
| `TestCase` | Class | `backend/tests/TestCase.php` | 6 |
| `AssignmentGradedNotificationTest` | Class | `lms-example/tests/Unit/AssignmentGradedNotificationTest.php` | 8 |
| `QuizSubmitTest` | Class | `lms-example/tests/Feature/QuizSubmitTest.php` | 14 |
| `NotificationsTest` | Class | `lms-example/tests/Feature/NotificationsTest.php` | 10 |
| `NotesTest` | Class | `lms-example/tests/Feature/NotesTest.php` | 13 |
| `FacilitatorLearnersViewTest` | Class | `lms-example/tests/Feature/FacilitatorLearnersViewTest.php` | 12 |
| `EnrollTest` | Class | `lms-example/tests/Feature/EnrollTest.php` | 11 |
| `CourseEvaluationTest` | Class | `lms-example/tests/Feature/CourseEvaluationTest.php` | 12 |
| `AuthTest` | Class | `lms-example/tests/Feature/AuthTest.php` | 9 |
| `AdminAnalyticsTest` | Class | `lms-example/tests/Feature/AdminAnalyticsTest.php` | 13 |
| `ExampleTest` | Class | `backend/tests/Unit/ExampleTest.php` | 6 |
| `RegistrationRolesTest` | Class | `backend/tests/Feature/RegistrationRolesTest.php` | 9 |
| `PriorityProjectLikePolicyTest` | Class | `backend/tests/Feature/PriorityProjectLikePolicyTest.php` | 10 |
| `LibraryApiAccessTest` | Class | `backend/tests/Feature/LibraryApiAccessTest.php` | 13 |
| `ExampleTest` | Class | `backend/tests/Feature/ExampleTest.php` | 7 |
| `DialoguePolicyTest` | Class | `backend/tests/Feature/DialoguePolicyTest.php` | 11 |
| `DialogueApiTest` | Class | `backend/tests/Feature/DialogueApiTest.php` | 10 |
| `CourseEnrolPolicyTest` | Class | `backend/tests/Feature/CourseEnrolPolicyTest.php` | 10 |

## Execution Flows

| Flow | Type | Steps |
|------|------|-------|
| `Register → Disk` | cross_community | 8 |
| `Register → Path` | cross_community | 8 |
| `Register → Disk` | cross_community | 7 |
| `Register → Path` | cross_community | 7 |
| `Register → Roles` | cross_community | 7 |
| `Login → Disk` | cross_community | 7 |
| `Login → Path` | cross_community | 7 |
| `Refresh → Disk` | cross_community | 7 |
| `Refresh → Path` | cross_community | 7 |
| `ForgotPassword → Disk` | cross_community | 7 |

## Connected Areas

| Area | Connections |
|------|-------------|
| Admin | 11 calls |
| Controllers | 5 calls |
| Screens | 3 calls |
| Models | 2 calls |
| Offline | 1 calls |

## How to Explore

1. `gitnexus_context({name: "CourseDetailScreen"})` — see callers and callees
2. `gitnexus_query({query: "feature"})` — find related execution flows
3. Read key files listed above for implementation details
