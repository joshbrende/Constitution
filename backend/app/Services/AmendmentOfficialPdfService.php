<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AmendmentOfficialPdfService
{
    public static function disk(): string
    {
        return (string) config('constitution.amendment3_official_pdf_disk', 'public');
    }

    public static function path(): string
    {
        return (string) config('constitution.amendment3_official_pdf_path', 'constitution-official/amendment3.pdf');
    }

    public static function exists(): bool
    {
        return Storage::disk(self::disk())->exists(self::path());
    }

    /**
     * URL using APP_URL (fine for server-side emails); avoid for mobile API responses.
     */
    public static function absoluteUrl(): ?string
    {
        if (! self::exists()) {
            return null;
        }

        $u = Storage::disk(self::disk())->url(self::path());
        if (str_starts_with($u, 'http://') || str_starts_with($u, 'https://')) {
            return $u;
        }

        return url($u);
    }

    /**
     * Public URL path relative to host (leading slash), for pairing with request host (phones use LAN IP).
     */
    public static function publicPath(): string
    {
        return '/storage/'.ltrim(self::path(), '/');
    }

    public static function urlForRequest(Request $request): ?string
    {
        if (! self::exists()) {
            return null;
        }

        return $request->getSchemeAndHttpHost().self::publicPath();
    }
}
