<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = config('audit.connection') ?: config('database.default');

        if (! Schema::connection($connection)->hasTable('audit_logs')) {
            Schema::connection($connection)->create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('actor_user_id')->nullable()->index();
                $table->string('action', 120);
                $table->string('target_type', 120)->nullable();
                $table->unsignedBigInteger('target_id')->nullable();
                $table->json('metadata')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->string('request_id', 120)->nullable();
                $table->string('previous_hash', 64)->nullable();
                $table->string('integrity_hash', 64)->nullable()->index();
                $table->timestamps();

                $table->index(['target_type', 'target_id']);
                $table->index('action');
                $table->index('created_at');
            });

            return;
        }

        Schema::connection($connection)->table('audit_logs', function (Blueprint $table) use ($connection) {
            if (! Schema::connection($connection)->hasColumn('audit_logs', 'previous_hash')) {
                $table->string('previous_hash', 64)->nullable()->after('request_id');
            }
            if (! Schema::connection($connection)->hasColumn('audit_logs', 'integrity_hash')) {
                $table->string('integrity_hash', 64)->nullable()->index()->after('previous_hash');
            }
        });
    }

    public function down(): void
    {
        $connection = config('audit.connection') ?: config('database.default');

        if (! Schema::connection($connection)->hasTable('audit_logs')) {
            return;
        }

        Schema::connection($connection)->table('audit_logs', function (Blueprint $table) use ($connection) {
            if (Schema::connection($connection)->hasColumn('audit_logs', 'integrity_hash')) {
                $table->dropIndex(['integrity_hash']);
                $table->dropColumn('integrity_hash');
            }
            if (Schema::connection($connection)->hasColumn('audit_logs', 'previous_hash')) {
                $table->dropColumn('previous_hash');
            }
        });
    }
};
