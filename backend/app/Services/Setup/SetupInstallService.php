<?php

namespace App\Services\Setup;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\AmendmentBill2026MetaSyncSeeder;
use Database\Seeders\AmendmentBill2026Seeder;
use Database\Seeders\ConstitutionSeeder;
use Database\Seeders\DialogueSeeder;
use Database\Seeders\HomeBannersSeeder;
use Database\Seeders\LibrarySeeder;
use Database\Seeders\MembershipCourseSeeder;
use Database\Seeders\MobileTestUserSeeder;
use Database\Seeders\PartyLeaguesSeeder;
use Database\Seeders\PartyOrgansSeeder;
use Database\Seeders\PartyProfileSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PresidiumSeeder;
use Database\Seeders\PriorityProjectsSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\StaticPagesSeeder;
use Database\Seeders\ZimbabweConstitutionSeeder;
use Illuminate\Support\Facades\Artisan;

class SetupInstallService
{
    /**
     * Create the MySQL/MariaDB database if missing, then run all migrations (WordPress-style install).
     */
    public function provisionDatabase(): void
    {
        $this->ensureDatabaseExists();
        $this->runMigrations();
        $this->ensurePublicStorageLink();
    }

    public function ensurePublicStorageLink(): void
    {
        if (is_link(public_path('storage')) || is_dir(public_path('storage'))) {
            return;
        }

        Artisan::call('storage:link');
    }

    /**
     * Create the configured database when the server user has permission (MySQL/MariaDB/SQLite).
     */
    public function ensureDatabaseExists(): void
    {
        $driver = (string) config('database.default');
        $connection = config("database.connections.{$driver}");
        if (! is_array($connection)) {
            throw new \RuntimeException('Database connection ['.$driver.'] is not configured.');
        }

        $connectionDriver = (string) ($connection['driver'] ?? '');

        if ($connectionDriver === 'sqlite') {
            $database = (string) ($connection['database'] ?? '');
            if ($database !== '' && $database !== ':memory:') {
                $directory = dirname($database);
                if ($directory !== '' && $directory !== '.' && ! is_dir($directory)) {
                    if (! mkdir($directory, 0755, true) && ! is_dir($directory)) {
                        throw new \RuntimeException('Cannot create SQLite directory: '.$directory);
                    }
                }
            }

            return;
        }

        if (! in_array($connectionDriver, ['mysql', 'mariadb'], true)) {
            return;
        }

        $database = (string) ($connection['database'] ?? '');
        if ($database === '') {
            throw new \RuntimeException('DB_DATABASE is not set in .env');
        }

        $host = (string) ($connection['host'] ?? '127.0.0.1');
        $port = (string) ($connection['port'] ?? '3306');
        $username = (string) ($connection['username'] ?? '');
        $password = (string) ($connection['password'] ?? '');
        $charset = (string) ($connection['charset'] ?? 'utf8mb4');
        $collation = (string) ($connection['collation'] ?? 'utf8mb4_unicode_ci');

        $dsn = "mysql:host={$host};port={$port};charset={$charset}";
        $pdo = new \PDO($dsn, $username, $password, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ]);

        $safeName = str_replace('`', '``', $database);
        $pdo->exec(
            "CREATE DATABASE IF NOT EXISTS `{$safeName}` CHARACTER SET {$charset} COLLATE {$collation}"
        );
    }

    public function runMigrations(): void
    {
        Artisan::call('migrate', ['--force' => true]);
    }

    public function systemAdminExists(): bool
    {
        if (! Role::query()->where('slug', 'system_admin')->exists()) {
            return false;
        }

        return User::query()
            ->whereHas('roles', fn ($q) => $q->where('slug', 'system_admin'))
            ->exists();
    }

    /**
     * @param  array{name: string, surname: string, email: string, password: string}  $data
     */
    public function createSystemAdmin(array $data): User
    {
        Artisan::call('db:seed', ['--class' => RoleSeeder::class, '--force' => true]);
        Artisan::call('db:seed', ['--class' => PermissionSeeder::class, '--force' => true]);

        $user = User::create([
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $role = Role::query()->where('slug', 'system_admin')->firstOrFail();
        if (! $user->hasRole('system_admin')) {
            $user->roles()->attach($role->id);
        }

        return $user;
    }

    public function seedPlatformContent(bool $includeMobileTestUser = false): void
    {
        $seeders = [
            RoleSeeder::class,
            PermissionSeeder::class,
            ConstitutionSeeder::class,
            ZimbabweConstitutionSeeder::class,
            AmendmentBill2026Seeder::class,
            AmendmentBill2026MetaSyncSeeder::class,
            MembershipCourseSeeder::class,
            DialogueSeeder::class,
            PartyProfileSeeder::class,
            PartyOrgansSeeder::class,
            PartyLeaguesSeeder::class,
            PresidiumSeeder::class,
            PriorityProjectsSeeder::class,
            \Database\Seeders\AcademyBadgesSeeder::class,
            HomeBannersSeeder::class,
            LibrarySeeder::class,
            StaticPagesSeeder::class,
        ];

        foreach ($seeders as $seeder) {
            Artisan::call('db:seed', ['--class' => $seeder, '--force' => true]);
        }

        if ($includeMobileTestUser) {
            Artisan::call('db:seed', ['--class' => MobileTestUserSeeder::class, '--force' => true]);
        }

        if (file_exists(storage_path('app/zimbabwe-constitution-source.txt'))) {
            Artisan::call('constitution:import-zimbabwe');
        }
    }
}
