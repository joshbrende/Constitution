<?php

namespace Tests\Unit;

use App\Services\Setup\SetupProductionChecklist;
use App\Services\Setup\SetupSystemChecker;
use Tests\TestCase;

class SetupProductionChecklistTest extends TestCase
{
    public function test_includes_mobile_api_url_from_defaults(): void
    {
        $checklist = new SetupProductionChecklist(new SetupSystemChecker);
        $items = collect($checklist->items([
            'public_site_url' => 'https://academy.example.org.zw',
        ]));

        $mobile = $items->firstWhere('key', 'mobile_api');
        $this->assertNotNull($mobile);
        $this->assertStringContainsString('https://academy.example.org.zw/api/v1', (string) $mobile['env_block']);
    }

    public function test_adds_subdirectory_item_when_url_has_path(): void
    {
        $checklist = new SetupProductionChecklist(new SetupSystemChecker);
        $items = collect($checklist->items([
            'public_site_url' => 'https://academy.example.org.zw/constitution',
        ]));

        $this->assertNotNull($items->firstWhere('key', 'subdirectory'));
    }
}
