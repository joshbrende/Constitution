<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_activity_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('last_seen_audit_log_id')->default(0);
            $table->timestamps();

            $table->unique('user_id');
            $table->index('last_seen_audit_log_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_activity_reads');
    }
};

