<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class AppConfigController extends Controller
{
    public function show(): JsonResponse
    {
        $payload = Cache::remember('app_config_v1', 60, function () {
            $orgName = (string) SiteSetting::get('org_name', 'ZANUPF');

            return [
                'org_name' => $orgName,
                'support_email' => (string) SiteSetting::get('support_email', 'support@ttm-group.co.za'),
                'public_site_url' => (string) SiteSetting::get('public_site_url', ''),
                'legal' => [
                    'privacy_url' => (string) SiteSetting::get('legal_privacy_url', url('/privacy-policy')),
                    'terms_url' => (string) SiteSetting::get('legal_terms_url', url('/terms-of-use')),
                    'cookies_url' => (string) SiteSetting::get('legal_cookies_url', url('/cookies')),
                ],
                'features' => [
                    'enable_dialogue' => (bool) SiteSetting::get('enable_dialogue', true),
                    'require_national_id' => (bool) SiteSetting::get('require_national_id', true),
                ],
                'meta' => [
                    'updated_at' => now()->toIso8601String(),
                    'source' => 'site_settings',
                ],
            ];
        });

        return response()->json(['data' => $payload]);
    }
}

