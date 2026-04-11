<?php

namespace Tests\Feature;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiRateLimitTest extends TestCase
{
    use RefreshDatabase;

    private const REGISTER_PASSWORD = 'Password123!';

    public function test_register_is_rate_limited_per_ip(): void
    {
        Role::firstOrCreate(
            ['slug' => 'student'],
            ['name' => 'Student', 'description' => 'Learner']
        );

        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/v1/auth/register', [
                'name' => 'N',
                'surname' => 'U',
                'email' => "user{$i}@example.com",
                'password' => self::REGISTER_PASSWORD,
                'password_confirmation' => self::REGISTER_PASSWORD,
                'accept_terms' => true,
            ]);
            $this->assertNotSame(429, $response->getStatusCode(), "Request {$i} should not be rate limited yet");
        }

        $blocked = $this->postJson('/api/v1/auth/register', [
            'name' => 'N',
            'surname' => 'U',
            'email' => 'sixth@example.com',
            'password' => self::REGISTER_PASSWORD,
            'password_confirmation' => self::REGISTER_PASSWORD,
            'accept_terms' => true,
        ]);

        $blocked->assertStatus(429);
    }
}
