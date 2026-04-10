<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSetupNotCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        $installedAt = SiteSetting::get('installed_at');
        if (! empty($installedAt)) {
            abort(404);
        }

        return $next($request);
    }
}

