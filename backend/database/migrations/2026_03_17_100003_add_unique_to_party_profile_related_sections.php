<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('party_profile_related_sections', function (Blueprint $table) {
            $table->unique(['party_profile_id', 'section_id'], 'party_rel_sections_unique');
        });
    }

    public function down(): void
    {
        Schema::table('party_profile_related_sections', function (Blueprint $table) {
            $table->dropUnique('party_rel_sections_unique');
        });
    }
};
