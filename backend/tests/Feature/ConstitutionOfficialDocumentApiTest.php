<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ConstitutionOfficialDocumentApiTest extends TestCase
{
    private ?string $isolatedPublicDiskRoot = null;

    protected function tearDown(): void
    {
        if ($this->isolatedPublicDiskRoot !== null && is_dir($this->isolatedPublicDiskRoot)) {
            File::deleteDirectory($this->isolatedPublicDiskRoot);
            $this->isolatedPublicDiskRoot = null;
        }

        parent::tearDown();
    }

    /**
     * Avoid Storage::fake('public') here: its root lives under storage/framework/testing/disks,
     * which is often not writable from Sail when the project is bind-mounted from Windows.
     */
    private function useIsolatedPublicDisk(): void
    {
        $root = sys_get_temp_dir().DIRECTORY_SEPARATOR.'constitution-test-public-'.uniqid('', true);
        File::makeDirectory($root, 0777, true, true);
        $this->isolatedPublicDiskRoot = $root;

        config(['filesystems.disks.public.root' => $root]);
        Storage::purge('public');
    }

    public function test_amendment_official_endpoint_returns_json_shape(): void
    {
        $this->useIsolatedPublicDisk();

        $response = $this->getJson('/api/v1/constitution/official/amendment3');

        $response->assertOk()
            ->assertJsonStructure(['available', 'title'])
            ->assertJson(['available' => false]);
    }

    public function test_amendment_official_endpoint_returns_url_when_file_present(): void
    {
        $this->useIsolatedPublicDisk();
        Storage::disk('public')->put(
            config('constitution.amendment3_official_pdf_path'),
            '%PDF-1.4 fake'
        );

        $response = $this->getJson('/api/v1/constitution/official/amendment3');

        $response->assertOk()
            ->assertJson([
                'available' => true,
                'title' => config('constitution.amendment3_chapter_title'),
            ]);

        $url = $response->json('url');
        $this->assertIsString($url);
        $this->assertStringContainsString('/storage/', $url);
    }
}
