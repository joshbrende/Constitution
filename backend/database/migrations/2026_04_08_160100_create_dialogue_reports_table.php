<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dialogue_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reported_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('dialogue_thread_id')->nullable()->constrained('dialogue_threads')->cascadeOnDelete();
            $table->foreignId('dialogue_message_id')->nullable()->constrained('dialogue_messages')->cascadeOnDelete();
            $table->string('reason', 60);
            $table->text('details')->nullable();
            $table->string('status', 40)->default('open'); // open|reviewed|resolved|rejected
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('resolution_action', 80)->nullable(); // e.g. message_removed, thread_locked, none
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['dialogue_thread_id', 'created_at']);
            $table->index(['dialogue_message_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dialogue_reports');
    }
};

