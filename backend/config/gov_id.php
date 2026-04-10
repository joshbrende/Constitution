<?php

return [
    // When enabled, membership-granting Academy actions can require national ID verification.
    'enforce_verification_for_membership' => (bool) env('GOV_ID_ENFORCE_MEMBERSHIP_VERIFICATION', false),
];

