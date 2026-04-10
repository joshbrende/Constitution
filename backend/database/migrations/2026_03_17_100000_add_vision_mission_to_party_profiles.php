<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('party_profiles', function (Blueprint $table) {
            $table->longText('vision')->nullable();
            $table->longText('mission')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('party_profiles', function (Blueprint $table) {
            $table->dropColumn(['vision', 'mission']);
        });
    }
};
