<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->uuid('public_id')->nullable()->unique()->after('id');
            $table->timestamp('expires_at')->nullable()->after('issued_at');
            $table->timestamp('revoked_at')->nullable()->after('expires_at');
        });

        DB::table('certificates')
            ->whereNull('public_id')
            ->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('certificates')
                        ->where('id', $row->id)
                        ->update(['public_id' => (string) Str::uuid()]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn(['public_id', 'expires_at', 'revoked_at']);
        });
    }
};

