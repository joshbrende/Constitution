<?php

namespace Tests\Unit;

use App\Models\HomeBanner;
use App\Models\SiteSetting;
use App\Services\Setup\SetupInstallService;
use App\Services\Setup\SetupProgressResolver;
use App\Services\Setup\SetupSystemChecker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SetupProgressResolverTest extends TestCase
{
    use RefreshDatabase;

    public function test_syncs_session_from_database_state(): void
    {
        SiteSetting::set('public_site_url', 'https://test.example');
        SiteSetting::set('org_name', 'Test Org');
        HomeBanner::query()->create([
            'title' => 'Test',
            'subtitle' => 'Banner',
            'image_url' => '/test.jpg',
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $resolver = new SetupProgressResolver(
            app(SetupSystemChecker::class),
            app(SetupInstallService::class)
        );

        $resolver->syncSession();

        $this->assertTrue(session('setup_checks_passed'));
        $this->assertTrue(session('setup_platform_done'));
        $this->assertTrue(session('setup_seed_done'));
    }

    public function test_resume_route_points_to_finish_when_content_seeded(): void
    {
        SiteSetting::set('public_site_url', 'https://test.example');
        SiteSetting::set('org_name', 'Test Org');
        HomeBanner::query()->create([
            'title' => 'Test',
            'subtitle' => 'Banner',
            'image_url' => '/test.jpg',
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $resolver = new SetupProgressResolver(
            app(SetupSystemChecker::class),
            app(SetupInstallService::class)
        );

        $this->assertSame('setup.finish', $resolver->resumeRoute());
    }
}
