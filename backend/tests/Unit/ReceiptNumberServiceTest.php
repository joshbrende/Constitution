<?php

namespace Tests\Unit;

use App\Models\Province;
use App\Models\User;
use App\Services\ReceiptNumberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceiptNumberServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_generates_structured_receipt_and_payment_reference(): void
    {
        $harare = Province::query()->where('code', 'harare')->firstOrFail();
        $user = User::factory()->create(['province_id' => $harare->id]);

        $numbers = app(ReceiptNumberService::class)->generateForUser($user);

        $this->assertMatchesRegularExpression('/^ZPF-REC-\d{4}-HAR-\d{6}$/', $numbers['receipt_number']);
        $this->assertMatchesRegularExpression('/^HAR-\d{6}-[0-9A-Z]{2}$/', $numbers['payment_reference_code']);
    }

    public function test_uses_nat_when_user_has_no_province(): void
    {
        $user = User::factory()->create(['province_id' => null]);

        $numbers = app(ReceiptNumberService::class)->generateForUser($user);

        $this->assertStringContainsString('-NAT-', $numbers['receipt_number']);
    }

    public function test_checksum_is_deterministic(): void
    {
        $service = app(ReceiptNumberService::class);
        $a = $service->checksum2('ZPF-REC-2026-HAR-000042');
        $b = $service->checksum2('ZPF-REC-2026-HAR-000042');
        $this->assertSame($a, $b);
        $this->assertSame(2, strlen($a));
    }
}
