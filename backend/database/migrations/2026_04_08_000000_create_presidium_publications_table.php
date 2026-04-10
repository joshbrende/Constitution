<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presidium_publications', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('author')->nullable();
            $table->text('summary')->nullable();
            $table->string('cover_url')->nullable();
            $table->string('article_url')->nullable();
            $table->string('purchase_url')->nullable();
            $table->string('online_copy_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index(['is_published', 'is_featured', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presidium_publications');
    }
};

