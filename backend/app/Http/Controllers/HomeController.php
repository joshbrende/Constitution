<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Services\PlatformBrandingService;
use App\Services\Setup\SetupProgressResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        protected SetupProgressResolver $setupProgress,
        protected PlatformBrandingService $branding
    ) {}

    public function __invoke(Request $request): View
    {
        $setupComplete = $this->isSetupComplete();

        return view('home', [
            'orgName' => $this->branding->orgName(),
            'supportEmail' => $this->branding->supportEmail(),
            'mobileAppStoreUrl' => $this->branding->mobileAppStoreUrl(),
            'mobilePlayStoreUrl' => $this->branding->mobilePlayStoreUrl(),
            'setupComplete' => $setupComplete,
            'navCta' => $this->resolveNavCta($request, $setupComplete),
        ]);
    }

    private function isSetupComplete(): bool
    {
        if (! Schema::hasTable('site_settings')) {
            return false;
        }

        return ! empty(SiteSetting::get('installed_at'));
    }

    /**
     * @return array{label: string, url: string, variant: string}|null
     */
    private function resolveNavCta(Request $request, bool $setupComplete): ?array
    {
        if (! $request->user()) {
            return null;
        }

        if (! $setupComplete) {
            $route = $this->setupProgress->resumeRoute() ?? 'setup.index';

            return [
                'label' => 'Continue setup',
                'url' => route($route),
                'variant' => 'accent',
            ];
        }

        return [
            'label' => 'Dashboard',
            'url' => route('dashboard'),
            'variant' => 'primary',
        ];
    }
}
