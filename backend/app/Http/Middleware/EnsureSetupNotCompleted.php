<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class EnsureSetupNotCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Schema::hasTable('site_settings')) {
            $installedAt = SiteSetting::get('installed_at');
            if (! empty($installedAt)) {
                abort(404);
            }
        }

        return $next($request);
    }
}

