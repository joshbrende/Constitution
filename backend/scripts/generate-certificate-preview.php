<?php

/**
 * One-off preview: sample membership certificate PDF.
 * Usage: php scripts/generate-certificate-preview.php [output-path]
 */

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use App\Services\CertificatePdfService;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$output = $argv[1] ?? public_path('previews/certificate-preview.pdf');
$dir = dirname($output);
if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
    fwrite(STDERR, "Cannot create directory: {$dir}\n");
    exit(1);
}

$user = new User([
    'name' => 'Tendai',
    'surname' => 'Moyo',
]);

$course = new Course([
    'title' => 'ZANU PF Constitution & Membership Course',
    'code' => 'MEMBERSHIP',
    'certificate_title' => 'Certificate of Competence',
]);

$year = date('Y');
$prefix = (string) config('certificates.certificate_number_prefix', 'ZPF-MEM');

$certificate = new Certificate([
    'public_id' => '00000000-0000-4000-8000-000000000002',
    'certificate_number' => sprintf('%s-%s-PREVIEW01', $prefix, $year),
    'verification_code' => 'PREVIEW1',
    'issued_at' => now(),
]);
$certificate->setRelation('user', $user);
$certificate->setRelation('course', $course);

/** @var CertificatePdfService $service */
$service = app(CertificatePdfService::class);

if (! $service->canGenerate()) {
    fwrite(STDERR, "Certificate template or TCPDF not available — cannot generate PDF.\n");
    exit(1);
}

$pdf = $service->generate($certificate);
file_put_contents($output, $pdf);

echo $output.PHP_EOL;
