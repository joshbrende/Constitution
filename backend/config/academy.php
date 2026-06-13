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

    'receipt_number_prefix' => 'ZP-REC',

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

];
