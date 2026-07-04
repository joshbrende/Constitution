<?php

namespace App\Services\Setup;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SetupSystemChecker
{
    /**
     * @return list<array{key: string, label: string, status: string, message: string, critical: bool}>
     */
    public function run(): array
    {
        return [
            $this->checkPhpVersion(),
            $this->checkExtensions(),
            $this->checkAppKey(),
            $this->checkDatabase(),
            $this->checkMigrationsTable(),
            $this->checkSiteSettingsTable(),
            $this->checkStorageWritable(),
            $this->checkBootstrapCacheWritable(),
            $this->checkPublicStorageLink(),
            $this->checkGdExtension(),
            $this->checkMailConfiguration(),
            $this->checkOfficialPdf(),
            $this->checkQueueConnection(),
        ];
    }

    public function allCriticalPassed(array $checks): bool
    {
        foreach ($checks as $check) {
            if ($check['critical'] && $check['status'] !== 'pass') {
                return false;
            }
        }

        return true;
    }

    public function hasPendingMigrations(): bool
    {
        try {
            if (! Schema::hasTable('migrations')) {
                return true;
            }

            $migrator = app('migrator');
            $files = $migrator->getMigrationFiles(database_path('migrations'));

            return count(array_diff(array_keys($files), $migrator->getRepository()->getRan())) > 0;
        } catch (\Throwable) {
            return true;
        }
    }

    public function needsDatabaseProvision(): bool
    {
        return $this->hasPendingMigrations()
            || ! $this->canUseApplicationDatabase();
    }

    public function hasPublicStorageLink(): bool
    {
        $link = public_path('storage');

        return is_link($link) || (is_dir($link) && file_exists($link));
    }

    public function canUseApplicationDatabase(): bool
    {
        try {
            DB::select('select 1');

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * @return array{key: string, label: string, status: string, message: string, critical: bool}
     */
    private function checkPhpVersion(): array
    {
        $ok = PHP_VERSION_ID >= 80200;

        return [
            'key' => 'php',
            'label' => 'PHP version',
            'status' => $ok ? 'pass' : 'fail',
            'message' => $ok ? PHP_VERSION : 'PHP 8.2+ required (found '.PHP_VERSION.')',
            'critical' => true,
        ];
    }

    /**
     * @return array{key: string, label: string, status: string, message: string, critical: bool}
     */
    private function checkExtensions(): array
    {
        $required = ['pdo', 'mbstring', 'openssl', 'tokenizer', 'json', 'ctype', 'fileinfo'];
        $missing = array_values(array_filter($required, fn ($ext) => ! extension_loaded($ext)));
        $ok = $missing === [];

        return [
            'key' => 'extensions',
            'label' => 'PHP extensions',
            'status' => $ok ? 'pass' : 'fail',
            'message' => $ok ? 'Required extensions loaded' : 'Missing: '.implode(', ', $missing),
            'critical' => true,
        ];
    }

    /**
     * @return array{key: string, label: string, status: string, message: string, critical: bool}
     */
    private function checkAppKey(): array
    {
        $key = (string) config('app.key', '');
        $ok = $key !== '' && $key !== 'base64:';

        return [
            'key' => 'app_key',
            'label' => 'Application key',
            'status' => $ok ? 'pass' : 'fail',
            'message' => $ok ? 'APP_KEY is set' : 'Run: php artisan key:generate',
            'critical' => true,
        ];
    }

    /**
     * @return array{key: string, label: string, status: string, message: string, critical: bool}
     */
    private function checkDatabase(): array
    {
        $dbName = (string) config('database.connections.'.config('database.default').'.database', '');

        try {
            DB::select('select 1');

            return [
                'key' => 'database',
                'label' => 'Database connection',
                'status' => 'pass',
                'message' => 'Connected to '.($dbName !== '' ? $dbName : config('database.default')),
                'critical' => true,
            ];
        } catch (\Throwable $e) {
            if ($this->isUnknownDatabaseError($e)) {
                return [
                    'key' => 'database',
                    'label' => 'Database connection',
                    'status' => 'warn',
                    'message' => 'Database "'.$dbName.'" not found — the wizard will create it when you continue',
                    'critical' => false,
                ];
            }

            return [
                'key' => 'database',
                'label' => 'Database connection',
                'status' => 'fail',
                'message' => 'Cannot connect — check DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD in .env',
                'critical' => true,
            ];
        }
    }

    private function isUnknownDatabaseError(\Throwable $e): bool
    {
        $message = strtolower($e->getMessage());

        return str_contains($message, 'unknown database')
            || str_contains($message, '1049');
    }

    /**
     * @return array{key: string, label: string, status: string, message: string, critical: bool}
     */
    private function checkMigrationsTable(): array
    {
        try {
            $exists = Schema::hasTable('migrations');

            return [
                'key' => 'migrations',
                'label' => 'Database schema',
                'status' => $exists && ! $this->hasPendingMigrations() ? 'pass' : 'warn',
                'message' => ! $exists
                    ? 'Tables not created yet — the wizard will create them when you continue'
                    : ($this->hasPendingMigrations() ? 'Pending migrations — will run when you continue' : 'All tables installed'),
                'critical' => false,
            ];
        } catch (\Throwable) {
            return [
                'key' => 'migrations',
                'label' => 'Database schema',
                'status' => 'warn',
                'message' => 'Tables will be created when you continue (like WordPress install)',
                'critical' => false,
            ];
        }
    }

    /**
     * @return array{key: string, label: string, status: string, message: string, critical: bool}
     */
    private function checkSiteSettingsTable(): array
    {
        try {
            $exists = Schema::hasTable('site_settings');

            return [
                'key' => 'site_settings',
                'label' => 'Platform settings table',
                'status' => $exists ? 'pass' : 'warn',
                'message' => $exists ? 'Ready' : 'Created automatically with database setup',
                'critical' => false,
            ];
        } catch (\Throwable) {
            return [
                'key' => 'site_settings',
                'label' => 'Platform settings table',
                'status' => 'warn',
                'message' => 'Created automatically with database setup',
                'critical' => false,
            ];
        }
    }

    /**
     * @return array{key: string, label: string, status: string, message: string, critical: bool}
     */
    private function checkStorageWritable(): array
    {
        $paths = [storage_path(), storage_path('logs'), storage_path('framework/cache')];
        $bad = array_filter($paths, fn ($p) => ! is_dir($p) || ! is_writable($p));
        $ok = $bad === [];

        return [
            'key' => 'storage',
            'label' => 'Storage permissions',
            'status' => $ok ? 'pass' : 'fail',
            'message' => $ok ? 'storage/ is writable' : 'Not writable: '.Str::after((string) array_values($bad)[0], base_path().DIRECTORY_SEPARATOR),
            'critical' => true,
        ];
    }

    private function checkBootstrapCacheWritable(): array
    {
        return $this->checkCacheWritable();
    }

    /**
     * @return array{key: string, label: string, status: string, message: string, critical: bool}
     */
    private function checkPublicStorageLink(): array
    {
        $linked = $this->hasPublicStorageLink();

        return [
            'key' => 'storage_link',
            'label' => 'Public storage link',
            'status' => $linked ? 'pass' : 'warn',
            'message' => $linked
                ? 'public/storage is linked'
                : 'Run php artisan storage:link (wizard will attempt this during database setup)',
            'critical' => false,
        ];
    }

    /**
     * @return array{key: string, label: string, status: string, message: string, critical: bool}
     */
    private function checkGdExtension(): array
    {
        $ok = extension_loaded('gd');

        return [
            'key' => 'gd',
            'label' => 'GD extension (certificates)',
            'status' => $ok ? 'pass' : 'warn',
            'message' => $ok ? 'GD loaded for certificate PDF generation' : 'Enable ext-gd for academy certificate PDFs',
            'critical' => false,
        ];
    }

    /**
     * @return array{key: string, label: string, status: string, message: string, critical: bool}
     */
    private function checkMailConfiguration(): array
    {
        $mailer = (string) config('mail.default', 'log');
        $from = (string) config('mail.from.address', '');
        $configured = $mailer !== 'log' && $mailer !== 'array' && $from !== '' && $from !== 'noreply@example.org.zw';

        return [
            'key' => 'mail',
            'label' => 'Mail configuration',
            'status' => $configured ? 'pass' : 'warn',
            'message' => $configured
                ? 'Mail driver: '.$mailer
                : 'MAIL_* still uses log/placeholder — configure before sending invitations',
            'critical' => false,
        ];
    }

    /**
     * @return array{key: string, label: string, status: string, message: string, critical: bool}
     */
    private function checkOfficialPdf(): array
    {
        $path = storage_path('app/public/constitution-official/amendment3.pdf');
        $exists = is_file($path);

        return [
            'key' => 'official_pdf',
            'label' => 'Official amendment PDF',
            'status' => $exists ? 'pass' : 'warn',
            'message' => $exists
                ? 'amendment3.pdf found'
                : 'Optional: upload storage/app/public/constitution-official/amendment3.pdf before go-live',
            'critical' => false,
        ];
    }

    /**
     * @return array{key: string, label: string, status: string, message: string, critical: bool}
     */
    private function checkCacheWritable(): array
    {
        $path = base_path('bootstrap/cache');
        $ok = is_dir($path) && is_writable($path);

        return [
            'key' => 'bootstrap_cache',
            'label' => 'Bootstrap cache',
            'status' => $ok ? 'pass' : 'fail',
            'message' => $ok ? 'bootstrap/cache is writable' : 'bootstrap/cache must be writable',
            'critical' => true,
        ];
    }

    /**
     * @return array{key: string, label: string, status: string, message: string, critical: bool}
     */
    private function checkQueueConnection(): array
    {
        $driver = (string) config('queue.default', 'sync');
        if ($driver === 'redis') {
            try {
                Redis::connection()->ping();

                return [
                    'key' => 'queue',
                    'label' => 'Queue (Redis)',
                    'status' => 'pass',
                    'message' => 'Redis queue configured',
                    'critical' => false,
                ];
            } catch (\Throwable) {
                return [
                    'key' => 'queue',
                    'label' => 'Queue (Redis)',
                    'status' => 'warn',
                    'message' => 'QUEUE_CONNECTION=redis but Redis unreachable — mail/jobs may fail',
                    'critical' => false,
                ];
            }
        }

        return [
            'key' => 'queue',
            'label' => 'Queue driver',
            'status' => 'pass',
            'message' => 'Using '.$driver.' (set QUEUE_CONNECTION=redis in production)',
            'critical' => false,
        ];
    }
}
