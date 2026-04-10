<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('courses') || !Schema::hasColumn('courses', 'instructor_id')) {
            return;
        }
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
        });
        DB::statement('ALTER TABLE courses MODIFY instructor_id BIGINT UNSIGNED NULL');
        Schema::table('courses', function (Blueprint $table) {
            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('courses') || Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
        });
        DB::statement('ALTER TABLE courses MODIFY instructor_id BIGINT UNSIGNED NOT NULL');
        Schema::table('courses', function (Blueprint $table) {
            $table->foreign('instructor_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
