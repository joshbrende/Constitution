<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts the public setup wizard to holders of SETUP_ACCESS_TOKEN.
 */
class EnsureSetupAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('setup.access_token', '');

        if ($expected === '') {
            if (app()->environment('production')) {
                abort(503, 'Installation is locked: configure SETUP_ACCESS_TOKEN in the server environment before running setup.');
            }

            return $next($request);
        }

        $provided = (string) ($request->query('setup_token')
            ?? $request->header('X-Setup-Token')
            ?? '');

        if ($provided === '' || ! hash_equals($expected, $provided)) {
            abort(403, 'A valid setup access token is required to run installation.');
        }

        return $next($request);
    }
}
