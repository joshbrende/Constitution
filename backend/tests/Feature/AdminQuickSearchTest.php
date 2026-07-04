<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Services\PermissionSyncService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminQuickSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        app(PermissionSyncService::class)->syncAll();
    }

    public function test_quick_search_limits_groups_to_accessible_sections(): void
    {
        $dialogueRole = Role::query()->where('slug', 'dialogue_moderator')->firstOrFail();
        $editorRole = Role::query()->where('slug', 'content_editor')->firstOrFail();

        User::factory()->create([
            'name' => 'Unique',
            'surname' => 'SearchTarget',
            'email' => 'unique-search-target@example.org.zw',
        ]);

        $moderator = User::factory()->create(['surname' => 'Mod', 'email' => 'mod@example.org.zw']);
        $moderator->roles()->attach($dialogueRole->id);

        $editor = User::factory()->create(['surname' => 'Ed', 'email' => 'editor@example.org.zw']);
        $editor->roles()->attach($editorRole->id);

        $modResponse = $this->actingAs($moderator)->getJson(route('admin.quick-search', ['q' => 'Unique']));
        $modResponse->assertOk();
        $modGroupKeys = collect($modResponse->json('data.groups'))->pluck('key')->all();
        $this->assertNotContains('users', $modGroupKeys);

        $editorResponse = $this->actingAs($editor)->getJson(route('admin.quick-search', ['q' => 'Unique']));
        $editorResponse->assertOk();
        $editorGroupKeys = collect($editorResponse->json('data.groups'))->pluck('key')->all();
        $this->assertContains('users', $editorGroupKeys);
    }
}
