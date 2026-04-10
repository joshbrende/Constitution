<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('priority_projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('summary', 500)->nullable();
            $table->text('body')->nullable();
            $table->string('image_url')->nullable();
            $table->unsignedBigInteger('zanupf_section_id')->nullable();
            $table->unsignedBigInteger('zimbabwe_section_id')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('likes_count')->default(0);
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();

            $table->foreign('zanupf_section_id')->references('id')->on('sections')->nullOnDelete();
            $table->foreign('zimbabwe_section_id')->references('id')->on('sections')->nullOnDelete();
            $table->foreign('created_by_user_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('priority_project_likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('priority_project_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->unique(['priority_project_id', 'user_id'], 'priority_project_likes_unique');
            $table->foreign('priority_project_id')->references('id')->on('priority_projects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('priority_project_likes');
        Schema::dropIfExists('priority_projects');
    }
};

