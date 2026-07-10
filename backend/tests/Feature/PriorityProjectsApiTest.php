<?php

namespace Tests\Feature;

use App\Models\PriorityProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriorityProjectsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_list_priority_projects(): void
    {
        PriorityProject::create([
            'title' => 'Vision 2030',
            'slug' => 'vision-2030',
            'is_published' => true,
            'published_at' => now()->subHour(),
            'likes_count' => 0,
        ]);

        $this->getJson('/api/v1/priority-projects')
            ->assertUnauthorized();
    }

    public function test_member_can_list_published_priority_projects(): void
    {
        $project = PriorityProject::create([
            'title' => 'Vision 2030',
            'slug' => 'vision-2030',
            'summary' => 'Summary',
            'is_published' => true,
            'published_at' => now()->subHour(),
            'likes_count' => 2,
        ]);

        $user = User::factory()->create(['surname' => 'Member']);
        $this->sanctumAs($user);

        $this->getJson('/api/v1/priority-projects')
            ->assertOk()
            ->assertJsonPath('data.0.id', $project->id)
            ->assertJsonPath('data.0.title', 'Vision 2030')
            ->assertJsonPath('data.0.liked', false);
    }

    public function test_member_can_show_published_priority_project(): void
    {
        $project = PriorityProject::create([
            'title' => 'Road rehab',
            'slug' => 'road-rehab',
            'summary' => 'Summary',
            'body' => '<p>Details</p>',
            'is_published' => true,
            'published_at' => now()->subHour(),
            'likes_count' => 0,
        ]);

        $user = User::factory()->create(['surname' => 'Member']);
        $this->sanctumAs($user);

        $this->getJson("/api/v1/priority-projects/{$project->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $project->id)
            ->assertJsonPath('data.body', '<p>Details</p>');
    }

    public function test_show_returns_not_found_for_unpublished_project(): void
    {
        $project = PriorityProject::create([
            'title' => 'Draft',
            'slug' => 'draft',
            'is_published' => false,
            'published_at' => null,
            'likes_count' => 0,
        ]);

        $user = User::factory()->create(['surname' => 'Member']);
        $this->sanctumAs($user);

        $this->getJson("/api/v1/priority-projects/{$project->id}")
            ->assertNotFound();
    }
}
