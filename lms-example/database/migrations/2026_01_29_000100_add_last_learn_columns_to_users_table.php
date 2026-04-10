<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('last_learn_course_id')
                ->nullable()
                ->after('points')
                ->constrained('courses')
                ->nullOnDelete();

            $table->foreignId('last_learn_unit_id')
                ->nullable()
                ->after('last_learn_course_id')
                ->constrained('units')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['last_learn_course_id']);
            $table->dropForeign(['last_learn_unit_id']);
            $table->dropColumn(['last_learn_course_id', 'last_learn_unit_id']);
        });
    }
};

