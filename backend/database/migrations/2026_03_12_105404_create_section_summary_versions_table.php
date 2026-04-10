<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('section_summary_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_version_id')->constrained('section_versions')->cascadeOnDelete();
            $table->string('language', 5)->default('en');
            $table->longText('summary_text');
            $table->string('reading_level')->nullable(); // e.g. "basic"
            $table->enum('status', ['draft', 'in_review', 'published'])->default('draft');
            $table->timestamps();

            $table->index(['section_version_id', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_summary_versions');
    }
};
