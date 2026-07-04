<?php

namespace Tests\Feature;

use App\Models\Province;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProvincesPublicApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_list_provinces_without_authentication(): void
    {
        $response = $this->getJson('/api/v1/provinces');

        $response->assertOk()
            ->assertJsonStructure(['data' => [['id', 'name', 'code']]]);

        $this->assertGreaterThanOrEqual(10, count($response->json('data')));
        $this->assertSame('Bulawayo', $response->json('data.0.name'));
    }

    public function test_authenticated_user_can_still_list_provinces(): void
    {
        $user = User::factory()->create(['surname' => 'Picker']);
        $this->sanctumAs($user);

        $this->getJson('/api/v1/provinces')
            ->assertOk()
            ->assertJsonPath('data.1.name', 'Harare');
    }

    public function test_provinces_are_ordered_by_sort_order(): void
    {
        $response = $this->getJson('/api/v1/provinces');

        $response->assertOk();

        $ids = collect($response->json('data'))->pluck('id')->all();
        $expected = Province::orderBy('sort_order')->pluck('id')->all();

        $this->assertSame($expected, $ids);
    }
}
