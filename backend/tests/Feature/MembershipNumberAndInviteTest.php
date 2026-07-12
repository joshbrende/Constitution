<?php

namespace Tests\Feature;

use App\Enums\MembershipSource;
use App\Enums\MembershipStanding;
use App\Models\Course;
use App\Models\Province;
use App\Models\Role;
use App\Models\User;
use App\Services\CertificateApplicationService;
use App\Services\MembershipNumberService;
use App\Services\MembershipStandingService;
use App\Services\PermissionSyncService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MembershipNumberAndInviteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        app(PermissionSyncService::class)->syncAll();
    }

    public function test_mark_full_member_assigns_opaque_number_once(): void
    {
        $user = User::factory()->create([
            'membership_standing' => MembershipStanding::Provisional->value,
        ]);

        app(MembershipStandingService::class)->markFullMember($user, 'test');

        $user->refresh();
        $this->assertSame(MembershipStanding::Member, $user->membership_standing);
        $this->assertNotNull($user->membership_number);
        $this->assertStringStartsWith('ZPF-', $user->membership_number);

        $first = $user->membership_number;
        app(MembershipStandingService::class)->markFullMember($user, 'test');
        $this->assertSame($first, $user->fresh()->membership_number);
    }

    public function test_invite_source_preserved_when_number_assigned(): void
    {
        $user = User::factory()->create([
            'membership_standing' => MembershipStanding::Provisional->value,
            'membership_source' => MembershipSource::Invite->value,
        ]);

        app(MembershipNumberService::class)->ensureForFullMember($user);
        $this->assertNull($user->fresh()->membership_number);

        $user->forceFill(['membership_standing' => MembershipStanding::Member->value])->save();
        app(MembershipNumberService::class)->ensureForFullMember($user->fresh());

        $user->refresh();
        $this->assertSame(MembershipSource::Invite->value, $user->membership_source);
        $this->assertNotNull($user->membership_number);
    }

    public function test_admin_create_member_creates_payment_pending_application(): void
    {
        Notification::fake();

        $this->seedMembershipCourse();

        $admin = $this->makeUserWithRole('system_admin');
        $province = Province::query()->orderBy('id')->first()
            ?? Province::create(['name' => 'Harare', 'code' => 'harare', 'sort_order' => 1]);

        $this->actingAs($admin)
            ->post(route('admin.members.store'), [
                'name' => 'Tariro',
                'surname' => 'Moyo',
                'email' => 'invited.member@example.org.zw',
                'national_id' => '63-1234567A12',
                'province_id' => $province->id,
            ])
            ->assertRedirect(route('admin.members.index'));

        $user = User::where('email', 'invited.member@example.org.zw')->firstOrFail();
        $this->assertSame(MembershipSource::AdminCreated->value, $user->membership_source);
        $this->assertSame(MembershipStanding::Provisional, $user->membership_standing);
        $this->assertNull($user->membership_number);
        $this->assertDatabaseHas('certificate_applications', [
            'user_id' => $user->id,
            'admission_source' => MembershipSource::AdminCreated->value,
            'assessment_attempt_id' => null,
            'status' => 'payment_pending',
        ]);
    }

    public function test_provincial_admin_cannot_invite_members(): void
    {
        $admin = $this->makeUserWithRole('provincial_admin');

        $this->actingAs($admin)
            ->get(route('admin.members.invite.create'))
            ->assertForbidden();
    }

    public function test_create_from_invite_admission_via_service(): void
    {
        Notification::fake();
        $this->seedMembershipCourse();

        $user = User::factory()->create([
            'membership_standing' => MembershipStanding::Applicant->value,
        ]);
        Role::firstOrCreate(['slug' => 'student'], ['name' => 'Student']);
        $user->roles()->attach(Role::where('slug', 'student')->first()->id);

        $app = app(CertificateApplicationService::class)
            ->createFromInviteAdmission($user, MembershipSource::Invite);

        $this->assertNull($app->assessment_attempt_id);
        $this->assertSame(MembershipSource::Invite->value, $app->admission_source);
        $this->assertSame(MembershipStanding::Provisional, $user->fresh()->membership_standing);
        $this->assertNull($user->fresh()->membership_number);
    }

    private function seedMembershipCourse(): Course
    {
        return Course::create([
            'code' => 'MEM-INVITE',
            'title' => 'Membership Course',
            'level' => 'basic',
            'status' => 'published',
            'grants_membership' => true,
            'certificate_fee_amount' => 25.00,
            'certificate_fee_currency' => 'USD',
        ]);
    }

    private function makeUserWithRole(string $slug): User
    {
        $user = User::factory()->create([
            'password' => Hash::make('SecretPass123!'),
        ]);
        $role = Role::firstOrCreate(['slug' => $slug], ['name' => ucfirst(str_replace('_', ' ', $slug))]);
        $user->roles()->attach($role->id);
        $user->load('roles');

        return $user;
    }
}
