<?php

namespace Tests\Feature;

use App\Models\BackendUserInvitation;
use App\Models\Role;
use App\Models\User;
use App\Notifications\BackendUserInvitationNotification;
use App\Notifications\BackendUserWelcomeNotification;
use App\Services\BackendRoleDutiesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BackendUserInvitationTest extends TestCase
{
    use RefreshDatabase;

    private function seedAdminRoles(): array
    {
        $system = Role::firstOrCreate(['slug' => 'system_admin'], ['name' => 'System Admin']);
        $editor = Role::firstOrCreate(['slug' => 'content_editor'], ['name' => 'Content Editor']);
        $presidium = Role::firstOrCreate(['slug' => 'presidium'], ['name' => 'Presidium']);
        $userManager = Role::firstOrCreate(['slug' => 'user_manager'], ['name' => 'User Manager']);
        $academy = Role::firstOrCreate(['slug' => 'academy_manager'], ['name' => 'Academy Manager']);

        return compact('system', 'editor', 'presidium', 'userManager', 'academy');
    }

    public function test_system_admin_can_send_invitation_with_duties(): void
    {
        Notification::fake();
        $r = $this->seedAdminRoles();

        $admin = User::factory()->create(['surname' => 'Admin', 'email' => 'admin@example.com']);
        $admin->roles()->attach($r['system']->id);

        $response = $this->actingAs($admin)->post(route('admin.users.invite.store'), [
            'email' => 'invitee@example.com',
            'roles' => [$r['academy']->id],
        ]);

        $response->assertRedirect(route('admin.users.index'));
        Notification::assertSentOnDemand(BackendUserInvitationNotification::class, function ($notification) {
            return count($notification->dutyBriefs) === 1
                && $notification->dutyBriefs[0]['slug'] === 'academy_manager'
                && str_contains($notification->email, 'invitee@example.com');
        });
    }

    public function test_user_manager_cannot_provision_backend_users(): void
    {
        Notification::fake();
        $r = $this->seedAdminRoles();

        $manager = User::factory()->create(['surname' => 'Mgr', 'email' => 'mgr@example.com']);
        $manager->roles()->attach($r['userManager']->id);

        $this->actingAs($manager)->post(route('admin.users.invite.store'), [
            'email' => 'bad@example.com',
            'roles' => [$r['editor']->id],
        ])->assertForbidden();

        Notification::assertNothingSent();
    }

    public function test_system_admin_can_create_backend_user_with_welcome_email(): void
    {
        Notification::fake();
        $r = $this->seedAdminRoles();

        $admin = User::factory()->create(['email' => 'admin@example.com']);
        $admin->roles()->attach($r['system']->id);

        $response = $this->actingAs($admin)->post(route('admin.users.store-backend'), [
            'name' => 'Academy',
            'surname' => 'Lead',
            'email' => 'academy.lead@example.com',
            'roles' => [$r['academy']->id],
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $user = User::where('email', 'academy.lead@example.com')->firstOrFail();
        $this->assertTrue($user->roles->contains('slug', 'academy_manager'));

        Notification::assertSentTo($user, BackendUserWelcomeNotification::class);

        $sections = app(BackendRoleDutiesService::class)->accessibleSectionSlugsForRoleIds([$r['academy']->id]);
        $this->assertContains('academy', $sections);
        $this->assertNotContains('roles', $sections);
    }

    public function test_guest_can_accept_invitation_and_login(): void
    {
        $r = $this->seedAdminRoles();

        $admin = User::factory()->create(['surname' => 'Admin', 'email' => 'admin2@example.com']);
        $admin->roles()->attach($r['system']->id);

        $plainToken = 'accepttokenaccepttokenaccepttokenaccepttoken12';
        BackendUserInvitation::create([
            'email' => 'newuser@example.com',
            'token_hash' => BackendUserInvitation::hashToken($plainToken),
            'role_ids' => [$r['editor']->id],
            'invited_by_user_id' => $admin->id,
            'expires_at' => now()->addDay(),
        ]);

        $response = $this->post(route('backend-invitations.accept', ['token' => $plainToken]), [
            'name' => 'Pat',
            'surname' => 'Lee',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'accept_terms' => '1',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $user = User::where('email', 'newuser@example.com')->firstOrFail();
        $this->assertTrue($user->roles->contains('id', $r['editor']->id));
        $this->assertNotNull($user->accepted_terms_at);
        $this->assertNotNull(BackendUserInvitation::where('email', 'newuser@example.com')->whereNotNull('accepted_at')->first());
    }
}
