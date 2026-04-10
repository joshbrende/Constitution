<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('certificates')) {
            return;
        }

        Schema::table('certificates', function (Blueprint $table) {
            if (! Schema::hasColumn('certificates', 'verification_code')) {
                $table->string('verification_code', 12)->nullable()->unique()->after('certificate_number');
            }
        });

        // Backfill existing certificates with verification codes
        if (Schema::hasColumn('certificates', 'verification_code')) {
            $certificates = DB::table('certificates')->whereNull('verification_code')->get();
            foreach ($certificates as $cert) {
                DB::table('certificates')
                    ->where('id', $cert->id)
                    ->update(['verification_code' => Str::upper(Str::random(8))]);
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('certificates') || ! Schema::hasColumn('certificates', 'verification_code')) {
            return;
        }

        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn('verification_code');
        });
    }
};
