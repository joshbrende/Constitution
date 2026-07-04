<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('membership_standing', 32)->default('applicant')->after('wing');
        });

        // Backfill from existing data.
        if (Schema::hasTable('certificates')) {
            DB::table('users')
                ->whereIn('id', DB::table('certificates')->distinct()->pluck('user_id'))
                ->update(['membership_standing' => 'member']);
        }

        if (Schema::hasTable('certificate_applications')) {
            DB::table('users')
                ->where('membership_standing', 'applicant')
                ->whereIn('id', DB::table('certificate_applications')->distinct()->pluck('user_id'))
                ->update(['membership_standing' => 'provisional']);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('membership_standing');
        });
    }
};
