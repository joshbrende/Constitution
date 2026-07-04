<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('branch_admitted_at')->nullable()->after('membership_standing');
            $table->foreignId('branch_admitted_by_user_id')->nullable()->after('branch_admitted_at')->constrained('users')->nullOnDelete();
            $table->string('branch_admission_note', 500)->nullable()->after('branch_admitted_by_user_id');
            $table->timestamp('cadre_designated_at')->nullable()->after('branch_admission_note');
            $table->foreignId('cadre_designated_by_user_id')->nullable()->after('cadre_designated_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_admitted_by_user_id');
            $table->dropConstrainedForeignId('cadre_designated_by_user_id');
            $table->dropColumn([
                'branch_admitted_at',
                'branch_admission_note',
                'cadre_designated_at',
            ]);
        });
    }
};
