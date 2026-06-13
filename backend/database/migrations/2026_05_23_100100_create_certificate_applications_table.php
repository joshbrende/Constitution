<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_applications', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessment_attempt_id')->constrained()->cascadeOnDelete();
            $table->string('receipt_number')->unique();
            $table->string('payment_reference_code', 16)->unique();
            $table->decimal('fee_amount', 10, 2);
            $table->string('fee_currency', 3)->default('USD');
            $table->string('status', 40);
            $table->timestamp('exam_passed_at');
            $table->timestamp('payment_confirmed_at')->nullable();
            $table->foreignId('payment_confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('payment_reference_note')->nullable();
            $table->timestamp('presidium_approved_at')->nullable();
            $table->foreignId('presidium_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('presidium_note')->nullable();
            $table->foreignId('certificate_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('printed_at')->nullable();
            $table->foreignId('printed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('ready_for_collection_at')->nullable();
            $table->timestamp('collected_at')->nullable();
            $table->foreignId('collected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('collection_office')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_applications');
    }
};
