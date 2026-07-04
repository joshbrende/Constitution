<?php

namespace App\Services\Setup;

use App\Models\HomeBanner;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Schema;

class SetupProgressResolver
{
    public function __construct(
        protected SetupSystemChecker $checker,
        protected SetupInstallService $installService
    ) {}

    /**
     * Restore wizard session flags from database state (survives browser restarts).
     */
    public function syncSession(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        if ($this->checksComplete()) {
            session(['setup_checks_passed' => true]);
        }

        if ($this->installService->systemAdminExists()) {
            session(['setup_admin_done' => true]);
        }

        if ($this->platformConfigured()) {
            session(['setup_platform_done' => true]);
        }

        if ($this->contentSeeded()) {
            session(['setup_seed_done' => true]);
        }
    }

    public function checksComplete(): bool
    {
        return $this->checker->canUseApplicationDatabase()
            && ! $this->checker->hasPendingMigrations();
    }

    public function platformConfigured(): bool
    {
        $url = trim((string) SiteSetting::get('public_site_url', ''));
        $org = trim((string) SiteSetting::get('org_name', ''));

        return $url !== '' && $org !== '';
    }

    public function contentSeeded(): bool
    {
        if (! Schema::hasTable('home_banners')) {
            return false;
        }

        return HomeBanner::query()->exists();
    }

    public function resumeRoute(): ?string
    {
        if (! $this->checksComplete()) {
            return 'setup.checks';
        }

        if (! $this->installService->systemAdminExists()) {
            return 'setup.admin';
        }

        if (! $this->platformConfigured()) {
            return 'setup.platform';
        }

        if (! $this->contentSeeded()) {
            return 'setup.seed';
        }

        return 'setup.finish';
    }
}
