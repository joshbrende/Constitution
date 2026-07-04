<?php

namespace App\Services\Setup;

class SetupProductionChecklist
{
    public function __construct(
        protected SetupSystemChecker $checker
    ) {}

    /**
     * Post-install production tasks for the finish step.
     *
     * @param  array<string, mixed>|null  $defaults
     * @return list<array{key: string, label: string, status: string, message: string, command: ?string, env_block: ?string}>
     */
    public function items(?array $defaults = null): array
    {
        $defaults ??= [];
        $appUrl = rtrim(trim((string) ($defaults['public_site_url'] ?? config('app.url', ''))), '/');
        $apiBase = $appUrl !== '' ? $appUrl.'/api/v1' : 'https://www.zanupf.org.zw/api/v1';

        $items = [
            $this->envBlockItem($defaults),
            $this->storageLinkItem(),
            $this->mailItem(),
            $this->corsItem($appUrl),
            $this->queueItem(),
            $this->cronItem(),
            $this->mobileApiItem($apiBase),
            $this->certificateReadinessItem(),
            $this->officialPdfItem(),
            $this->inviteAdminsItem(),
        ];

        $subdirectory = $this->installationSubdirectory($appUrl);
        if ($subdirectory !== null) {
            array_splice($items, 1, 0, [$this->subdirectoryItem($appUrl, $subdirectory)]);
        }

        return $items;
    }

    /**
     * @param  array<string, mixed>  $defaults
     * @return array{key: string, label: string, status: string, message: string, command: ?string, env_block: ?string}
     */
    private function envBlockItem(array $defaults): array
    {
        $curEnv = (string) config('app.env', '');
        $curDebug = (bool) config('app.debug', false);
        $curUrl = (string) config('app.url', '');
        $recommendedUrl = trim((string) ($defaults['public_site_url'] ?? ''));

        $looksGood = $curEnv === 'production'
            && ! $curDebug
            && $recommendedUrl !== ''
            && $curUrl === $recommendedUrl;

        return [
            'key' => 'env',
            'label' => 'Server environment (.env)',
            'status' => $looksGood ? 'pass' : 'warn',
            'message' => $looksGood
                ? 'APP_ENV, APP_DEBUG, and APP_URL match wizard recommendations.'
                : 'Set APP_NAME, APP_URL, APP_ENV=production, and APP_DEBUG=false on the server, then clear config cache.',
            'command' => 'php artisan config:clear',
            'env_block' => null,
        ];
    }

    /**
     * @return array{key: string, label: string, status: string, message: string, command: ?string, env_block: ?string}
     */
    private function storageLinkItem(): array
    {
        $linked = $this->checker->hasPublicStorageLink();

        return [
            'key' => 'storage_link',
            'label' => 'Public storage symlink',
            'status' => $linked ? 'pass' : 'warn',
            'message' => $linked
                ? 'public/storage is linked — uploads and PDFs can be served.'
                : 'Run once so /storage URLs work for uploads, banners, and official documents.',
            'command' => 'php artisan storage:link',
            'env_block' => null,
        ];
    }

    /**
     * @return array{key: string, label: string, status: string, message: string, command: ?string, env_block: ?string}
     */
    private function mailItem(): array
    {
        $mailer = (string) config('mail.default', 'log');
        $from = (string) config('mail.from.address', '');
        $configured = $mailer !== 'log' && $mailer !== 'array' && $from !== '' && $from !== 'noreply@example.org.zw';

        return [
            'key' => 'mail',
            'label' => 'Outbound mail (invitations & notifications)',
            'status' => $configured ? 'pass' : 'warn',
            'message' => $configured
                ? 'Mail is configured ('.$mailer.').'
                : 'Configure SMTP or your provider so admin invitations and academy notifications can be sent.',
            'command' => null,
            'env_block' => "MAIL_MAILER=smtp\nMAIL_HOST=your-smtp-host\nMAIL_PORT=587\nMAIL_USERNAME=\nMAIL_PASSWORD=\nMAIL_FROM_ADDRESS=support@zanupf.org.zw\nMAIL_FROM_NAME=\"ZANUPF\"",
        ];
    }

    /**
     * @return array{key: string, label: string, status: string, message: string, command: ?string, env_block: ?string}
     */
    private function corsItem(string $appUrl): array
    {
        $raw = (string) ($_ENV['CORS_ALLOWED_ORIGINS'] ?? $_SERVER['CORS_ALLOWED_ORIGINS'] ?? '');
        $configured = trim($raw) !== '';

        return [
            'key' => 'cors',
            'label' => 'CORS (browser clients)',
            'status' => $configured ? 'pass' : 'warn',
            'message' => $configured
                ? 'CORS_ALLOWED_ORIGINS is set for browser-based clients.'
                : 'Restrict cross-origin API access to your production web app domain(s).',
            'command' => null,
            'env_block' => 'CORS_ALLOWED_ORIGINS="https://your-app-domain.example"',
        ];
    }

    /**
     * @return array{key: string, label: string, status: string, message: string, command: ?string, env_block: ?string}
     */
    private function queueItem(): array
    {
        $driver = (string) config('queue.default', 'sync');
        $checks = $this->checker->run();
        $queueCheck = collect($checks)->firstWhere('key', 'queue');
        $status = ($queueCheck['status'] ?? 'pass') === 'pass' && $driver !== 'sync' ? 'pass' : 'warn';

        return [
            'key' => 'queue',
            'label' => 'Queue worker',
            'status' => $status,
            'message' => $driver === 'sync'
                ? 'Using sync driver — set QUEUE_CONNECTION=redis (or database) and run a queue worker in production.'
                : (string) ($queueCheck['message'] ?? 'Queue driver: '.$driver),
            'command' => 'php artisan queue:work --tries=3',
            'env_block' => "QUEUE_CONNECTION=redis\nREDIS_HOST=127.0.0.1\nREDIS_PASSWORD=null\nREDIS_PORT=6379",
        ];
    }

    /**
     * @return array{key: string, label: string, status: string, message: string, command: ?string, env_block: ?string}
     */
    private function cronItem(): array
    {
        return [
            'key' => 'cron',
            'label' => 'Scheduler (cron)',
            'status' => 'info',
            'message' => 'Add a cron entry so scheduled tasks (cleanup, notifications) run every minute.',
            'command' => '* * * * * cd /path/to/backend && php artisan schedule:run >> /dev/null 2>&1',
            'env_block' => null,
        ];
    }

    /**
     * @return array{key: string, label: string, status: string, message: string, command: ?string, env_block: ?string}
     */
    private function mobileApiItem(string $apiBase): array
    {
        return [
            'key' => 'mobile_api',
            'label' => 'Mobile app API URL',
            'status' => 'info',
            'message' => 'Point the Expo/mobile build at your production API (include /api/v1).',
            'command' => null,
            'env_block' => 'EXPO_PUBLIC_API_BASE_URL="'.$apiBase.'"',
        ];
    }

    /**
     * @return array{key: string, label: string, status: string, message: string, command: ?string, env_block: ?string}
     */
    private function certificateReadinessItem(): array
    {
        $gd = extension_loaded('gd');
        $fontDir = storage_path('app/fonts/tcpdf');
        $fontsWritable = is_dir($fontDir) ? is_writable($fontDir) : is_writable(storage_path('app'));

        $ok = $gd && $fontsWritable;

        return [
            'key' => 'certificates',
            'label' => 'Academy certificate PDFs',
            'status' => $ok ? 'pass' : 'warn',
            'message' => $ok
                ? 'GD extension and font cache directory are ready for TCPDF.'
                : (!$gd ? 'Install/enable the PHP GD extension for certificate PDF generation.' : 'Ensure storage/app is writable for TCPDF fonts.'),
            'command' => null,
            'env_block' => null,
        ];
    }

    /**
     * @return array{key: string, label: string, status: string, message: string, command: ?string, env_block: ?string}
     */
    private function officialPdfItem(): array
    {
        $path = storage_path('app/public/constitution-official/amendment3.pdf');
        $exists = is_file($path);

        return [
            'key' => 'official_pdf',
            'label' => 'Official amendment PDF',
            'status' => $exists ? 'pass' : 'warn',
            'message' => $exists
                ? 'amendment3.pdf is present in storage.'
                : 'Upload the official PDF to storage/app/public/constitution-official/amendment3.pdf (after storage:link).',
            'command' => null,
            'env_block' => null,
        ];
    }

    /**
     * @return array{key: string, label: string, status: string, message: string, command: ?string, env_block: ?string}
     */
    private function inviteAdminsItem(): array
    {
        return [
            'key' => 'invite_admins',
            'label' => 'Invite additional administrators',
            'status' => 'info',
            'message' => 'After completing installation, invite content editors, approvers, and provincial admins from Admin → Users.',
            'command' => null,
            'env_block' => null,
        ];
    }

    /**
     * @return array{key: string, label: string, status: string, message: string, command: ?string, env_block: ?string}
     */
    private function subdirectoryItem(string $appUrl, string $directory): array
    {
        return [
            'key' => 'subdirectory',
            'label' => 'Subdirectory installation',
            'status' => 'info',
            'message' => 'Installed in /'.$directory.' — ensure the web server document root or rewrite rules serve Laravel from this path. Consider SESSION_DOMAIN and SANCTUM_STATEFUL_DOMAINS if using cookie auth.',
            'command' => null,
            'env_block' => 'SESSION_DOMAIN=zanupf.org.zw',
        ];
    }

    private function installationSubdirectory(string $appUrl): ?string
    {
        if ($appUrl === '') {
            return null;
        }

        $path = trim((string) parse_url($appUrl, PHP_URL_PATH), '/');

        return $path !== '' ? $path : null;
    }
}
