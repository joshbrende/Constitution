<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessment_answers', function (Blueprint $table) {
            $table->unique(
                ['assessment_attempt_id', 'question_id'],
                'assessment_answers_attempt_question_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('assessment_answers', function (Blueprint $table) {
            $table->dropUnique('assessment_answers_attempt_question_unique');
        });
    }
};

