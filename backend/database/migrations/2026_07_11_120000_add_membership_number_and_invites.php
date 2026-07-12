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
            $table->string('membership_number', 32)->nullable()->unique()->after('membership_standing');
            $table->timestamp('membership_admitted_at')->nullable()->after('membership_number');
            $table->string('membership_source', 32)->nullable()->after('membership_admitted_at');
        });

        Schema::table('certificate_applications', function (Blueprint $table) {
            $table->dropForeign(['assessment_attempt_id']);
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'sqlite') {
            // SQLite: drop + recreate column as nullable (test DB only; production is MySQL).
            Schema::table('certificate_applications', function (Blueprint $table) {
                $table->dropColumn('assessment_attempt_id');
            });
            Schema::table('certificate_applications', function (Blueprint $table) {
                $table->foreignId('assessment_attempt_id')
                    ->nullable()
                    ->after('course_id')
                    ->constrained()
                    ->nullOnDelete();
            });
        } else {
            DB::statement('ALTER TABLE certificate_applications MODIFY assessment_attempt_id BIGINT UNSIGNED NULL');
            Schema::table('certificate_applications', function (Blueprint $table) {
                $table->foreign('assessment_attempt_id')
                    ->references('id')
                    ->on('assessment_attempts')
                    ->nullOnDelete();
            });
        }

        Schema::table('certificate_applications', function (Blueprint $table) {
            $table->string('admission_source', 32)->default('exam')->after('assessment_attempt_id');
        });

        if ($driver === 'sqlite') {
            // Recreate as nullable — invite/admin admissions have no exam timestamp.
            Schema::table('certificate_applications', function (Blueprint $table) {
                $table->dropColumn('exam_passed_at');
            });
            Schema::table('certificate_applications', function (Blueprint $table) {
                $table->timestamp('exam_passed_at')->nullable()->after('status');
            });
        } else {
            DB::statement('ALTER TABLE certificate_applications MODIFY exam_passed_at TIMESTAMP NULL');
        }

        Schema::create('member_invitations', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('token_hash', 64)->unique();
            $table->string('name')->nullable();
            $table->string('surname')->nullable();
            $table->string('national_id', 32)->nullable();
            $table->foreignId('province_id')->nullable()->constrained('provinces')->nullOnDelete();
            $table->string('wing', 32)->nullable();
            $table->foreignId('invited_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['email', 'accepted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_invitations');

        Schema::table('certificate_applications', function (Blueprint $table) {
            $table->dropColumn('admission_source');
            $table->dropForeign(['assessment_attempt_id']);
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'sqlite') {
            Schema::table('certificate_applications', function (Blueprint $table) {
                $table->dropColumn('assessment_attempt_id');
            });
            Schema::table('certificate_applications', function (Blueprint $table) {
                $table->foreignId('assessment_attempt_id')->after('course_id')->constrained()->cascadeOnDelete();
            });
        } else {
            DB::statement('ALTER TABLE certificate_applications MODIFY assessment_attempt_id BIGINT UNSIGNED NOT NULL');
            Schema::table('certificate_applications', function (Blueprint $table) {
                $table->foreign('assessment_attempt_id')
                    ->references('id')
                    ->on('assessment_attempts')
                    ->cascadeOnDelete();
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['membership_number']);
            $table->dropColumn(['membership_number', 'membership_admitted_at', 'membership_source']);
        });
    }
};
