<?php

namespace App\Support;

/**
 * Sanitizes HTML for safe output, removing XSS vectors.
 * Uses mews/purifier when available; otherwise falls back to strip_tags + href cleaning.
 */
class HtmlSanitizer
{
    public static function sanitize(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        if (class_exists(\Mews\Purifier\Facades\Purifier::class)) {
            return \Mews\Purifier\Facades\Purifier::clean($html);
        }

        return self::fallbackSanitize($html);
    }

    /**
     * Fallback when Purifier is not available.
     */
    private static function fallbackSanitize(string $html): string
    {
        $allowed = '<p><br><strong><em><b><i><u><ul><ol><li><a><h1><h2><h3><h4><blockquote><span>';
        $cleaned = strip_tags($html, $allowed);
        $cleaned = preg_replace_callback(
            '/<a\s+([^>]*)>/i',
            function (array $m) {
                $attrs = preg_replace(
                    '/\bhref\s*=\s*(["\'])\s*(javascript|data|vbscript):[^\1]*\1/i',
                    'href="#"',
                    $m[1]
                );
                return '<a ' . $attrs . '>';
            },
            $cleaned
        );
        return $cleaned ?: '';
    }
}
