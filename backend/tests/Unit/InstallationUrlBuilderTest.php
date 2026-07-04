<?php

namespace Tests\Unit;

use App\Services\Setup\InstallationUrlBuilder;
use PHPUnit\Framework\TestCase;

class InstallationUrlBuilderTest extends TestCase
{
    public function test_builds_root_installation_url(): void
    {
        $this->assertSame(
            'https://ttm-group.co.za',
            InstallationUrlBuilder::build('https', 'ttm-group.co.za', '')
        );
    }

    public function test_builds_subdirectory_installation_url(): void
    {
        $this->assertSame(
            'https://ttm-group.co.za/constitution',
            InstallationUrlBuilder::build('https', 'ttm-group.co.za', 'constitution')
        );
    }

    public function test_parses_saved_url(): void
    {
        $parsed = InstallationUrlBuilder::parse('https://example.org/app');

        $this->assertSame('https', $parsed['protocol']);
        $this->assertSame('example.org', $parsed['domain']);
        $this->assertSame('app', $parsed['directory']);
    }
}
