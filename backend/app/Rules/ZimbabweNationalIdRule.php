<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validates Zimbabwe National ID format.
 * Format: 2 digits, optional dash, 4-7 digits, optional separator, 1 letter, optional separator, 2 digits.
 * Examples: 08-2047823Q29, 082047823Q29, 69-235489 C 67
 */
class ZimbabweNationalIdRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value) || ! is_string($value)) {
            return;
        }

        $normalized = preg_replace('/[\s\-]+/', '', $value);

        // Core format: 2 digits + 4-7 digits + 1 letter + 2 digits = 9-10 digits + 1 letter + 2 digits
        if (! preg_match('/^\d{2}\d{4,7}[A-Za-z]\d{2}$/', $normalized)) {
            $fail('The :attribute must be a valid Zimbabwe National ID (e.g. 08-2047823Q29).');
        }
    }
}
