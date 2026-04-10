<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dialogue_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dialogue_channel_id')->constrained('dialogue_channels')->cascadeOnDelete();
            $table->foreignId('created_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->foreignId('zanupf_section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->foreignId('zimbabwe_section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->string('status')->default('open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dialogue_threads');
    }
};

