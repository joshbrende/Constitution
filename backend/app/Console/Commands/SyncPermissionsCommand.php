<?php

namespace App\Console\Commands;

use App\Services\AuditIntegrityService;
use App\Services\PermissionSyncService;
use Illuminate\Console\Command;

class SyncPermissionsCommand extends Command
{
    protected $signature = 'permissions:sync';

    protected $description = 'Sync admin section and API permissions from config into the database.';

    public function handle(PermissionSyncService $syncService): int
    {
        $syncService->syncAll();
        $this->info('Permissions synced from config/admin.php and config/permissions.php.');

        return self::SUCCESS;
    }
}
