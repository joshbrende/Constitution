<?php

/**
 * One-off maintenance: extract <<<TEXT heredoc bodies from ZanuPfChapter*Sections.php
 * into database/data/zanupf-constitution/bodies/{slug}.txt and rewrite seeders to use
 * ZanuPfConstitutionBodyRepository::body($slug). Safe to re-run if seeders still use heredocs.
 *
 * Usage (from backend/): php database/scripts/extract_zanupf_constitution_bodies.php
 */

declare(strict_types=1);

$base = dirname(__DIR__);
$seedersDir = $base.DIRECTORY_SEPARATOR.'seeders'.DIRECTORY_SEPARATOR.'Constitution';
$bodiesDir = $base.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'zanupf-constitution'.DIRECTORY_SEPARATOR.'bodies';

if (! is_dir($bodiesDir) && ! mkdir($bodiesDir, 0755, true) && ! is_dir($bodiesDir)) {
    fwrite(STDERR, "Cannot create directory: {$bodiesDir}\n");
    exit(1);
}

$files = [
    'ZanuPfChapter1Sections.php',
    'ZanuPfChapter2Sections.php',
    'ZanuPfChapter3Sections.php',
    'ZanuPfChapter4Sections.php',
];

$pairPattern = <<<'REGEX'
/
Section::create\(\[
(?P<section_block>[\s\S]*?)
\]\);\s*
SectionVersion::create\(\[
(?P<version_block>[\s\S]*?)
'body'\s*=>\s*<<<'?(?P<delim>\w+)'?\R
(?P<body>[\s\S]*?)
\R\s*(?P=delim)\s*,
/x
REGEX;

foreach ($files as $name) {
    $path = $seedersDir.DIRECTORY_SEPARATOR.$name;
    if (! is_readable($path)) {
        fwrite(STDERR, "Skip (not readable): {$path}\n");
        continue;
    }
    $original = file_get_contents($path);
    if ($original === false) {
        fwrite(STDERR, "Could not read: {$path}\n");
        exit(1);
    }

    if (! preg_match_all($pairPattern, $original, $matches, PREG_SET_ORDER)) {
        fwrite(STDERR, "No Section::create / SectionVersion pairs matched in {$name}\n");
        exit(1);
    }

    $written = 0;
    $transformed = $original;

    foreach ($matches as $m) {
        if (! preg_match("/'slug'\\s*=>\\s*'([^']+)'/", $m['section_block'], $sm)) {
            fwrite(STDERR, "Slug not found in section block ({$name})\n");
            exit(1);
        }
        $slug = $sm[1];
        $body = $m['body'];
        $txtPath = $bodiesDir.DIRECTORY_SEPARATOR.$slug.'.txt';
        if (file_put_contents($txtPath, $body) === false) {
            fwrite(STDERR, "Failed writing {$txtPath}\n");
            exit(1);
        }
        $written++;

        $fullMatch = $m[0];
        $replacement = $m[0];
        $replacement = preg_replace(
            "/'body'\\s*=>\\s*<<<'?".$m['delim']."'?\\R[\\s\\S]*?\\R\\s*".$m['delim']."\\s*,/",
            "'body' => ZanuPfConstitutionBodyRepository::body('".addslashes($slug)."'),",
            $replacement,
            1
        );
        if ($replacement === null || $replacement === $fullMatch) {
            fwrite(STDERR, "Replace failed for slug {$slug} in {$name}\n");
            exit(1);
        }
        $transformed = str_replace($fullMatch, $replacement, $transformed);
    }

    if ($transformed !== $original) {
        if (file_put_contents($path, $transformed) === false) {
            fwrite(STDERR, "Failed writing {$path}\n");
            exit(1);
        }
        echo "{$name}: wrote {$written} body file(s), updated PHP\n";
    } else {
        echo "{$name}: wrote {$written} body file(s), PHP unchanged (already migrated?)\n";
    }
}

echo "Done. Bodies directory: {$bodiesDir}\n";
