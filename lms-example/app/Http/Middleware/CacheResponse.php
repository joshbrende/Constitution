<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $minutes = 60): Response
    {
        // Only cache GET requests for non-authenticated users
        if ($request->method() !== 'GET' || auth()->check()) {
            return $next($request);
        }

        $key = 'response_' . md5($request->fullUrl());
        
        return Cache::remember($key, now()->addMinutes($minutes), function () use ($next, $request) {
            $response = $next($request);
            
            // Only cache successful responses
            if ($response->getStatusCode() === 200) {
                return $response;
            }
            
            return $response;
        });
    }
}
