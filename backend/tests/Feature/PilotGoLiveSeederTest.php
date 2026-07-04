<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\PilotGoLiveSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PilotGoLiveSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_pilot_go_live_seeder_finalizes_platform_and_presidium(): void
    {
        config([
            'pilot.admin_password' => 'Pilot-Admin-2026!xK9',
            'pilot.public_site_url' => 'http://localhost:8081',
            'pilot.presidium_email' => 'presidium.pilot@zanupf.org.zw',
        ]);

        $this->app->make(PilotGoLiveSeeder::class)->run();

        $this->assertNotEmpty(SiteSetting::get('installed_at'));
        $this->assertSame('ZANU PF', SiteSetting::get('org_name'));
        $this->assertSame('http://localhost:8081', SiteSetting::get('public_site_url'));

        $presidium = User::where('email', 'presidium.pilot@zanupf.org.zw')->firstOrFail();
        $this->assertTrue($presidium->hasRole('presidium'));
    }
}
