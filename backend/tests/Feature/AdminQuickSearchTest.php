<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminQuickSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_quick_search_limits_groups_to_accessible_sections(): void
    {
        $dialogueRole = Role::firstOrCreate(
            ['slug' => 'dialogue_moderator'],
            ['name' => 'Dialogue Moderator']
        );
        $editorRole = Role::firstOrCreate(
            ['slug' => 'content_editor'],
            ['name' => 'Content Editor']
        );

        User::factory()->create([
            'name' => 'Unique',
            'surname' => 'SearchTarget',
            'email' => 'unique-search-target@example.com',
        ]);

        $moderator = User::factory()->create(['surname' => 'Mod', 'email' => 'mod@example.com']);
        $moderator->roles()->attach($dialogueRole->id);

        $editor = User::factory()->create(['surname' => 'Ed', 'email' => 'editor@example.com']);
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
