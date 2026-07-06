<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

/**
 * @group System
 *
 * Load balancer and uptime monitoring endpoint.
 *
 * @unauthenticated
 */
class HealthController extends Controller
{
    /**
     * Health check
     *
     * Returns database and Redis connectivity for load balancers.
     * Redis is checked only when queue, cache, or session uses the redis driver.
     *
     * @response 200 scenario="Healthy" {"status":"ok","checks":{"database":true,"redis":true}}
     * @response 503 scenario="Degraded" {"status":"degraded","checks":{"database":true,"redis":false}}
     */
    public function show(): JsonResponse
    {
        $checks = [
            'database' => false,
        ];

        try {
            DB::select('select 1');
            $checks['database'] = true;
        } catch (\Throwable) {
            $checks['database'] = false;
        }

        if ($this->shouldCheckRedis()) {
            $checks['redis'] = $this->redisIsReachable();
        }

        $ok = ! in_array(false, $checks, true);

        return response()->json([
            'status' => $ok ? 'ok' : 'degraded',
            'checks' => $checks,
        ], $ok ? 200 : 503);
    }

    private function shouldCheckRedis(): bool
    {
        return config('queue.default') === 'redis'
            || config('cache.default') === 'redis'
            || config('session.driver') === 'redis';
    }

    private function redisIsReachable(): bool
    {
        try {
            // Use the dedicated "health" connection so connect/read timeouts are
            // enforced for every supported client (phpredis, Predis, RedisCluster).
            Redis::connection('health')->ping();

            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}

