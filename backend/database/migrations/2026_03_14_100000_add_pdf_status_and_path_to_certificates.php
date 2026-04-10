<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('certificates')) {
            return;
        }

        Schema::table('certificates', function (Blueprint $table) {
            if (! Schema::hasColumn('certificates', 'pdf_status')) {
                $table->string('pdf_status', 20)->default('pending')->after('issued_at');
            }
            if (! Schema::hasColumn('certificates', 'pdf_path')) {
                $table->string('pdf_path', 500)->nullable()->after('pdf_status');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('certificates')) {
            return;
        }

        Schema::table('certificates', function (Blueprint $table) {
            $drop = [];
            if (Schema::hasColumn('certificates', 'pdf_status')) {
                $drop[] = 'pdf_status';
            }
            if (Schema::hasColumn('certificates', 'pdf_path')) {
                $drop[] = 'pdf_path';
            }
            if (! empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};
