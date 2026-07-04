<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditArchiveService;
use App\Services\AuditIntegrityService;
use App\Services\AuditLogger;
use App\Services\PermissionSyncService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Phase4AuditInfrastructureTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_log_cannot_be_updated_without_guard(): void
    {
        $log = app(AuditLogger::class)->log(action: 'test.append_only');

        $this->expectException(\RuntimeException::class);
        $log->update(['action' => 'tampered']);
    }

    public function test_audit_log_can_be_deleted_with_guard(): void
    {
        $log = app(AuditLogger::class)->log(action: 'test.deletable');

        AuditLog::allowingMutation(fn () => $log->delete());

        $this->assertDatabaseMissing('audit_logs', ['id' => $log->id]);
    }

    public function test_audit_logger_writes_integrity_hash_chain(): void
    {
        AuditLog::allowingMutation(fn () => AuditLog::query()->delete());

        $first = app(AuditLogger::class)->log(action: 'test.chain.one');
        $second = app(AuditLogger::class)->log(action: 'test.chain.two');

        $this->assertNotNull($first->integrity_hash);
        $this->assertNull($first->previous_hash);
        $this->assertNotNull($second->integrity_hash);
        $this->assertSame($first->integrity_hash, $second->previous_hash);

        $result = app(AuditIntegrityService::class)->verifyChain($first->id, $second->id);
        $this->assertTrue($result['valid']);
    }

    public function test_cleanup_archives_before_purge(): void
    {
        Storage::fake('local');

        AuditLog::allowingMutation(function () {
            AuditLog::query()->delete();
        });

        config([
            'audit.archive.require_before_purge' => true,
            'operations.cleanup.audit_log_retention_days' => 365,
        ]);

        $old = AuditLog::allowingMutation(function () {
            $log = new AuditLog(['action' => 'test.old']);
            $log->created_at = now()->subDays(400);
            $log->updated_at = now()->subDays(400);
            $log->save();

            return $log;
        });

        $cutoff = now()->subDays(365);
        $this->assertTrue(
            AuditLog::query()->where('id', $old->id)->where('created_at', '<', $cutoff)->exists()
        );

        $this->artisan('ops:cleanup-security-data', [
            '--skip-archive' => false,
        ])->assertSuccessful();

        $this->assertDatabaseMissing('audit_logs', ['id' => $old->id]);
        $files = Storage::disk('local')->allFiles('audit-archives');
        $this->assertNotEmpty($files);
    }

    public function test_audit_export_command_writes_jsonl(): void
    {
        Storage::fake('local');

        app(AuditLogger::class)->log(action: 'test.export.me');

        $this->artisan('audit:export')->assertSuccessful();

        $files = Storage::disk('local')->allFiles('audit-archives');
        $this->assertNotEmpty($files);
    }

    public function test_audit_export_from_admin_writes_meta_audit(): void
    {
        $role = Role::firstOrCreate(['slug' => 'audit_viewer'], ['name' => 'Audit Viewer']);
        $viewer = User::factory()->create(['email' => 'audit-export@example.org.zw']);
        $viewer->roles()->attach($role->id);

        app(AuditLogger::class)->log(action: 'test.exportable');

        $response = $this->actingAs($viewer)->get(route('admin.audit-logs.export'));

        $response->assertOk();
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'audit_logs.exported',
            'actor_user_id' => $viewer->id,
        ]);
    }

    public function test_permissions_sync_command_runs(): void
    {
        $this->seed(RoleSeeder::class);

        $this->artisan('permissions:sync')->assertSuccessful();

        $this->assertDatabaseHas('permissions', [
            'slug' => 'admin.section.users',
        ]);
    }

    public function test_audit_verify_command_passes_for_valid_chain(): void
    {
        AuditLog::allowingMutation(fn () => AuditLog::query()->delete());

        app(AuditLogger::class)->log(action: 'verify.one');
        app(AuditLogger::class)->log(action: 'verify.two');

        $this->artisan('audit:verify')->assertSuccessful();
    }
}
