<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_signatures', function (Blueprint $table) {
            $table->id();
            $table->string('type', 32); // board_of_faculty, supervisor, facilitator
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete(); // null for board/supervisor; set for facilitator
            $table->string('path'); // storage path to image file
            $table->timestamps();

            $table->unique(['type', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_signatures');
    }
};
