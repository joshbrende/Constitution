<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('units', function (Blueprint $table) {
            if (!Schema::hasColumn('units', 'quiz_id')) {
                $table->unsignedBigInteger('quiz_id')->nullable()->after('prerequisite_unit_id');
                $table->index('quiz_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropIndex(['quiz_id']);
            $table->dropColumn('quiz_id');
        });
    }
};
