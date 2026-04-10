<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('amendment_clause_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('amendment_section_id')->constrained('sections')->cascadeOnDelete();
            $table->foreignId('zimbabwe_section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->string('ref_label', 100)->nullable(); // e.g. "Section 92", "Part 4 Ch 12" when section not in DB
            $table->string('relation_type', 30)->default('amends'); // amends, repeals, inserts
            $table->timestamps();

            $table->index(['amendment_section_id', 'zimbabwe_section_id'], 'amendment_zw_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amendment_clause_relations');
    }
};
