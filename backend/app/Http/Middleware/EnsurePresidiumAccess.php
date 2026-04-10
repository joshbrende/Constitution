<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePresidiumAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        // Presidium-protected actions are also allowed for system admins.
        $allowed = ['presidium', 'system_admin'];
        $hasAccess = $user->roles->contains(
            fn ($r) => in_array((string) $r->slug, $allowed, true)
        );

        if (! $hasAccess) {
            abort(403, 'Presidium access required.');
        }

        return $next($request);
    }
}

