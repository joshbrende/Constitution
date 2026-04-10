<?php

namespace Tests\Feature;

use App\Models\LibraryCategory;
use App\Models\LibraryDocument;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LibraryApiAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    private function seedDocuments(LibraryCategory $cat): array
    {
        $public = LibraryDocument::create([
            'library_category_id' => $cat->id,
            'title' => 'Public paper',
            'slug' => 'public-paper',
            'published_at' => now()->subDay(),
            'access_rule' => 'public',
        ]);
        $member = LibraryDocument::create([
            'library_category_id' => $cat->id,
            'title' => 'Members only',
            'slug' => 'members-only',
            'published_at' => now()->subDay(),
            'access_rule' => 'member',
        ]);
        $leadership = LibraryDocument::create([
            'library_category_id' => $cat->id,
            'title' => 'Leadership brief',
            'slug' => 'leadership-brief',
            'published_at' => now()->subDay(),
            'access_rule' => 'leadership',
        ]);

        return compact('public', 'member', 'leadership');
    }

    public function test_guest_list_excludes_member_and_leadership_documents(): void
    {
        $cat = LibraryCategory::create(['name' => 'Main', 'slug' => 'main']);
        $docs = $this->seedDocuments($cat);

        $response = $this->getJson('/api/v1/library/documents?per_page=50');

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id')->all();

        $this->assertContains($docs['public']->id, $ids);
        $this->assertNotContains($docs['member']->id, $ids);
        $this->assertNotContains($docs['leadership']->id, $ids);
    }

    public function test_authenticated_member_sees_public_and_member_not_leadership(): void
    {
        $cat = LibraryCategory::create(['name' => 'Main', 'slug' => 'main-2']);
        $docs = $this->seedDocuments($cat);

        $user = User::factory()->create(['surname' => 'Member']);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/library/documents?per_page=50');

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id')->all();

        $this->assertContains($docs['public']->id, $ids);
        $this->assertContains($docs['member']->id, $ids);
        $this->assertNotContains($docs['leadership']->id, $ids);
    }

    public function test_guest_show_member_document_returns_403(): void
    {
        $cat = LibraryCategory::create(['name' => 'Main', 'slug' => 'main-3']);
        $docs = $this->seedDocuments($cat);

        $this->getJson("/api/v1/library/documents/{$docs['member']->id}")
            ->assertForbidden()
            ->assertJsonFragment(['message' => 'This document is restricted. Sign in or request access.']);
    }

    public function test_member_can_show_member_document(): void
    {
        $cat = LibraryCategory::create(['name' => 'Main', 'slug' => 'main-4']);
        $docs = $this->seedDocuments($cat);

        $user = User::factory()->create(['surname' => 'Member']);
        Sanctum::actingAs($user);

        $this->getJson("/api/v1/library/documents/{$docs['member']->id}")
            ->assertOk()
            ->assertJsonPath('data.title', 'Members only');
    }

    public function test_presidium_user_can_show_leadership_document(): void
    {
        $cat = LibraryCategory::create(['name' => 'Main', 'slug' => 'main-5']);
        $docs = $this->seedDocuments($cat);

        $role = Role::firstOrCreate(
            ['slug' => 'presidium'],
            ['name' => 'Presidium', 'description' => 'Test']
        );
        $user = User::factory()->create(['surname' => 'Lead']);
        $user->roles()->attach($role->id);
        $user->load('roles');

        Sanctum::actingAs($user);

        $this->getJson("/api/v1/library/documents/{$docs['leadership']->id}")
            ->assertOk()
            ->assertJsonPath('data.title', 'Leadership brief');
    }
}
