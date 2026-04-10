<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_banners', function (Blueprint $table) {
            $table->string('cta_type', 20)->nullable()->after('cta_url'); // internal | external
            $table->string('cta_tab', 50)->nullable()->after('cta_type'); // HomeTab | ConstitutionTab | ChatTab | ProfileTab
            $table->string('cta_screen', 80)->nullable()->after('cta_tab'); // Optional nested screen
            $table->json('cta_params')->nullable()->after('cta_screen');
        });
    }

    public function down(): void
    {
        Schema::table('home_banners', function (Blueprint $table) {
            $table->dropColumn(['cta_type', 'cta_tab', 'cta_screen', 'cta_params']);
        });
    }
};

