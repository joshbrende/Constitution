<?php

namespace Tests\Feature;

use App\Models\Province;
use App\Models\Role;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\AdminScopeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DistrictAdminScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_district_assigned_provincial_admin_scopes_user_query(): void
    {
        Role::firstOrCreate(['slug' => 'provincial_admin'], ['name' => 'Provincial Admin']);
        $province = Province::query()->orderBy('id')->firstOrFail();

        $admin = User::factory()->create([
            'province_id' => $province->id,
            'district_id' => 101,
        ]);
        $admin->roles()->attach(Role::where('slug', 'provincial_admin')->first()->id);

        User::factory()->create([
            'province_id' => $province->id,
            'district_id' => 101,
            'email' => 'in-district@example.org.zw',
        ]);
        User::factory()->create([
            'province_id' => $province->id,
            'district_id' => 202,
            'email' => 'other-district@example.org.zw',
        ]);

        $scoped = app(AdminScopeService::class)
            ->applyToUserQuery(User::query(), $admin)
            ->pluck('email')
            ->all();

        $this->assertContains('in-district@example.org.zw', $scoped);
        $this->assertNotContains('other-district@example.org.zw', $scoped);
    }
}
