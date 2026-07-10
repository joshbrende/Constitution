<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_notification_campaigns', function (Blueprint $table) {
            $table->string('trigger', 60)->nullable()->after('status');
            $table->string('source_type', 120)->nullable()->after('trigger');
            $table->unsignedBigInteger('source_id')->nullable()->after('source_type');
            $table->index(['trigger', 'source_type', 'source_id'], 'member_notification_campaigns_source_idx');
        });
    }

    public function down(): void
    {
        Schema::table('member_notification_campaigns', function (Blueprint $table) {
            $table->dropIndex('member_notification_campaigns_source_idx');
            $table->dropColumn(['trigger', 'source_type', 'source_id']);
        });
    }
};
