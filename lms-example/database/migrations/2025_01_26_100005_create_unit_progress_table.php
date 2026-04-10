<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('unit_progress')) {
            return;
        }
        Schema::create('unit_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedTinyInteger('quiz_score')->nullable();
            $table->boolean('quiz_passed')->nullable();
            $table->timestamps();
            $table->unique(['enrollment_id', 'unit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_progress');
    }
};
