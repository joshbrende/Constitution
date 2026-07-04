<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthEndpointTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        SiteSetting::set('installed_at', now()->toIso8601String());
    }

    public function test_web_health_endpoint_returns_status_payload(): void
    {
        $response = $this->get('/health');

        $response->assertJsonStructure(['status', 'checks']);
        $this->assertContains($response->json('status'), ['ok', 'degraded']);
    }

    public function test_api_health_endpoint_returns_status_payload(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertJsonStructure(['status', 'checks']);
        $this->assertContains($response->json('status'), ['ok', 'degraded']);
    }
}
