<?php

namespace App\Http\Middleware;

use App\Services\Setup\SetupProgressResolver;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class SyncSetupProgress
{
    public function __construct(
        protected SetupProgressResolver $progress
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (Schema::hasTable('site_settings')) {
            $this->progress->syncSession();
        }

        return $next($request);
    }
}
