<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class HealthEndpointRedisTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        SiteSetting::set('installed_at', now()->toIso8601String());
    }

    public function test_api_health_ok_when_database_up_and_redis_not_required(): void
    {
        config([
            'queue.default' => 'sync',
            'cache.default' => 'array',
            'session.driver' => 'array',
        ]);

        $response = $this->getJson('/api/v1/health');

        $response->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('checks.database', true)
            ->assertJsonMissingPath('checks.redis');
    }

    public function test_api_health_degraded_when_redis_required_but_unreachable(): void
    {
        config([
            'queue.default' => 'redis',
            'cache.default' => 'array',
            'database.redis.health.host' => '127.0.0.1',
            'database.redis.health.port' => 6399,
        ]);

        Redis::purge('health');

        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(503)
            ->assertJsonPath('status', 'degraded')
            ->assertJsonPath('checks.database', true)
            ->assertJsonPath('checks.redis', false);
    }
}
