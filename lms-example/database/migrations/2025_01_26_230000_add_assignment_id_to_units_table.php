<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('units', 'assignment_id')) {
            return;
        }
        Schema::table('units', function (Blueprint $table) {
            $table->unsignedBigInteger('assignment_id')->nullable()->after('quiz_id');
        });
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn('assignment_id');
        });
    }
};
