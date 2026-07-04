<?php

namespace Tests;

use App\Models\SiteSetting;
use App\Models\User;
use App\Services\TokenAbilityService;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'session.driver' => 'array',
            'cache.default' => 'array',
            'queue.default' => 'sync',
        ]);

        // Docker Compose sets QUEUE_CONNECTION=redis on the app container; force sync for tests.
        // Disable throttling globally except AuthApiRateLimitTest (opt-in via $enableThrottleMiddleware).
        if (! property_exists($this, 'enableThrottleMiddleware') || ! $this->enableThrottleMiddleware) {
            $this->withoutMiddleware(ThrottleRequests::class);
        }

        // Web form tests (admin Blade POST/PUT) do not carry CSRF tokens by default.
        $this->withoutMiddleware(ValidateCsrfToken::class);

        if (in_array(RefreshDatabase::class, class_uses_recursive(static::class))) {
            $this->markInstallCompleteForTests();
        }
    }

    /**
     * Most feature tests expect the app past the install wizard. Setup* tests clear this in setUp().
     */
    protected function markInstallCompleteForTests(): void
    {
        if (Schema::hasTable('site_settings')) {
            SiteSetting::set('installed_at', now()->toIso8601String());
        }
    }

    /**
     * Authenticate for API tests with role-appropriate Sanctum abilities (Phase 2).
     *
     * @param  list<string>|null  $abilities  Override; null = resolve from user roles / config.
     */
    protected function sanctumAs(User $user, ?array $abilities = null): void
    {
        $user->loadMissing('roles');

        if ($abilities === null) {
            $abilities = app(TokenAbilityService::class)->abilitiesForUser($user);
            // Feature tests often create users without roles; mirror post-register student access.
            if ($user->roles->isEmpty()) {
                $abilities = config('permissions.role_api_abilities.student')
                    ?? array_keys(config('permissions.api_ability_labels', []));
            }
        }

        Sanctum::actingAs($user, $abilities);
    }
}
