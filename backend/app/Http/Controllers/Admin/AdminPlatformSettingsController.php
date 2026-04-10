<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPlatformSettingsController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $user?->loadMissing('roles');

        abort_unless($user && $user->hasRole('system_admin'), 403);

        return view('admin.platform-settings.edit', [
            'settings' => [
                'org_name' => (string) SiteSetting::get('org_name', 'ZANUPF'),
                'support_email' => (string) SiteSetting::get('support_email', 'support@ttm-group.co.za'),
                'public_site_url' => (string) SiteSetting::get('public_site_url', ''),
                'legal_privacy_url' => (string) SiteSetting::get('legal_privacy_url', url('/privacy-policy')),
                'legal_terms_url' => (string) SiteSetting::get('legal_terms_url', url('/terms-of-use')),
                'legal_cookies_url' => (string) SiteSetting::get('legal_cookies_url', url('/cookies')),
                'enable_dialogue' => (bool) SiteSetting::get('enable_dialogue', true),
                'require_national_id' => (bool) SiteSetting::get('require_national_id', true),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
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

        return redirect()
            ->route('admin.platform-settings.edit')
            ->with('success', 'Platform settings updated.');
    }
}

