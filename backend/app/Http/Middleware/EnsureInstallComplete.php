<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirect authenticated routes to the install wizard until setup is complete.
 */
class EnsureInstallComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Schema::hasTable('site_settings')) {
            return redirect()->route('setup.index');
        }

        $installedAt = SiteSetting::get('installed_at');
        if (empty($installedAt)) {
            return redirect()->route('setup.index');
        }

        return $next($request);
    }
}
