<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use setasign\Fpdi\Tcpdf\Fpdi;

/**
 * Fills the certificate PDF template with course name, recipient name, date, and certificate number.
 * Template: public/certificate-template.pdf (A4 landscape). Falls back to certificate.pdf if missing.
 * Course name: centered above recipient. Name: Great Vibes, green. Date/cert number: bottom right.
 */
class CertificatePdfService
{
    private const TEMPLATE_PRIMARY = 'certificate-template.pdf';
    private const TEMPLATE_FALLBACK = 'certificate.pdf';

    private string $fontPath;
    private string $fontCachePath;

    /** A4 landscape: 297×210 mm. Positions tuned for certificate-template.pdf layout. */
    private const Y_TITLE = 58;        // Certificate title at top (e.g. Certificate of Competence)
    private const Y_COURSE = 150;       // Course name – centered, above recipient name
    private const Y_NAME = 107;        // Recipient name – below "Is hereby awarded to:"
    private const X_DATE_RIGHT = 360;  // X for date/cert (right side)
    private const Y_DATE = 210;        // Date – just under "Date" line
    private const Y_CERT_NO = 260;     // Certificate number – below date

    public function __construct()
    {
        $this->fontPath = storage_path('app/fonts/GreatVibes-Regular.ttf');
        $this->fontCachePath = storage_path('app/fonts/tcpdf');
    }

    public function templateExists(): bool
    {
        return is_file(public_path(self::TEMPLATE_PRIMARY)) && is_readable(public_path(self::TEMPLATE_PRIMARY))
            || is_file(public_path(self::TEMPLATE_FALLBACK)) && is_readable(public_path(self::TEMPLATE_FALLBACK));
    }

    private function getTemplatePath(): string
    {
        $primary = public_path(self::TEMPLATE_PRIMARY);
        if (is_file($primary) && is_readable($primary)) {
            return self::TEMPLATE_PRIMARY;
        }
        $fallback = public_path(self::TEMPLATE_FALLBACK);
        if (is_file($fallback) && is_readable($fallback)) {
            return self::TEMPLATE_FALLBACK;
        }
        throw new \RuntimeException('Certificate template not found. Add ' . self::TEMPLATE_PRIMARY . ' or ' . self::TEMPLATE_FALLBACK . ' to public/.');
    }

    public function canGenerate(): bool
    {
        return class_exists(Fpdi::class) && $this->templateExists();
    }

    /**
     * Draw title along a gentle asymmetric upward arc to match certificate style:
     * Left end lowest, rises to peak around middle/right, then descends so the right end
     * stays higher than the left (no full return to baseline). Letters stay upright.
     */
    private function drawArchedText(Fpdi $pdf, string $text, float $pageWidth, float $baseY, float $archPercent): void
    {
        $text = trim($text);
        if ($text === '') {
            return;
        }

        $totalWidth = $pdf->GetStringWidth($text);
        if ($totalWidth <= 0) {
            $pdf->SetXY(0, $baseY);
            $pdf->Cell($pageWidth, 14, $text, 0, 0, 'C');
            return;
        }

        $amplitude = $archPercent * $totalWidth;
        $centerX = $pageWidth / 2;
        $startX = $centerX - $totalWidth / 2;
        $len = mb_strlen($text);

        $offset = 0.0;
        for ($i = 0; $i < $len; $i++) {
            $char = mb_substr($text, $i, 1);
            $charWidth = $pdf->GetStringWidth($char);
            $charCenterX = $startX + $offset + $charWidth / 2;
            // t from 0 (left) to 1 (right). Peak near centre; right (Competence) down; left (Certificate) lifted a bit.
            $t = ($i + 0.5) / $len;
            $k = 0.12;
            $lift = 4 * $t * (1 - $t) * (1 - $k * $t) + $k * $t;
            // Bump left side up: add 0.35*(1-t)^2 so Certificate side rises, then renormalize so peak stays 1
            $lift = ($lift + 0.35 * (1 - $t) * (1 - $t)) / 1.0875;
            $y = $baseY - $amplitude * $lift;
            $pdf->Text($charCenterX - $charWidth / 2, $y, $char);
            $offset += $charWidth;
        }
    }

    private function registerGreatVibesFont(Fpdi $pdf): string
    {
        if (! is_file($this->fontPath) || ! is_readable($this->fontPath)) {
            return 'times';
        }
        if (! File::isDirectory($this->fontCachePath)) {
            File::makeDirectory($this->fontCachePath, 0755, true);
        }
        $kPath = defined('K_PATH_FONTS') ? K_PATH_FONTS : $this->fontCachePath . DIRECTORY_SEPARATOR;
        if (! is_writable($this->fontCachePath)) {
            return 'times';
        }
        try {
            $pathForTcpdf = str_replace('\\', '/', realpath($this->fontPath) ?: $this->fontPath);
            $name = \TCPDF_FONTS::addTTFfont($pathForTcpdf, 'TrueTypeUnicode', '', 32, $kPath);

            return is_string($name) && $name !== '' ? $name : 'times';
        } catch (\Throwable) {
            return 'times';
        }
    }

    /**
     * Generate PDF content as binary string.
     *
     * @param  object  $certificate  Must have user (name, surname), course (title), certificate_number, issued_at
     */
    public function generate(object $certificate): string
    {
        $template = $this->getTemplatePath();
        $path = public_path($template);

        $pdf = new Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);

        $pdf->setSourceFile($path);
        $tplId = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($tplId);
        $w = (float) ($size['width'] ?? 297);
        $h = (float) ($size['height'] ?? 210);
        $pdf->AddPage($size['orientation'] ?? 'L', [$w, $h]);
        $pdf->useImportedPage($tplId, 0, 0, $w, $h);

        $certificateTitle = isset($certificate->course) ? ($certificate->course->certificate_title ?? 'Certificate of Completion') : 'Certificate of Completion';
        $courseTitle = isset($certificate->course) ? ($certificate->course->title ?? 'Membership') : 'Membership';
        $name = trim(($certificate->user->name ?? '') . ' ' . ($certificate->user->surname ?? ''));
        if ($name === '') {
            $name = 'Member';
        }
        $dateStr = $certificate->issued_at
            ? $certificate->issued_at->format('d F Y')
            : now()->format('d F Y');
        $certNumber = $certificate->certificate_number ?? '';
        $publicId = $certificate->public_id ?? null;
        $verificationCode = $certificate->verification_code ?? null;
        $verificationToken = null;
        if (is_object($certificate) && method_exists($certificate, 'signedVerificationToken')) {
            $verificationToken = $certificate->signedVerificationToken();
        } elseif (isset($certificate->verification_token)) {
            $verificationToken = (string) $certificate->verification_token;
        }

        $scriptFont = $this->registerGreatVibesFont($pdf);

        // 0. Certificate title at top – Great Vibes 72pt, green #026302, arched by 20%
        $pdf->SetFont($scriptFont, '', 72);
        $pdf->SetTextColor(2, 99, 2); // #026302 – title only
        $this->drawArchedText($pdf, $certificateTitle, $w, self::Y_TITLE, 0.10);

        // 1. Course name – Great Vibes, centered above recipient (#c10102)
        $pdf->SetFont($scriptFont, '', 36);
        $pdf->SetTextColor(193, 1, 2); // #c10102
        $pdf->SetXY(0, self::Y_COURSE);
        $pdf->Cell($w, 10, $courseTitle, 0, 0, 'C');

        // 2. Recipient name – Great Vibes, centered
        $pdf->SetFont($scriptFont, '', 34);
        $pdf->SetTextColor(193, 1, 2); // #c10102
        $pdf->SetXY(0, self::Y_NAME);
        $pdf->Cell($w, 14, $name, 0, 0, 'C');

        // 3. Date – bottom right
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(60, 60, 60);
        $pdf->Text(self::X_DATE_RIGHT, self::Y_DATE, $dateStr);

        // 4. Certificate number – below date
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->Text(self::X_DATE_RIGHT, self::Y_CERT_NO, 'Certificate No. ' . $certNumber);

        // 5. QR code – verification
        if ($verificationCode) {
            $verifyUrl = config('app.url') . '/verify-certificate?number=' . urlencode($certNumber)
                . '&code=' . urlencode($verificationCode);
            if ($publicId) {
                $verifyUrl .= '&id=' . urlencode($publicId);
            }
            if ($verificationToken) {
                $verifyUrl .= '&token=' . urlencode($verificationToken);
            }
            $qrX = 370;
            $qrY = 235;
            $pdf->write2DBarcode($verifyUrl, 'QRCODE,M', $qrX, $qrY, 22, 22);
        }

        return $pdf->Output('', 'S');
    }
}
