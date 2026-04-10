<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('party_profile_related_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_profile_id')->constrained('party_profiles')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->string('label')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['party_profile_id', 'section_id'], 'party_profile_related_sections_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('party_profile_related_sections');
    }
};
