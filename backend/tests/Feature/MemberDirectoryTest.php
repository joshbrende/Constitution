<?php

namespace Tests\Feature;

use App\Enums\MembershipStanding;
use App\Enums\MembershipWingStatus;
use App\Models\Membership;
use App\Models\Province;
use App\Models\Role;
use App\Models\User;
use App\Services\WingMembershipService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberDirectoryTest extends TestCase
{
    use RefreshDatabase;

    private function attachMemberRole(User $user): void
    {
        $user->roles()->syncWithoutDetaching([
            Role::firstOrCreate(['slug' => 'member'], ['name' => 'Member'])->id,
        ]);
        $user->load('roles');
    }

    private function makeFullMember(array $attrs = []): User
    {
        $user = User::factory()->create(array_merge([
            'membership_standing' => MembershipStanding::Member->value,
            'membership_number' => 'ZPF-M'.strtoupper(substr(uniqid(), -6)),
            'wing' => 'main',
            'name' => 'Tariro',
            'surname' => 'Moyo',
        ], $attrs));
        $this->attachMemberRole($user);
        app(WingMembershipService::class)->ensureForFullMember($user);

        return $user->fresh();
    }

    public function test_applicant_cannot_access_directory(): void
    {
        $user = User::factory()->create([
            'membership_standing' => MembershipStanding::Applicant->value,
        ]);
        $user->roles()->attach(Role::firstOrCreate(['slug' => 'student'], ['name' => 'Student'])->id);

        $this->sanctumAs($user);

        $this->getJson('/api/v1/members')->assertForbidden();
    }

    public function test_provisional_with_member_role_still_forbidden(): void
    {
        $user = User::factory()->create([
            'membership_standing' => MembershipStanding::Provisional->value,
        ]);
        $this->attachMemberRole($user);

        $this->sanctumAs($user);

        $this->getJson('/api/v1/members')
            ->assertForbidden()
            ->assertJsonPath('code', 'FULL_MEMBER_REQUIRED');
    }

    public function test_full_member_can_list_and_search_minimal_fields(): void
    {
        $viewer = $this->makeFullMember([
            'membership_number' => 'ZPF-VIEWER1',
            'name' => 'Viewer',
            'surname' => 'One',
        ]);

        $harare = Province::query()->where('code', 'harare')->first()
            ?? Province::create(['name' => 'Harare', 'code' => 'harare']);

        $other = $this->makeFullMember([
            'membership_number' => 'ZPF-TARGET9',
            'name' => 'Chipo',
            'surname' => 'Ncube',
            'email' => 'secret-chipo@example.org.zw',
            'national_id' => '63-999999-A-99',
            'province_id' => $harare->id,
            'wing' => 'youth',
        ]);

        Membership::query()->updateOrCreate(
            ['user_id' => $other->id, 'wing' => 'youth'],
            [
                'status' => MembershipWingStatus::Active->value,
                'joined_at' => now(),
                'ended_at' => null,
            ]
        );

        $this->sanctumAs($viewer);

        $response = $this->getJson('/api/v1/members?q=Ncube')->assertOk();

        $row = collect($response->json('data'))->firstWhere('id', $other->id);
        $this->assertNotNull($row);
        $this->assertSame('Chipo', $row['name']);
        $this->assertSame('Ncube', $row['surname']);
        $this->assertSame('ZPF-TARGET9', $row['membership_number']);
        $this->assertSame($harare->id, $row['province']['id']);
        $this->assertArrayHasKey('active_wings', $row);
        $this->assertArrayNotHasKey('email', $row);
        $this->assertArrayNotHasKey('national_id', $row);

        $encoded = json_encode($response->json());
        $this->assertStringNotContainsString('secret-chipo@example.org.zw', $encoded);
        $this->assertStringNotContainsString('63-999999-A-99', $encoded);
    }

    public function test_wing_filter_uses_active_memberships(): void
    {
        $viewer = $this->makeFullMember(['membership_number' => 'ZPF-VIEWER2']);

        $youth = $this->makeFullMember([
            'membership_number' => 'ZPF-YOUTH1',
            'surname' => 'Youthful',
            'wing' => 'youth',
        ]);
        app(WingMembershipService::class)->ensureActive($youth, 'youth');

        $women = $this->makeFullMember([
            'membership_number' => 'ZPF-WOMEN1',
            'surname' => 'WomenOnly',
            'wing' => 'women',
        ]);
        app(WingMembershipService::class)->ensureActive($women, 'women');

        $this->sanctumAs($viewer);

        $ids = collect($this->getJson('/api/v1/members?wing=youth')->assertOk()->json('data'))
            ->pluck('id')
            ->all();

        $this->assertContains($youth->id, $ids);
        $this->assertNotContains($women->id, $ids);
    }
}
