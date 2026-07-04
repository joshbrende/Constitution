<?php

namespace Tests;

use App\Models\User;
use App\Services\TokenAbilityService;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Docker Compose sets QUEUE_CONNECTION=redis on the app container; force sync for tests.
        config(['queue.default' => 'sync']);

        // Web form tests (admin Blade POST/PUT) do not carry CSRF tokens by default.
        $this->withoutMiddleware(ValidateCsrfToken::class);
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
