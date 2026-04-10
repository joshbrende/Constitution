<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('attendance_registers')) {
            return;
        }
        Schema::create('attendance_registers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('enrollment_id');
            $table->string('title', 50)->nullable();
            $table->string('name');
            $table->string('surname');
            $table->string('designation', 255)->nullable();
            $table->string('organisation', 255)->nullable();
            $table->string('contact_number', 50)->nullable();
            $table->string('email');
            $table->timestamps();

            $table->unique(['enrollment_id', 'unit_id']);
            $table->index('course_id');
            $table->index('unit_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_registers');
    }
};
