<?php

namespace Database\Seeders\Constitution;

final class ZanuPfConstitutionBodyRepository
{
    private const RELATIVE_BODIES_DIR = 'data/zanupf-constitution/bodies';

    public static function body(string $slug): string
    {
        $path = database_path(self::RELATIVE_BODIES_DIR.'/'.$slug.'.txt');
        if (! is_readable($path)) {
            throw new \RuntimeException('ZANU PF constitution body file missing or unreadable: '.$path);
        }
        $content = file_get_contents($path);
        if ($content === false) {
            throw new \RuntimeException('Could not read constitution body: '.$path);
        }

        return $content;
    }
}
