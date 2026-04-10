<?php

namespace App\Services;

use App\Models\CertificateSignature;
use setasign\Fpdi\Tcpdf\Fpdi;
use Illuminate\Support\Facades\File;

/**
 * Generates certificate PDF using the background PDF template. Only inserts:
 * delegate name, certificate number, date, and Facilitator / Board of Faculty / Supervisor signatures.
 * Static text (title, "Is hereby awarded to", course title, labels) is on the template.
 */
final class CertificatePdfService
{
    /** Locked positions for certificate_background_performance-v1.pdf (A4 landscape). Do not change. */
    private const SIG_IMAGE_HEIGHT = 18;
    private const SIG_IMAGE_WIDTH = 35;
    private const Y_DATE = 152;
    private const Y_SIG_TOP = 150;       // Facilitator (horizontal with Date)
    private const Y_SIG_BOTTOM = 168;    // Board of Faculty, Supervisor
    private const X_LEFT = 68;           // Facilitator, Board of Faculty
    private const DATE_CELL_WIDTH = 60;
    private const DATE_COL_OFFSET = 115; // Date column: x = pageWidth - this
    private const SUPERVISOR_X_OFFSET = 90; // Supervisor directly under Date: x = pageWidth - this

    private string $fontPath;

    private string $fontCachePath;

    /** Background PDF path: course's certificate template if set and valid, else performance template, else legacy. */
    private function getBackgroundPath(object $certificate): string
    {
        $course = $certificate->course ?? null;
        if ($course && $course->certificate_template_id) {
            $template = $course->relationLoaded('certificateTemplate')
                ? $course->certificateTemplate
                : \App\Models\CertificateTemplate::find($course->certificate_template_id);
            if ($template && $template->fileExists()) {
                return $template->getFullPathAttribute();
            }
        }
        $performance = public_path('asset/certificate_background_performance-v1.pdf');
        $legacy = public_path('asset/certificate_background.pdf');
        if (is_file($performance) && is_readable($performance)) {
            return $performance;
        }
        return $legacy;
    }

    public function __construct()
    {
        $this->fontPath = storage_path('app/fonts/GreatVibes-Regular.ttf');
        $this->fontCachePath = storage_path('app/fonts/tcpdf');
    }

    public function backgroundExists(?object $certificate = null): bool
    {
        $path = $certificate ? $this->getBackgroundPath($certificate) : $this->getDefaultBackgroundPath();
        return is_file($path) && is_readable($path);
    }

    /** Default background path when no course template is set. */
    private function getDefaultBackgroundPath(): string
    {
        $performance = public_path('asset/certificate_background_performance-v1.pdf');
        $legacy = public_path('asset/certificate_background.pdf');
        if (is_file($performance) && is_readable($performance)) {
            return $performance;
        }
        return $legacy;
    }

    public function supportsBackgroundPdf(?object $certificate = null): bool
    {
        return class_exists(Fpdi::class) && $this->backgroundExists($certificate);
    }

    /**
     * Generate PDF content as string. Certificate must have user and course loaded (Certificate model or stdClass for preview).
     */
    public function generate(object $certificate): string
    {
        // Ensure TCPDF font cache dir exists (K_PATH_FONTS set in AppServiceProvider)
        if (!File::isDirectory($this->fontCachePath)) {
            File::makeDirectory($this->fontCachePath, 0755, true);
        }

        $pdf = new Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);

        $backgroundPath = $this->getBackgroundPath($certificate);
        $pdf->setSourceFile($backgroundPath);
        $tplId = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($tplId);
        $pdf->AddPage($size['orientation'] ?? 'L', [$size['width'] ?? 297, $size['height'] ?? 210]);
        $pdf->useImportedPage($tplId, 0, 0, $size['width'] ?? 297, $size['height'] ?? 210);

        $w = (float) ($size['width'] ?? 297);
        $h = (float) ($size['height'] ?? 210);

        $name = trim(($certificate->user->name ?? '') . ' ' . ($certificate->user->surname ?? ''));
        if ($name === '') {
            $name = 'Participant';
        }
        $certNumber = $certificate->certificate_number ?? '';
        $dateStr = $certificate->issued_at ? $certificate->issued_at->format('d F Y') : date('d F Y');

        $scriptFont = $this->registerScriptFont($pdf);

        // 1. Delegate name (centered, below "Is hereby awarded to" on the template)
        $pdf->SetFont($scriptFont, '', 34);
        $pdf->SetTextColor(80, 50, 40);
        $pdf->SetXY(0, 84);
        $pdf->Cell($w, 14, $name, 0, 0, 'C');

        // 2. Certificate number (below main content, centered, typewriter font)
        $pdf->SetFont('courier', '', 9);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->SetXY(0, 98);
        $pdf->Cell($w, 6, 'Certificate no. ' . $certNumber, 0, 0, 'C');

        // Locked signature/date positions (see class constants)
        $xDateCol = $w - self::DATE_COL_OFFSET;
        $xSupervisor = $w - self::SUPERVISOR_X_OFFSET;

        // 3. Date (right column)
        $pdf->SetFont('courier', '', 10);
        $pdf->SetTextColor(60, 60, 60);
        $pdf->SetXY($xDateCol, self::Y_DATE);
        $pdf->Cell(self::DATE_CELL_WIDTH, 6, $dateStr, 0, 0, 'R');

        $course = $certificate->course ?? null;
        $instructorId = $course->instructor_id ?? null;

        $facPath = CertificateSignature::getFacilitatorPath($instructorId);
        $boardPath = CertificateSignature::getBoardOfFacultyPath();
        $superPath = CertificateSignature::getSupervisorPath();

        // Facilitator Signature (left column, upper line – locked position)
        if ($facPath && is_file($facPath)) {
            $pdf->Image($facPath, self::X_LEFT, self::Y_SIG_TOP, self::SIG_IMAGE_WIDTH, self::SIG_IMAGE_HEIGHT, '', '', '', false, 300);
        }

        // Board of Faculty (left column, lower line – locked position)
        if ($boardPath && is_file($boardPath)) {
            $pdf->Image($boardPath, self::X_LEFT, self::Y_SIG_BOTTOM, self::SIG_IMAGE_WIDTH, self::SIG_IMAGE_HEIGHT, '', '', '', false, 300);
        }

        // Supervisor Signature (directly under Date – locked position)
        if ($superPath && is_file($superPath)) {
            $pdf->Image($superPath, $xSupervisor, self::Y_SIG_BOTTOM, self::SIG_IMAGE_WIDTH, self::SIG_IMAGE_HEIGHT, '', '', '', false, 300);
        }

        return $pdf->Output('', 'S');
    }

    /**
     * Register Great Vibes TTF if present and TCPDF font dir is writable; else use times (serif).
     */
    private function registerScriptFont(Fpdi $pdf): string
    {
        $fontFile = is_file($this->fontPath) ? $this->fontPath : null;
        if (!$fontFile || !is_readable($fontFile)) {
            return 'times';
        }
        $fontFile = realpath($fontFile) ?: $fontFile;
        $kPathFonts = defined('K_PATH_FONTS') ? rtrim(K_PATH_FONTS, " \t\n\r\0\x0B/\\") . '/' : null;
        $fontDir = $kPathFonts ? rtrim($kPathFonts, '/') : '';
        if (!$kPathFonts || !$fontDir || !is_writable($fontDir)) {
            return 'times';
        }
        try {
            // Use forward slashes for TCPDF (avoids issues on Windows). TCPDF_FONTS::addTTFfont is static (no instance method).
            $fontPathForTcpdf = str_replace('\\', '/', $fontFile);
            $name = \TCPDF_FONTS::addTTFfont($fontPathForTcpdf, 'TrueTypeUnicode', '', 32, $kPathFonts);
            return is_string($name) && $name !== '' ? $name : 'times';
        } catch (\Throwable $e) {
            return 'times';
        }
    }
}

