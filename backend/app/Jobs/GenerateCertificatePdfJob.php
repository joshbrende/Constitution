<?php

namespace App\Jobs;

use App\Models\Certificate;
use App\Services\CertificatePdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateCertificatePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Certificate $certificate
    ) {}

    public function handle(CertificatePdfService $pdfService): void
    {
        $certificate = $this->certificate->fresh();
        if (! $certificate || $certificate->pdf_status === 'ready') {
            return;
        }

        $certificate->update(['pdf_status' => 'generating']);

        if (! $pdfService->canGenerate()) {
            $certificate->update(['pdf_status' => 'pending']);
            return;
        }

        try {
            $certificate->load(['user', 'course']);
            $pdfContent = $pdfService->generate($certificate);

            $dir = 'certificates/' . $certificate->id;
            $filename = 'certificate-' . $certificate->certificate_number . '.pdf';
            $path = $dir . '/' . $filename;

            Storage::put($path, $pdfContent);
            $certificate->update([
                'pdf_status' => 'ready',
                'pdf_path' => $path,
            ]);
        } catch (\Throwable $e) {
            $certificate->update(['pdf_status' => 'pending']);
            throw $e;
        }
    }
}
