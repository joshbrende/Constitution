<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_returns_200(): void
    {
        $this->withoutExceptionHandling();

        $response = $this->get(route('login'));
        $response->assertStatus(200);
    }

    public function test_login_with_invalid_credentials_returns_back_with_errors(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'nobody@example.com',
            'password' => 'wrong',
        ]);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['email']);
    }

    public function test_login_with_valid_credentials_redirects(): void
    {
        $role = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web'], ['guard_name' => 'web']);
        $user = User::create([
            'name' => 'Test',
            'email' => 'student@example.com',
            'password' => 'password',
        ]);
        $user->roles()->sync([$role->id]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    public function test_register_page_returns_200(): void
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
    }

    public function test_register_creates_user_and_redirects(): void
    {
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web'], ['guard_name' => 'web']);

        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'new@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);
        $response->assertRedirect(route('verification.notice'));
        $this->assertDatabaseHas('users', ['email' => 'new@example.com']);
        $this->assertAuthenticated();
    }
}
