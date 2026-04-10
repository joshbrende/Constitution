<?php

namespace App\Http\Middleware;

use App\Services\AdminAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminOrContentEditor
{
    public function __construct(
        protected AdminAccessService $adminAccess
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if (! $this->adminAccess->hasAnyAdminAccess($request->user())) {
            abort(403, 'Admin access required.');
        }

        return $next($request);
    }
}
