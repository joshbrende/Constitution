<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('quizzes')) {
            return;
        }

        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->unsignedInteger('duration')->nullable();
            $table->unsignedInteger('pass_percentage')->default(70);
            $table->unsignedInteger('max_attempts')->default(5);
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('show_results')->default(true);
            $table->boolean('show_correct_answers')->default(true);
            $table->unsignedInteger('total_points')->default(0);
            $table->string('grading_type')->default('auto');
            $table->string('assessment_type')->default('summative');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};

