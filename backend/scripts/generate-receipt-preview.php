<?php

/**
 * One-off preview: sample payment receipt after course exam pass.
 * Usage: php scripts/generate-receipt-preview.php [output-path]
 */

use App\Enums\CertificateApplicationStatus;
use App\Models\CertificateApplication;
use App\Models\Course;
use App\Models\Province;
use App\Models\User;
use App\Services\ReceiptPdfService;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$output = $argv[1] ?? public_path('previews/payment-receipt-preview.pdf');
$dir = dirname($output);
if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
    fwrite(STDERR, "Cannot create directory: {$dir}\n");
    exit(1);
}

$user = new User([
    'name' => 'Tendai',
    'surname' => 'Moyo',
    'national_id' => '08-2047823Q29',
]);
$user->setRelation('province', new Province(['name' => 'Harare', 'code' => 'harare']));

$course = new Course([
    'title' => 'ZANU PF Constitution & Membership Course',
    'code' => 'MEMBERSHIP',
]);

$receiptNumbers = app(\App\Services\ReceiptNumberService::class);
$numbers = $receiptNumbers->generateForUser($user);

$application = new CertificateApplication([
    'public_id' => '00000000-0000-4000-8000-000000000001',
    'receipt_number' => $numbers['receipt_number'],
    'payment_reference_code' => $numbers['payment_reference_code'],
    'fee_amount' => 25.00,
    'fee_currency' => 'USD',
    'status' => CertificateApplicationStatus::PaymentPending,
    'exam_passed_at' => now(),
]);
$application->setRelation('user', $user);
$application->setRelation('course', $course);

/** @var ReceiptPdfService $service */
$service = app(ReceiptPdfService::class);

if (! $service->canGenerate()) {
    fwrite(STDERR, "TCPDF not available — cannot generate PDF.\n");
    exit(1);
}

$pdf = $service->generate($application);
file_put_contents($output, $pdf);

echo $output.PHP_EOL;
