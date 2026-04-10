<?php

namespace Tests\Feature;

use App\Models\DialogueChannel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DialogueApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_dialogue_channels_requires_authentication(): void
    {
        $this->getJson('/api/v1/dialogue/channels')->assertUnauthorized();
    }

    public function test_authenticated_user_can_list_dialogue_channels(): void
    {
        DialogueChannel::create([
            'name' => 'National',
            'slug' => 'national',
            'description' => 'Test channel',
            'is_public' => true,
            'min_role_slug' => null,
        ]);

        $user = User::factory()->create(['surname' => 'Chatter']);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/dialogue/channels');

        $response->assertOk()
            ->assertJsonStructure(['data'])
            ->assertJsonCount(1, 'data');

        $this->assertSame('National', $response->json('data.0.name'));
    }
}
