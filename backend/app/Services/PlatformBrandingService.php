<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Schema;

class PlatformBrandingService
{
    public function orgName(string $fallback = 'ZANU PF Academy'): string
    {
        if (! Schema::hasTable('site_settings')) {
            return $fallback;
        }

        $name = trim((string) SiteSetting::get('org_name', ''));

        return $name !== '' ? $name : $fallback;
    }

    public function supportEmail(): ?string
    {
        if (! Schema::hasTable('site_settings')) {
            return null;
        }

        $email = trim((string) SiteSetting::get('support_email', ''));

        return $email !== '' ? $email : null;
    }

    public function mobileAppStoreUrl(): ?string
    {
        return $this->optionalUrl('mobile_app_store_url');
    }

    public function mobilePlayStoreUrl(): ?string
    {
        return $this->optionalUrl('mobile_play_store_url');
    }

    private function optionalUrl(string $key): ?string
    {
        if (! Schema::hasTable('site_settings')) {
            return null;
        }

        $url = trim((string) SiteSetting::get($key, ''));

        return $url !== '' ? $url : null;
    }
}
