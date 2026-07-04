<?php

namespace Tests\Unit;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditLogDisplayService;
use App\Services\AuditLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogDisplayServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_presents_human_readable_auth_registration(): void
    {
        $user = User::factory()->create(['email' => 'member@example.org.zw', 'surname' => 'Member']);

        $log = app(AuditLogger::class)->log(
            action: 'auth.api.registered',
            targetType: User::class,
            targetId: $user->id,
            metadata: ['email' => 'member@example.org.zw', 'source' => 'api'],
            actorUserId: $user->id,
        );
        $log->load('actor');

        $view = app(AuditLogDisplayService::class)->present($log);

        $this->assertSame('Authentication', $view['category_label']);
        $this->assertSame('Mobile/API registration', $view['action_label']);
        $this->assertSame('member@example.org.zw', $view['actor_hint']);
        $this->assertSame('User account #'.$user->id, $view['target_label']);
        $this->assertTrue($view['show_ip']);
    }

    public function test_failed_login_shows_email_when_actor_missing(): void
    {
        $log = app(AuditLogger::class)->log(
            action: 'auth.api.login_failed',
            targetType: User::class,
            targetId: null,
            metadata: ['email' => 'attacker@example.org.zw'],
        );

        $view = app(AuditLogDisplayService::class)->present($log);

        $this->assertSame('attacker@example.org.zw', $view['actor_label']);
        $this->assertNull($view['target_label']);
        $this->assertSame('warning', $view['severity']);
    }

    public function test_refresh_token_without_id_does_not_show_hash_suffix(): void
    {
        $log = app(AuditLogger::class)->log(
            action: 'auth.api.refresh_failed',
            targetType: \App\Models\RefreshToken::class,
            targetId: null,
            metadata: ['reason' => 'invalid_or_expired_refresh_token'],
        );

        $view = app(AuditLogDisplayService::class)->present($log);

        $this->assertSame('Refresh token', $view['target_label']);
    }
}
