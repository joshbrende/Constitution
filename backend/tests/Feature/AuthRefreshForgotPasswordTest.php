<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class AuthRefreshForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_refresh_rotates_token_and_returns_new_pair(): void
    {
        $user = User::factory()->create([
            'email' => 'refresh@example.org.zw',
            'password' => Hash::make('SecretPass123!'),
        ]);

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'refresh@example.org.zw',
            'password' => 'SecretPass123!',
        ])->assertOk();

        $refreshToken = (string) $login->json('refresh_token');

        $response = $this->postJson('/api/v1/auth/refresh', [
            'refresh_token' => $refreshToken,
        ]);

        $response->assertOk()
            ->assertJsonStructure(['user', 'access_token', 'refresh_token']);

        $this->assertNotSame($refreshToken, $response->json('refresh_token'));

        $this->postJson('/api/v1/auth/refresh', [
            'refresh_token' => $refreshToken,
        ])->assertUnauthorized();
    }

    public function test_refresh_rejects_placeholder_token(): void
    {
        $this->postJson('/api/v1/auth/refresh', [
            'refresh_token' => 'PASTE_REFRESH_TOKEN_FROM_LOGIN',
        ])->assertUnauthorized()
            ->assertJsonFragment(['message' => 'Refresh token expired or invalid. Please sign in again.']);
    }

    public function test_forgot_password_sends_reset_for_known_email(): void
    {
        Notification::fake();

        User::factory()->create(['email' => 'known@example.org.zw']);

        $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'known@example.org.zw',
        ])->assertOk();

        Notification::assertSentTo(
            User::where('email', 'known@example.org.zw')->firstOrFail(),
            ResetPassword::class
        );
    }

    public function test_forgot_password_returns_422_for_unknown_email(): void
    {
        $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'missing@example.org.zw',
        ])->assertStatus(422)
            ->assertJsonFragment(['message' => "We can't find a user with that email address."]);
    }

    public function test_register_requires_password_confirmation_and_accept_terms(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Tariro',
            'surname' => 'Moyo',
            'email' => 'tariro@example.org.zw',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'DifferentPass!',
            'accept_terms' => true,
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        $this->postJson('/api/v1/auth/register', [
            'name' => 'Tariro',
            'surname' => 'Moyo',
            'email' => 'tariro2@example.org.zw',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'accept_terms' => false,
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['accept_terms']);
    }

    public function test_logout_revokes_access_and_refresh_tokens(): void
    {
        $user = User::factory()->create([
            'email' => 'logout@example.org.zw',
            'password' => Hash::make('SecretPass123!'),
        ]);

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'logout@example.org.zw',
            'password' => 'SecretPass123!',
        ])->assertOk();

        $accessToken = (string) $login->json('access_token');
        $refreshToken = (string) $login->json('refresh_token');
        $plainToken = explode('|', $accessToken, 2)[1];

        $this->postJson('/api/v1/auth/logout', [], [
            'Authorization' => 'Bearer '.$accessToken,
        ])
            ->assertOk()
            ->assertJsonPath('message', 'Logged out successfully.');

        $this->assertNull(PersonalAccessToken::findToken($plainToken));

        $this->app->get('auth')->forgetGuards();

        $this->getJson('/api/v1/profile', [
            'Authorization' => 'Bearer '.$accessToken,
        ])->assertUnauthorized();

        $this->postJson('/api/v1/auth/refresh', [
            'refresh_token' => $refreshToken,
        ])->assertUnauthorized();
    }

    public function test_logout_requires_authentication(): void
    {
        $this->postJson('/api/v1/auth/logout')->assertUnauthorized();
    }
}
