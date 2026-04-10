<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class HealthController extends Controller
{
    public function show(): JsonResponse
    {
        $checks = [
            'database' => false,
            'redis' => false,
        ];

        // Database check
        try {
            DB::select('select 1');
            $checks['database'] = true;
        } catch (\Throwable) {
            $checks['database'] = false;
        }

        // Redis / cache check
        try {
            if (class_exists(Redis::class)) {
                Redis::connection()->ping();
            } else {
                Cache::store(config('cache.default'))->get('health-check', null);
            }
            $checks['redis'] = true;
        } catch (\Throwable) {
            $checks['redis'] = false;
        }

        $ok = ! in_array(false, $checks, true);

        return response()->json([
            'status' => $ok ? 'ok' : 'degraded',
            'checks' => $checks,
        ], $ok ? 200 : 503);
    }
}

