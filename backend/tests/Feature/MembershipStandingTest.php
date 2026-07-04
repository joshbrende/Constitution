<?php

namespace Tests\Feature;

use App\Enums\MembershipStanding;
use App\Models\Role;
use App\Models\User;
use App\Services\MembershipStandingService;
use App\Services\PermissionSyncService;
use App\Services\TokenAbilityService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembershipStandingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        app(PermissionSyncService::class)->syncApiPermissions();
    }

    public function test_registration_sets_applicant_standing(): void
    {
        $user = User::factory()->create([
            'membership_standing' => MembershipStanding::Applicant->value,
        ]);

        Role::firstOrCreate(['slug' => 'student'], ['name' => 'Student']);
        $user->roles()->attach(Role::where('slug', 'student')->first()->id);

        $this->assertSame(MembershipStanding::Applicant, app(MembershipStandingService::class)->standing($user->fresh()));
    }

    public function test_student_lacks_dialogue_write_ability(): void
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('slug', 'student')->firstOrFail()->id);
        $user->load('roles');

        $abilities = app(TokenAbilityService::class)->abilitiesForUser($user);

        $this->assertNotContains('dialogue:write', $abilities);
        $this->assertContains('academy:read', $abilities);
    }

    public function test_member_role_includes_dialogue_write(): void
    {
        $user = User::factory()->create();
        Role::firstOrCreate(['slug' => 'member'], ['name' => 'Member']);
        $user->roles()->attach(Role::where('slug', 'member')->first()->id);
        $user->load('roles');

        $abilities = app(TokenAbilityService::class)->abilitiesForUser($user);

        $this->assertContains('dialogue:write', $abilities);
    }

    public function test_suspended_user_gets_profile_read_only_abilities(): void
    {
        $user = User::factory()->create([
            'membership_standing' => MembershipStanding::Suspended->value,
        ]);
        Role::firstOrCreate(['slug' => 'member'], ['name' => 'Member']);
        $user->roles()->attach(Role::where('slug', 'member')->first()->id);
        $user->load('roles');

        $abilities = app(TokenAbilityService::class)->abilitiesForUser($user);

        $this->assertSame(['profile:read'], $abilities);
    }

    public function test_mark_full_member_on_certificate_path(): void
    {
        $user = User::factory()->create([
            'membership_standing' => MembershipStanding::Provisional->value,
        ]);

        app(MembershipStandingService::class)->markFullMember($user, 'test');

        $this->assertSame(MembershipStanding::Member, $user->fresh()->membership_standing);
    }
}
