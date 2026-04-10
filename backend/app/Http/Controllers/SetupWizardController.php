<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SetupWizardController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();
        $user?->loadMissing('roles');

        abort_unless($user && $user->hasRole('system_admin'), 403);

        $defaults = [
                'org_name' => (string) (SiteSetting::get('org_name', 'ZANUPF')),
                'support_email' => (string) (SiteSetting::get('support_email', 'support@ttm-group.co.za')),
                'public_site_url' => (string) (SiteSetting::get('public_site_url', '')),
                'legal_privacy_url' => (string) (SiteSetting::get('legal_privacy_url', url('/privacy-policy'))),
                'legal_terms_url' => (string) (SiteSetting::get('legal_terms_url', url('/terms-of-use'))),
                'legal_cookies_url' => (string) (SiteSetting::get('legal_cookies_url', url('/cookies'))),
                'enable_dialogue' => (bool) SiteSetting::get('enable_dialogue', true),
                'require_national_id' => (bool) SiteSetting::get('require_national_id', true),
        ];

        $recommendedAppUrl = trim((string) ($defaults['public_site_url'] ?? ''));
        if ($recommendedAppUrl === '') {
            $recommendedAppUrl = 'https://your-domain.example';
        }

        return view('setup.wizard', [
            'defaults' => $defaults,
            'serverConfig' => [
                'current' => [
                    'APP_NAME' => (string) config('app.name', ''),
                    'APP_URL' => (string) config('app.url', ''),
                    'APP_ENV' => (string) config('app.env', ''),
                    'APP_DEBUG' => (bool) config('app.debug', false),
                ],
                'recommended' => [
                    'APP_NAME' => (string) ($defaults['org_name'] ?? 'ZANUPF'),
                    'APP_URL' => $recommendedAppUrl,
                    'APP_ENV' => 'production',
                    'APP_DEBUG' => false,
                ],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user?->loadMissing('roles');
        abort_unless($user && $user->hasRole('system_admin'), 403);

        $data = $request->validate([
            'org_name' => ['required', 'string', 'max:120'],
            'support_email' => ['required', 'email', 'max:255'],
            'public_site_url' => ['nullable', 'string', 'max:255'],
            'legal_privacy_url' => ['required', 'string', 'max:255'],
            'legal_terms_url' => ['required', 'string', 'max:255'],
            'legal_cookies_url' => ['required', 'string', 'max:255'],
            'enable_dialogue' => ['nullable', 'boolean'],
            'require_national_id' => ['nullable', 'boolean'],
        ]);

        SiteSetting::set('org_name', $data['org_name']);
        SiteSetting::set('support_email', $data['support_email']);
        SiteSetting::set('public_site_url', $data['public_site_url'] ?? '');
        SiteSetting::set('legal_privacy_url', $data['legal_privacy_url']);
        SiteSetting::set('legal_terms_url', $data['legal_terms_url']);
        SiteSetting::set('legal_cookies_url', $data['legal_cookies_url']);
        SiteSetting::set('enable_dialogue', $request->boolean('enable_dialogue', true));
        SiteSetting::set('require_national_id', $request->boolean('require_national_id', true));

        SiteSetting::set('installed_at', now()->toIso8601String());

        return redirect()->route('dashboard')->with('success', 'Setup completed.');
    }
}

