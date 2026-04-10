<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dialogue_message_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dialogue_message_id')->constrained('dialogue_messages')->cascadeOnDelete();

            // image | pdf | audio | video | other (future)
            $table->string('type', 20);
            $table->string('disk', 30)->default('public');
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->string('mime', 150)->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);

            $table->timestamps();

            $table->index(['dialogue_message_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dialogue_message_attachments');
    }
};

