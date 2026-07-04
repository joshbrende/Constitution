<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicHomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_is_branded_and_not_laravel_default(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Study the Constitution', false)
            ->assertSee('favicon_io/favicon.ico', false)
            ->assertSee('Constitution reader', false)
            ->assertSee('Verify certificate', false)
            ->assertDontSee('Laravel has an incredibly rich ecosystem', false)
            ->assertDontSee('Laracasts', false);
    }

    public function test_home_page_shows_sign_in_for_guests(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Log in', false)
            ->assertSee(route('login'), false);
    }

    public function test_home_page_uses_organisation_name_from_site_settings(): void
    {
        SiteSetting::set('org_name', 'ZANU PF Digital Campus');

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('ZANU PF Digital Campus', false);
    }

    public function test_home_page_shows_store_badges_when_urls_configured(): void
    {
        SiteSetting::set('mobile_app_store_url', 'https://apps.apple.com/app/example');
        SiteSetting::set('mobile_play_store_url', 'https://play.google.com/store/apps/details?id=example');

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('https://apps.apple.com/app/example', false)
            ->assertSee('https://play.google.com/store/apps/details?id=example', false)
            ->assertSee('images/badges/app-store.svg', false)
            ->assertSee('images/badges/google-play.svg', false);
    }
}
