<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    private const REGISTER_PASSWORD = 'Password123!';

    private const NEW_USER_EMAIL = 'newuser@example.com';

    public function test_login_rejects_invalid_credentials_with_422(): void
    {
        User::factory()->create([
            'email' => 'member@example.com',
            'password' => Hash::make('correct-password'),
            'surname' => 'Member',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'member@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['message' => 'The provided credentials are incorrect.']);
    }

    public function test_login_returns_tokens_for_valid_credentials(): void
    {
        User::factory()->create([
            'email' => 'ok@example.com',
            'password' => Hash::make('SecretPass123!'),
            'surname' => 'Ok',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'ok@example.com',
            'password' => 'SecretPass123!',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['user', 'access_token', 'refresh_token']);

        $this->assertNotEmpty($response->json('access_token'));
        $this->assertNotEmpty($response->json('refresh_token'));
    }

    public function test_register_assigns_student_role_and_returns_201(): void
    {
        Role::firstOrCreate(
            ['slug' => 'student'],
            ['name' => 'Student', 'description' => 'Learner']
        );

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'New',
            'surname' => 'User',
            'email' => self::NEW_USER_EMAIL,
            'password' => self::REGISTER_PASSWORD,
            'password_confirmation' => self::REGISTER_PASSWORD,
            'accept_terms' => true,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('user.email', self::NEW_USER_EMAIL);

        $user = User::where('email', self::NEW_USER_EMAIL)->firstOrFail();
        $this->assertTrue($user->roles()->where('slug', 'student')->exists());
    }

    public function test_register_validation_requires_accepted_terms(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'A',
            'surname' => 'B',
            'email' => 'ab@example.com',
            'password' => self::REGISTER_PASSWORD,
            'password_confirmation' => self::REGISTER_PASSWORD,
            'accept_terms' => false,
        ]);

        $response->assertStatus(422);
    }
}
