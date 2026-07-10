<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_web_push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('endpoint', 768);
            $table->string('public_key', 255);
            $table->string('auth_token', 255);
            $table->string('device_name', 120)->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->unique(['endpoint'], 'user_web_push_endpoint_unique');
            $table->index(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_web_push_subscriptions');
    }
};
