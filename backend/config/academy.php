<?php

/**
 * Academy certificate workflow (government-led payment + Presidium approval).
 *
 * Operator documentation: docs/ACADEMY-CERTIFICATE-WORKFLOW.md (created in Phase 6).
 */
return [

    /** When to attach the member role: payment_confirmed | exam_pass */
    'grant_member_role_on' => env('ACADEMY_GRANT_MEMBER_ROLE_ON', 'payment_confirmed'),

    /** Default fee when seeding membership course (USD). */
    'default_membership_fee_amount' => (float) env('ACADEMY_DEFAULT_MEMBERSHIP_FEE', 25.00),

    'default_fee_currency' => env('ACADEMY_DEFAULT_FEE_CURRENCY', 'USD'),

    'receipt_number_prefix' => 'ZPF-REC',

    /** Relative to public/ — logo on payment receipt PDF. */
    'receipt_logo_path' => env('ACADEMY_RECEIPT_LOGO_PATH', 'download.png'),

    'payment_reference_length' => 10,

    /**
     * National payment offices (v1 static list). Receipts include offices matching
     * the student's province when possible, otherwise all offices.
     *
     * @var list<array{name: string, address: string, province_codes: list<string>|null, phone?: string, hours?: string}>
     */
    'payment_offices' => [
        [
            'name' => 'ZANU PF Headquarters – Finance Office',
            'address' => 'ZANU PF Headquarters, Harare',
            'province_codes' => ['harare'],
            'phone' => '+263 4 000 0000',
            'hours' => 'Mon–Fri 08:00–16:00',
        ],
        [
            'name' => 'Provincial Party Office (your province)',
            'address' => 'Contact your Provincial Administrator for the nearest collection and payment office.',
            'province_codes' => null,
            'hours' => 'Mon–Fri 08:00–16:00',
        ],
    ],

    /** Shown on receipts when no course-specific override is set. */
    'default_payment_instructions' => 'Present this receipt at the designated ZANU PF payment office. Pay the exact amount shown. Keep your official payment slip; an Academy administrator will confirm payment in the system before your certificate is processed.',

    /** When false, students cannot list/download certificates via API (government workflow). */
    'student_certificate_download_enabled' => filter_var(env('ACADEMY_STUDENT_CERTIFICATE_DOWNLOAD', false), FILTER_VALIDATE_BOOL),

    /**
     * Membership assessment question selection (per attempt).
     */
    'assessment_selection' => [
        'ensure_module_coverage' => true,
        'min_per_module' => 2,
        'difficulty_ratios' => [
            'easy' => 0.32,
            'medium' => 0.48,
            'hard' => 0.20,
        ],
    ],

    /** Minutes to cache pre-start question set tokens (at least exam duration + buffer). */
    'assessment_question_set_cache_minutes' => (int) env('ACADEMY_QUESTION_SET_CACHE_MINUTES', 10),

    'assessment_question_set_buffer_minutes' => (int) env('ACADEMY_QUESTION_SET_BUFFER_MINUTES', 5),

    /** Grace period after deadline before server rejects submit (clock skew / latency). */
    'assessment_time_grace_seconds' => (int) env('ACADEMY_ASSESSMENT_TIME_GRACE_SECONDS', 30),

    /** Maximum graded attempts per user per assessment (0 = unlimited). */
    'assessment_max_attempts' => (int) env('ACADEMY_ASSESSMENT_MAX_ATTEMPTS', 3),

    /** Hours to wait after a failed attempt before starting again (0 = no cooldown). */
    'assessment_attempt_cooldown_hours' => (int) env('ACADEMY_ASSESSMENT_COOLDOWN_HOURS', 24),

    /**
     * Course audience restrictions (stored on courses.audience).
     * Youth / women's / veterans audiences match users.wing.
     */
    'course_audiences' => [
        'all' => 'All learners',
        'member' => 'Ordinary members (membership completed)',
        'youth' => 'Youth League',
        'women' => "Women's League",
        'veterans' => 'Veterans League',
        'presidium' => 'Presidium',
    ],

    /** Valid values for users.wing when assigning league access. */
    'user_wings' => ['main', 'youth', 'women', 'veterans'],

    /** League courses require provincial branch admission confirmation before enrolment. */
    'require_branch_admission_for_league_courses' => filter_var(
        env('ACADEMY_REQUIRE_BRANCH_ADMISSION', true),
        FILTER_VALIDATE_BOOL
    ),

];
