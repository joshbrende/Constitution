<?php

namespace Tests\Feature;

use App\Models\PriorityProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PriorityProjectLikePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_like_unpublished_project(): void
    {
        $project = PriorityProject::create([
            'title' => 'Draft',
            'slug' => 'draft-pp',
            'is_published' => false,
            'published_at' => null,
            'likes_count' => 0,
        ]);

        $user = User::factory()->create(['surname' => 'Voter']);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/priority-projects/{$project->id}/like");

        $response->assertForbidden();
    }

    public function test_can_like_published_project(): void
    {
        $project = PriorityProject::create([
            'title' => 'Live',
            'slug' => 'live-pp',
            'is_published' => true,
            'published_at' => now()->subHour(),
            'likes_count' => 0,
        ]);

        $user = User::factory()->create(['surname' => 'Voter']);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/priority-projects/{$project->id}/like");

        $response->assertOk()
            ->assertJsonPath('data.liked', true);
    }
}
