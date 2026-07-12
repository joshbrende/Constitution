<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Production already ran 2026_07_11_120000 before exam_passed_at was included there.
        // Fresh installs/tests apply it inside that migration; this is a no-op when already nullable.
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE certificate_applications MODIFY exam_passed_at TIMESTAMP NULL');
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE certificate_applications MODIFY exam_passed_at TIMESTAMP NOT NULL');
    }
};
