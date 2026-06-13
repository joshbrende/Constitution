<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->decimal('certificate_fee_amount', 10, 2)->nullable()->after('certificate_title');
            $table->string('certificate_fee_currency', 3)->default('USD')->after('certificate_fee_amount');
            $table->text('payment_office_instructions')->nullable()->after('certificate_fee_currency');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'certificate_fee_amount',
                'certificate_fee_currency',
                'payment_office_instructions',
            ]);
        });
    }
};
