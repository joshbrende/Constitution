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
        Schema::table('refresh_tokens', function (Blueprint $table) {
            $table->uuid('family_id')->nullable()->after('user_id')->index();
        });

        // Legacy rows: each token becomes its own family so rotation can continue safely.
        DB::table('refresh_tokens')
            ->whereNull('family_id')
            ->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('refresh_tokens')
                        ->where('id', $row->id)
                        ->update(['family_id' => (string) Str::uuid()]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('refresh_tokens', function (Blueprint $table) {
            $table->dropColumn('family_id');
        });
    }
};
