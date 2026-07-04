<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SetupWizardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Docker .env may seed admin@zanupf.org.zw via ADMIN_SEED_PASSWORD; exercise the admin step in tests.
        User::query()->each(function (User $user): void {
            $user->roles()->detach();
            $user->delete();
        });

        if (Schema::hasTable('site_settings')) {
            SiteSetting::query()->where('key', 'installed_at')->delete();
        }

        // docker-compose sets APP_ENV=production; keep setup wizard reachable in tests.
        $this->app['env'] = 'testing';
        config(['setup.access_token' => '']);
    }

    public function test_setup_index_is_public_when_not_installed(): void
    {
        $response = $this->get(route('setup.index'));

        $response->assertOk()
            ->assertSee('Welcome to the Platform Setup Wizard')
            ->assertSee('Logo.png', false);
    }

    public function test_setup_checks_page_shows_system_checks(): void
    {
        $this->get(route('setup.checks'))
            ->assertOk()
            ->assertSee('System checks');
    }

    public function test_setup_returns_not_found_after_installation(): void
    {
        SiteSetting::set('installed_at', now()->toIso8601String());

        $this->get(route('setup.index'))->assertNotFound();
    }

    public function test_dashboard_redirects_to_setup_when_not_installed(): void
    {
        $user = User::factory()->create(['surname' => 'Admin']);
        $role = Role::firstOrCreate(['slug' => 'system_admin'], ['name' => 'System Admin']);
        $user->roles()->attach($role->id);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('setup.index'));
    }

    public function test_wizard_creates_admin_and_completes_installation(): void
    {
        $this->get(route('setup.index'))->assertOk();

        $this->get(route('setup.checks'))->assertOk();

        $this->post(route('setup.continue'))
            ->assertRedirect(route('setup.admin'));

        $this->post(route('setup.admin.store'), [
            'name' => 'Install',
            'surname' => 'Admin',
            'email' => 'install.admin@zanupf.org.zw',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ])->assertRedirect(route('setup.platform'));

        $this->assertDatabaseHas('users', ['email' => 'install.admin@zanupf.org.zw']);
        $this->assertTrue(
            User::where('email', 'install.admin@zanupf.org.zw')->first()?->hasRole('system_admin') ?? false
        );

        $this->post(route('setup.platform.store'), [
            'org_name' => 'ZANUPF Test',
            'support_email' => 'support@academy.example.org.zw',
            'install_protocol' => 'https',
            'install_domain' => 'academy.example.org.zw',
            'install_directory' => '',
            'legal_privacy_url' => url('/privacy-policy'),
            'legal_terms_url' => url('/terms-of-use'),
            'legal_cookies_url' => url('/cookies'),
            'enable_dialogue' => '1',
            'require_national_id' => '1',
        ])->assertRedirect(route('setup.seed'));

        $this->assertSame('ZANUPF Test', SiteSetting::get('org_name'));

        $this->post(route('setup.seed.run'))
            ->assertRedirect(route('setup.finish'));

        $this->assertGreaterThan(0, \App\Models\HomeBanner::count());

        $this->get(route('setup.finish'))
            ->assertOk()
            ->assertSee('Production checklist')
            ->assertSee('php artisan storage:link');

        $this->post(route('setup.complete'))
            ->assertRedirect(route('dashboard'));

        $this->assertNotEmpty(SiteSetting::get('installed_at'));
        $this->get(route('setup.index'))->assertNotFound();
    }

    public function test_platform_rejects_invalid_legal_urls(): void
    {
        $this->post(route('setup.continue'));

        $this->post(route('setup.admin.store'), [
            'name' => 'Install',
            'surname' => 'Admin',
            'email' => 'invalid-url.admin@zanupf.org.zw',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $this->post(route('setup.platform.store'), [
            'org_name' => 'ZANUPF Test',
            'support_email' => 'support@academy.example.org.zw',
            'install_protocol' => 'https',
            'install_domain' => 'academy.example.org.zw',
            'install_directory' => '',
            'legal_privacy_url' => 'not-a-valid-url',
            'legal_terms_url' => url('/terms-of-use'),
            'legal_cookies_url' => url('/cookies'),
        ])->assertSessionHasErrors(['legal_privacy_url']);
    }
}
