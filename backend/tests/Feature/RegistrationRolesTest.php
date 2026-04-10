<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_web_registration_assigns_student_only_not_member(): void
    {
        Role::create(['name' => 'Student', 'slug' => 'student']);
        Role::create(['name' => 'Member', 'slug' => 'member']);

        $response = $this->post('/register', [
            'name' => 'Jane',
            'surname' => 'Doe',
            'email' => 'jane@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'accept_terms' => '1',
        ]);

        $response->assertRedirect('/dashboard');

        $user = User::where('email', 'jane@example.com')->firstOrFail();
        $this->assertTrue($user->roles()->where('slug', 'student')->exists());
        $this->assertFalse($user->roles()->where('slug', 'member')->exists());
    }
}

