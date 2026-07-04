<?php

namespace Tests\Unit;

use App\Services\CertificatePdfService;
use Tests\TestCase;

class CertificatePdfServiceTest extends TestCase
{
    public function test_formats_leading_zanu_pf_to_title_case_in_same_line(): void
    {
        $formatted = CertificatePdfService::formatCourseTitleForCertificate(
            'ZANU PF Constitution & Membership Course'
        );

        $this->assertSame('Zanu Pf Constitution & Membership Course', $formatted);
    }

    public function test_formats_embedded_zanu_pf_anywhere_in_title(): void
    {
        $formatted = CertificatePdfService::formatCourseTitleForCertificate(
            'Foundational ZANU PF Studies'
        );

        $this->assertSame('Foundational Zanu Pf Studies', $formatted);
    }
}
