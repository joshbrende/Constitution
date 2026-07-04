<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verify_page_loads_without_credentials(): void
    {
        $this->get(route('certificate.verify'))
            ->assertOk();
    }

    public function test_verify_rejects_unknown_certificate(): void
    {
        $response = $this->get(route('certificate.verify', [
            'number' => 'ZPF-MEM-2099-99999',
            'code' => 'NOTVALID',
        ]));

        $response->assertOk()
            ->assertSee('not found', false);
    }
}
