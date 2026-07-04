<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('requires_membership')->default(true)->after('grants_membership');
            $table->string('audience', 32)->default('all')->after('requires_membership');
            $table->boolean('issues_certificate')->default(false)->after('payment_office_instructions');
            $table->string('certificate_number_prefix', 24)->nullable()->after('issues_certificate');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'requires_membership',
                'audience',
                'issues_certificate',
                'certificate_number_prefix',
            ]);
        });
    }
};
