<?php

namespace Tests\Feature;

use App\Models\Province;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\ProvincialPilotSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProvincialPilotSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_pilot_seeder_creates_harare_and_bulawayo_admins(): void
    {
        config(['pilot.admin_password' => 'Pilot-Admin-2026!xK9']);

        // Invoke directly so runtime config override applies ($this->seed() uses a subprocess).
        $this->app->make(ProvincialPilotSeeder::class)->run();

        $harare = Province::query()->where('code', 'harare')->firstOrFail();
        $bulawayo = Province::query()->where('code', 'bulawayo')->firstOrFail();

        $harareAdmin = User::where('email', 'harare.pilot@zanupf.org.zw')->firstOrFail();
        $bulawayoAdmin = User::where('email', 'bulawayo.pilot@zanupf.org.zw')->firstOrFail();

        $this->assertSame($harare->id, (int) $harareAdmin->province_id);
        $this->assertSame($bulawayo->id, (int) $bulawayoAdmin->province_id);
        $this->assertTrue($harareAdmin->hasRole('provincial_admin'));
        $this->assertTrue($bulawayoAdmin->hasRole('provincial_admin'));

        $this->assertSame(1, SiteSetting::get('pilot_phase'));
        $pilotProvinces = SiteSetting::get('pilot_provinces');
        $this->assertIsArray($pilotProvinces);
        $this->assertCount(2, $pilotProvinces);
    }
}
