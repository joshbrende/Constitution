<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // phpunit.xml sets sqlite :memory:, but Docker Compose injects DB_* env vars
        // that override PHPUnit env. Force in-memory sqlite for isolated test runs.
        if ($app->environment('testing')) {
            config([
                'database.default' => 'sqlite',
                'database.connections.sqlite.database' => ':memory:',
            ]);
        }

        return $app;
    }
}

