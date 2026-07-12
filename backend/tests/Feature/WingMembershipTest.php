<?php

namespace Tests\Feature;

use App\Enums\MembershipStanding;
use App\Enums\MembershipWingStatus;
use App\Models\Membership;
use App\Models\User;
use App\Services\MembershipStandingService;
use App\Services\WingMembershipService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WingMembershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_member_gets_main_membership(): void
    {
        $user = User::factory()->create([
            'membership_standing' => MembershipStanding::Provisional->value,
            'wing' => null,
        ]);

        app(MembershipStandingService::class)->markFullMember($user, 'test');

        $user->refresh();
        $this->assertDatabaseHas('memberships', [
            'user_id' => $user->id,
            'wing' => 'main',
            'status' => MembershipWingStatus::Active->value,
        ]);
        $this->assertSame('main', $user->wing);
    }

    public function test_full_member_with_legacy_wing_gets_main_and_league(): void
    {
        $user = User::factory()->create([
            'membership_standing' => MembershipStanding::Provisional->value,
            'wing' => 'youth',
        ]);

        app(MembershipStandingService::class)->markFullMember($user, 'test');

        $wings = app(WingMembershipService::class)->activeWings($user->fresh());
        $this->assertContains('main', $wings);
        $this->assertContains('youth', $wings);
        $this->assertSame('youth', $user->fresh()->wing);
    }

    public function test_admin_wing_sync_adds_league_for_full_member(): void
    {
        $user = User::factory()->create([
            'membership_standing' => MembershipStanding::Member->value,
            'wing' => 'main',
        ]);
        app(WingMembershipService::class)->ensureForFullMember($user);

        $admin = User::factory()->create();
        app(WingMembershipService::class)->syncFromLegacyWingField($user->fresh(), 'women', $admin);

        $wings = app(WingMembershipService::class)->activeWings($user->fresh());
        $this->assertContains('main', $wings);
        $this->assertContains('women', $wings);
        $this->assertSame('women', $user->fresh()->wing);
    }

    public function test_ending_league_keeps_main_and_updates_primary_wing(): void
    {
        $user = User::factory()->create([
            'membership_standing' => MembershipStanding::Member->value,
            'wing' => 'youth',
        ]);
        $service = app(WingMembershipService::class);
        $service->ensureForFullMember($user);

        $service->end($user->fresh(), 'youth');

        $wings = $service->activeWings($user->fresh());
        $this->assertContains('main', $wings);
        $this->assertNotContains('youth', $wings);
        $this->assertSame('main', $user->fresh()->wing);

        $this->assertDatabaseHas('memberships', [
            'user_id' => $user->id,
            'wing' => 'youth',
            'status' => MembershipWingStatus::Ended->value,
        ]);
    }

    public function test_cannot_end_main_membership(): void
    {
        $user = User::factory()->create([
            'membership_standing' => MembershipStanding::Member->value,
        ]);
        $service = app(WingMembershipService::class);
        $service->ensureForFullMember($user);

        $this->expectException(\InvalidArgumentException::class);
        $service->end($user->fresh(), 'main');
    }

    public function test_reactivate_does_not_duplicate_rows(): void
    {
        $user = User::factory()->create([
            'membership_standing' => MembershipStanding::Member->value,
            'wing' => 'veterans',
        ]);
        $service = app(WingMembershipService::class);
        $service->ensureForFullMember($user);
        $service->end($user->fresh(), 'veterans');
        $service->ensureActive($user->fresh(), 'veterans');

        $this->assertSame(
            1,
            Membership::query()->where('user_id', $user->id)->where('wing', 'veterans')->count()
        );
        $this->assertSame(
            MembershipWingStatus::Active,
            Membership::query()->where('user_id', $user->id)->where('wing', 'veterans')->first()->status
        );
    }

    public function test_sync_league_memberships_adds_and_removes(): void
    {
        $user = User::factory()->create([
            'membership_standing' => MembershipStanding::Member->value,
            'wing' => 'main',
        ]);
        $admin = User::factory()->create();
        $service = app(WingMembershipService::class);
        $service->ensureForFullMember($user);

        $service->syncLeagueMemberships($user->fresh(), ['youth', 'women'], 'women', $admin);
        $wings = $service->activeWings($user->fresh());
        $this->assertContains('main', $wings);
        $this->assertContains('youth', $wings);
        $this->assertContains('women', $wings);
        $this->assertSame('women', $user->fresh()->wing);

        $service->syncLeagueMemberships($user->fresh(), ['youth'], 'youth', $admin);
        $wings = $service->activeWings($user->fresh());
        $this->assertContains('youth', $wings);
        $this->assertNotContains('women', $wings);
        $this->assertSame('youth', $user->fresh()->wing);
    }

    public function test_admin_user_update_saves_multiple_leagues(): void
    {
        $adminRole = \App\Models\Role::firstOrCreate(['slug' => 'system_admin'], ['name' => 'System Admin']);
        $admin = User::factory()->create(['surname' => 'Admin']);
        $admin->roles()->attach($adminRole->id);

        $target = User::factory()->create([
            'membership_standing' => MembershipStanding::Member->value,
            'wing' => 'main',
        ]);
        app(WingMembershipService::class)->ensureForFullMember($target);

        $this->actingAs($admin)->put(route('admin.users.update', $target), [
            'league_wings' => ['youth', 'veterans'],
            'primary_wing' => 'youth',
            'membership_standing' => MembershipStanding::Member->value,
        ])->assertRedirect(route('admin.users.index'));

        $wings = app(WingMembershipService::class)->activeWings($target->fresh());
        $this->assertContains('main', $wings);
        $this->assertContains('youth', $wings);
        $this->assertContains('veterans', $wings);
        $this->assertSame('youth', $target->fresh()->wing);
    }

    public function test_profile_api_returns_memberships_and_active_wings(): void
    {
        $user = User::factory()->create([
            'membership_standing' => MembershipStanding::Member->value,
            'wing' => 'women',
        ]);

        app(WingMembershipService::class)->syncLeagueMemberships(
            $user,
            ['youth', 'women'],
            'women'
        );

        $this->sanctumAs($user);

        $response = $this->getJson('/api/v1/profile')->assertOk();

        $wings = $response->json('data.active_wings');
        $this->assertIsArray($wings);
        $this->assertContains('main', $wings);
        $this->assertContains('youth', $wings);
        $this->assertContains('women', $wings);

        $memberships = collect($response->json('data.memberships'));
        $this->assertTrue($memberships->contains(fn ($m) => ($m['wing'] ?? null) === 'youth' && ($m['status'] ?? null) === 'active'));
        $this->assertTrue($memberships->contains(fn ($m) => ($m['wing'] ?? null) === 'women' && ($m['status'] ?? null) === 'active'));
    }
}
