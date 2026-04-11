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
        $primary = public_path(self::TEMPLATE_PRIMARY);
        $fallback = public_path(self::TEMPLATE_FALLBACK);

        return (is_file($primary) && is_readable($primary))
            || (is_file($fallback) && is_readable($fallback));
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

        $fields = $this->resolveCertificateFields($certificate);
        $this->drawCertificateContent($pdf, $w, $fields);

        return $pdf->Output('', 'S');
    }

    /**
     * @return array{
     *     certificateTitle: string,
     *     courseTitle: string,
     *     recipientName: string,
     *     dateStr: string,
     *     certNumber: string,
     *     verifyUrl: ?string
     * }
     */
    private function resolveCertificateFields(object $certificate): array
    {
        $certificateTitle = isset($certificate->course)
            ? ($certificate->course->certificate_title ?? 'Certificate of Completion')
            : 'Certificate of Completion';
        $courseTitle = isset($certificate->course)
            ? ($certificate->course->title ?? 'Membership')
            : 'Membership';

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
        $verificationToken = $this->resolveVerificationToken($certificate);

        $verifyUrl = $this->buildCertificateVerifyUrl($certNumber, $verificationCode, $publicId, $verificationToken);

        return [
            'certificateTitle' => $certificateTitle,
            'courseTitle' => $courseTitle,
            'recipientName' => $name,
            'dateStr' => $dateStr,
            'certNumber' => $certNumber,
            'verifyUrl' => $verifyUrl,
        ];
    }

    private function resolveVerificationToken(object $certificate): ?string
    {
        if (method_exists($certificate, 'signedVerificationToken')) {
            return $certificate->signedVerificationToken();
        }
        if (isset($certificate->verification_token)) {
            return (string) $certificate->verification_token;
        }

        return null;
    }

    private function buildCertificateVerifyUrl(
        string $certNumber,
        mixed $verificationCode,
        mixed $publicId,
        ?string $verificationToken
    ): ?string {
        if (! $verificationCode) {
            return null;
        }

        $verifyUrl = config('app.url') . '/verify-certificate?number=' . urlencode($certNumber)
            . '&code=' . urlencode((string) $verificationCode);
        if ($publicId) {
            $verifyUrl .= '&id=' . urlencode((string) $publicId);
        }
        if ($verificationToken) {
            $verifyUrl .= '&token=' . urlencode($verificationToken);
        }

        return $verifyUrl;
    }

    /**
     * @param  array{
     *     certificateTitle: string,
     *     courseTitle: string,
     *     recipientName: string,
     *     dateStr: string,
     *     certNumber: string,
     *     verifyUrl: ?string
     * }  $fields
     */
    private function drawCertificateContent(Fpdi $pdf, float $w, array $fields): void
    {
        $scriptFont = $this->registerGreatVibesFont($pdf);

        $pdf->SetFont($scriptFont, '', 72);
        $pdf->SetTextColor(2, 99, 2);
        $this->drawArchedText($pdf, $fields['certificateTitle'], $w, self::Y_TITLE, 0.10);

        $pdf->SetFont($scriptFont, '', 36);
        $pdf->SetTextColor(193, 1, 2);
        $pdf->SetXY(0, self::Y_COURSE);
        $pdf->Cell($w, 10, $fields['courseTitle'], 0, 0, 'C');

        $pdf->SetFont($scriptFont, '', 34);
        $pdf->SetTextColor(193, 1, 2);
        $pdf->SetXY(0, self::Y_NAME);
        $pdf->Cell($w, 14, $fields['recipientName'], 0, 0, 'C');

        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(60, 60, 60);
        $pdf->Text(self::X_DATE_RIGHT, self::Y_DATE, $fields['dateStr']);

        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->Text(self::X_DATE_RIGHT, self::Y_CERT_NO, 'Certificate No. ' . $fields['certNumber']);

        if ($fields['verifyUrl'] !== null) {
            $pdf->write2DBarcode($fields['verifyUrl'], 'QRCODE,M', 370, 235, 22, 22);
        }
    }
}
