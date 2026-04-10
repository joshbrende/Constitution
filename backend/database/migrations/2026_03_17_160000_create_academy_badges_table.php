<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('academy_badges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');

            $table->string('slug')->unique();
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('icon')->nullable(); // optional emoji/icon name

            // rule_type: enrolled_n | completed_n | pass_score_at_least
            $table->string('rule_type');
            $table->integer('target_value')->default(0); // n or score threshold

            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
        });

        Schema::create('academy_user_badges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('academy_badge_id');
            $table->timestamp('unlocked_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'academy_badge_id'], 'academy_user_badges_unique');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('academy_badge_id')->references('id')->on('academy_badges')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_user_badges');
        Schema::dropIfExists('academy_badges');
    }
};

