<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('facilitator_chat_messages')) {
            return;
        }
        Schema::create('facilitator_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->enum('type', ['question', 'reply', 'announcement']);
            $table->foreignId('in_reply_to_id')->nullable()->constrained('facilitator_chat_messages')->nullOnDelete();
            $table->enum('status', ['pending', 'answered', 'dismissed'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilitator_chat_messages');
    }
};
