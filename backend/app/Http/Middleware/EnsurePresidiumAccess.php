<?php

namespace App\Http\Middleware;

use App\Services\AdminAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePresidiumAccess
{
    public function __construct(
        protected AdminAccessService $adminAccess
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        if (! $this->adminAccess->canPerformAdminAction($user, 'presidium_publish')) {
            abort(403, 'Presidium access required.');
        }

        return $next($request);
    }
}
