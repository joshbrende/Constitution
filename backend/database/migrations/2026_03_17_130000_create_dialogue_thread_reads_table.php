<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dialogue_thread_reads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dialogue_thread_id');
            $table->unsignedBigInteger('user_id');
            $table->dateTime('last_read_at')->nullable();

            $table->unique(['dialogue_thread_id', 'user_id'], 'dialogue_thread_reads_unique');
            $table->foreign('dialogue_thread_id')
                ->references('id')
                ->on('dialogue_threads')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dialogue_thread_reads');
    }
};

