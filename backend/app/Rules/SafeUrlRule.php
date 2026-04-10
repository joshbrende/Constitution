<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validates that a URL uses https or http scheme only (no javascript:, data:, etc.).
 */
class SafeUrlRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value) || ! is_string($value)) {
            return;
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            return;
        }

        $parsed = parse_url($trimmed);
        $scheme = isset($parsed['scheme']) ? strtolower($parsed['scheme']) : null;

        // Relative URLs (no scheme) are allowed
        if ($scheme === null) {
            return;
        }

        if (! in_array($scheme, ['http', 'https'], true)) {
            $fail('The :attribute must be a valid http or https URL.');
        }
    }
}
