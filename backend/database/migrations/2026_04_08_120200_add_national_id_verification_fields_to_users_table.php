<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('national_id_verified_at')->nullable()->after('national_id');
            $table->string('national_id_verification_source', 50)->nullable()->after('national_id_verified_at');
            $table->string('national_id_verification_ref', 100)->nullable()->after('national_id_verification_source');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'national_id_verified_at',
                'national_id_verification_source',
                'national_id_verification_ref',
            ]);
        });
    }
};

