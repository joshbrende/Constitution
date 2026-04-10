<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('courses')) {
            return;
        }
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->text('learning_guidelines')->nullable();
            $table->text('assessment_guide')->nullable();
            $table->string('short_description', 500)->nullable();
            $table->foreignId('instructor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('featured_image')->nullable();
            $table->string('video_preview')->nullable();
            $table->string('status')->default('published'); // draft, published
            $table->string('access')->default('free'); // free, paid, restricted
            $table->decimal('price', 8, 2)->nullable();
            $table->unsignedInteger('duration')->nullable();
            $table->unsignedInteger('max_students')->nullable();
            $table->json('curriculum')->nullable();
            $table->json('prerequisites')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->unsignedInteger('enrollment_count')->default(0);
            $table->decimal('rating', 3, 2)->nullable();
            $table->unsignedInteger('rating_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
