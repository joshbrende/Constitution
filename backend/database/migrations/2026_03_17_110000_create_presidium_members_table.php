<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presidium_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title');
            $table->string('role_slug')->unique();
            $table->string('photo_url')->nullable();
            $table->text('bio')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->foreignId('zanupf_section_id')
                ->nullable()
                ->constrained('sections')
                ->cascadeOnDelete();
            $table->foreignId('zimbabwe_section_id')
                ->nullable()
                ->constrained('sections')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->index(['is_published', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presidium_members');
    }
};

