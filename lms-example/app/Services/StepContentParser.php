<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Parses [STEP] delimited markdown into HTML with .learn-step divs for the panning/stepper UI.
 * Also extracts .learn-step divs from HTML for the learn view.
 */
final class StepContentParser
{
    /**
     * If $md contains [STEP], split into steps, run Markdown on each, wrap in .learn-step divs.
     * Otherwise return Str::markdown($md).
     */
    public static function toHtml(string $md): string
    {
        $md = str_replace('**Time Slot:**', 'Time Slot:', $md);

        if (!str_contains($md, '[STEP]')) {
            return Str::markdown($md);
        }

        // [STEP] Title on one line, then content until next [STEP] or end
        $pat = '/\n?\[STEP\]\s*(.+?)\n([\s\S]*?)(?=\[STEP\]|$)/';
        if (!preg_match_all($pat, $md, $m, PREG_SET_ORDER) || empty($m)) {
            return Str::markdown($md);
        }

        $out = '';
        foreach ($m as $match) {
            $title = trim($match[1]);
            $body = trim($match[2]);
            $html = Str::markdown($body);
            $out .= '<div class="learn-step" data-step-title="' . e($title) . '">' . $html . '</div>';
        }

        return $out;
    }

    /**
     * Extract .learn-step divs from HTML. Returns [ ['title'=>, 'content'=>], ... ].
     * If none found, returns [] so the view falls back to full content.
     */
    public static function fromHtml(string $html): array
    {
        $html = trim($html);
        if ($html === '') {
            return [];
        }

        $dom = new \DOMDocument();
        $old = libxml_use_internal_errors(true);
        @$dom->loadHTML('<?xml encoding="utf-8"?><div id="learn-step-root">' . $html . '</div>');
        libxml_use_internal_errors($old);

        $root = $dom->getElementById('learn-step-root');
        if (!$root) {
            return [];
        }

        $xpath = new \DOMXPath($dom);
        $divs = $xpath->query('.//div[contains(concat(" ", normalize-space(@class), " "), " learn-step ")]', $root);
        if (!$divs || $divs->length === 0) {
            return [];
        }

        $steps = [];
        foreach ($divs as $d) {
            $title = $d->getAttribute('data-step-title') ?: ('Step ' . (count($steps) + 1));
            $inner = '';
            foreach ($d->childNodes as $c) {
                $inner .= $dom->saveHTML($c);
            }
            $steps[] = ['title' => $title, 'content' => $inner];
        }

        return $steps;
    }
}
