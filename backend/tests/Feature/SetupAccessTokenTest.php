<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SetupAccessTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_setup_requires_token_when_configured(): void
    {
        config(['setup.access_token' => 'secret-install-token']);

        $this->get(route('setup.index'))->assertForbidden();

        $this->withHeader('X-Setup-Token', 'secret-install-token')
            ->followingRedirects()
            ->get(route('setup.index'))
            ->assertOk();
    }

    public function test_setup_header_token_is_accepted(): void
    {
        config(['setup.access_token' => 'header-token']);

        $this->followingRedirects()
            ->withHeader('X-Setup-Token', 'header-token')
            ->get(route('setup.index'))
            ->assertOk();
    }
}
