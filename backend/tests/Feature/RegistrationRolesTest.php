<?php

namespace Tests\Feature;

use App\Models\Province;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_web_registration_assigns_student_only_not_member(): void
    {
        Role::firstOrCreate(['slug' => 'student'], ['name' => 'Student']);
        Role::firstOrCreate(['slug' => 'member'], ['name' => 'Member']);

        $province = Province::query()->orderBy('id')->firstOrFail();

        $response = $this->post('/register', [
            'name' => 'Jane',
            'surname' => 'Doe',
            'email' => 'jane@example.org.zw',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'accept_terms' => '1',
            'province_id' => $province->id,
        ]);

        $response->assertRedirect('/dashboard');

        $user = User::where('email', 'jane@example.org.zw')->firstOrFail();
        $this->assertTrue($user->roles()->where('slug', 'student')->exists());
        $this->assertFalse($user->roles()->where('slug', 'member')->exists());
        $this->assertNotNull($user->accepted_terms_at);
    }
}

