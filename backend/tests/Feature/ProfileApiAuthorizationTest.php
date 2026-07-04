<?php

namespace Tests\Feature;

use App\Models\Province;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileApiAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_show_requires_authentication(): void
    {
        $this->getJson('/api/v1/profile')->assertUnauthorized();
    }

    public function test_profile_show_returns_authenticated_user(): void
    {
        $user = User::factory()->create([
            'email' => 'profile@example.org.zw',
            'password' => Hash::make('SecretPass123!'),
            'surname' => 'Profile',
        ]);

        Role::firstOrCreate(['slug' => 'student'], ['name' => 'Student', 'description' => 'Learner']);
        $user->roles()->attach(Role::where('slug', 'student')->firstOrFail()->id);

        $this->sanctumAs($user);

        $this->getJson('/api/v1/profile')
            ->assertOk()
            ->assertJsonPath('data.email', 'profile@example.org.zw')
            ->assertJsonPath('data.surname', 'Profile');
    }

    public function test_profile_update_requires_authentication(): void
    {
        $this->putJson('/api/v1/profile', [
            'national_id' => '08-2047823Q29',
        ])->assertUnauthorized();
    }

    public function test_profile_update_persists_valid_fields(): void
    {
        $user = User::factory()->create([
            'email' => 'update@example.org.zw',
            'surname' => 'Updater',
        ]);
        Role::firstOrCreate(['slug' => 'student'], ['name' => 'Student', 'description' => 'Learner']);
        $user->roles()->attach(Role::where('slug', 'student')->firstOrFail()->id);

        $harare = Province::query()->where('code', 'harare')->firstOrFail();

        $this->sanctumAs($user);

        $this->putJson('/api/v1/profile', [
            'national_id' => '08-2047823Q29',
            'province_id' => $harare->id,
        ])
            ->assertOk()
            ->assertJsonPath('data.national_id', '08-2047823Q29')
            ->assertJsonPath('data.province_id', $harare->id);

        $this->assertSame('08-2047823Q29', $user->fresh()->national_id);
    }

    public function test_profile_update_rejects_invalid_national_id(): void
    {
        $user = User::factory()->create(['email' => 'bad-id@example.org.zw']);
        Role::firstOrCreate(['slug' => 'student'], ['name' => 'Student', 'description' => 'Learner']);
        $user->roles()->attach(Role::where('slug', 'student')->firstOrFail()->id);

        $this->sanctumAs($user);

        $this->putJson('/api/v1/profile', [
            'national_id' => 'not-a-valid-id',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['national_id']);
    }
}
