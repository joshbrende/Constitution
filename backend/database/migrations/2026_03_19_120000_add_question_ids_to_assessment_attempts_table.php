<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessment_attempts', function (Blueprint $table) {
            // Stores the exact set of question IDs shown/expected for this attempt.
            // Used to prevent submitting answers for different questions than the client saw.
            $table->json('question_ids')->nullable()->after('submitted_at');
        });
    }

    public function down(): void
    {
        Schema::table('assessment_attempts', function (Blueprint $table) {
            $table->dropColumn('question_ids');
        });
    }
};

