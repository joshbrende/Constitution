<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Courses table indexes
        Schema::table('courses', function (Blueprint $table) {
            if (!$this->hasIndex('courses', 'courses_status_index')) {
                $table->index('status', 'courses_status_index');
            }
            if (!$this->hasIndex('courses', 'courses_instructor_id_index')) {
                $table->index('instructor_id', 'courses_instructor_id_index');
            }
            if (!$this->hasIndex('courses', 'courses_enrollment_count_index')) {
                $table->index('enrollment_count', 'courses_enrollment_count_index');
            }
            if (!$this->hasIndex('courses', 'courses_created_at_index')) {
                $table->index('created_at', 'courses_created_at_index');
            }
        });

        // Enrollments table indexes (user_id and course_id already have foreign keys, but add composite)
        Schema::table('enrollments', function (Blueprint $table) {
            if (!$this->hasIndex('enrollments', 'enrollments_user_course_index')) {
                $table->index(['user_id', 'course_id'], 'enrollments_user_course_index');
            }
            if (!$this->hasIndex('enrollments', 'enrollments_status_index')) {
                $table->index('status', 'enrollments_status_index');
            }
            if (!$this->hasIndex('enrollments', 'enrollments_progress_percentage_index')) {
                $table->index('progress_percentage', 'enrollments_progress_percentage_index');
            }
            if (!$this->hasIndex('enrollments', 'enrollments_enrolled_at_index')) {
                $table->index('enrolled_at', 'enrollments_enrolled_at_index');
            }
        });

        // Units table indexes
        Schema::table('units', function (Blueprint $table) {
            if (!$this->hasIndex('units', 'units_course_id_index')) {
                $table->index('course_id', 'units_course_id_index');
            }
            if (!$this->hasIndex('units', 'units_unit_type_index')) {
                $table->index('unit_type', 'units_unit_type_index');
            }
            if (!$this->hasIndex('units', 'units_order_index')) {
                $table->index('order', 'units_order_index');
            }
            if (!$this->hasIndex('units', 'units_quiz_id_index')) {
                $table->index('quiz_id', 'units_quiz_id_index');
            }
        });

        // Quiz attempts table indexes
        Schema::table('quiz_attempts', function (Blueprint $table) {
            if (!$this->hasIndex('quiz_attempts', 'quiz_attempts_user_quiz_index')) {
                $table->index(['user_id', 'quiz_id'], 'quiz_attempts_user_quiz_index');
            }
            if (!$this->hasIndex('quiz_attempts', 'quiz_attempts_status_index')) {
                $table->index('status', 'quiz_attempts_status_index');
            }
            if (!$this->hasIndex('quiz_attempts', 'quiz_attempts_completed_at_index')) {
                $table->index('completed_at', 'quiz_attempts_completed_at_index');
            }
        });

        // Questions table indexes
        Schema::table('questions', function (Blueprint $table) {
            if (!$this->hasIndex('questions', 'questions_quiz_id_index')) {
                $table->index('quiz_id', 'questions_quiz_id_index');
            }
            if (!$this->hasIndex('questions', 'questions_order_index')) {
                $table->index('order', 'questions_order_index');
            }
        });

        // Unit completions table indexes
        Schema::table('unit_completions', function (Blueprint $table) {
            if (!$this->hasIndex('unit_completions', 'unit_completions_user_unit_index')) {
                $table->index(['user_id', 'unit_id'], 'unit_completions_user_unit_index');
            }
            if (!$this->hasIndex('unit_completions', 'unit_completions_completed_at_index')) {
                $table->index('completed_at', 'unit_completions_completed_at_index');
            }
        });

        // Certificates table indexes
        Schema::table('certificates', function (Blueprint $table) {
            if (!$this->hasIndex('certificates', 'certificates_user_course_index')) {
                $table->index(['user_id', 'course_id'], 'certificates_user_course_index');
            }
            if (!$this->hasIndex('certificates', 'certificates_issued_at_index')) {
                $table->index('issued_at', 'certificates_issued_at_index');
            }
        });

        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            if (!$this->hasIndex('users', 'users_points_index')) {
                $table->index('points', 'users_points_index');
            }
        });

        // Tags table indexes
        Schema::table('tags', function (Blueprint $table) {
            if (!$this->hasIndex('tags', 'tags_slug_index')) {
                $table->index('slug', 'tags_slug_index');
            }
        });

        // Attendance registers table indexes
        Schema::table('attendance_registers', function (Blueprint $table) {
            if (!$this->hasIndex('attendance_registers', 'attendance_registers_enrollment_unit_index')) {
                $table->index(['enrollment_id', 'unit_id'], 'attendance_registers_enrollment_unit_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex('courses_status_index');
            $table->dropIndex('courses_instructor_id_index');
            $table->dropIndex('courses_enrollment_count_index');
            $table->dropIndex('courses_created_at_index');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex('enrollments_user_course_index');
            $table->dropIndex('enrollments_status_index');
            $table->dropIndex('enrollments_progress_percentage_index');
            $table->dropIndex('enrollments_enrolled_at_index');
        });

        Schema::table('units', function (Blueprint $table) {
            $table->dropIndex('units_course_id_index');
            $table->dropIndex('units_unit_type_index');
            $table->dropIndex('units_order_index');
            $table->dropIndex('units_quiz_id_index');
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropIndex('quiz_attempts_user_quiz_index');
            $table->dropIndex('quiz_attempts_status_index');
            $table->dropIndex('quiz_attempts_completed_at_index');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex('questions_quiz_id_index');
            $table->dropIndex('questions_order_index');
        });

        Schema::table('unit_completions', function (Blueprint $table) {
            $table->dropIndex('unit_completions_user_unit_index');
            $table->dropIndex('unit_completions_completed_at_index');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->dropIndex('certificates_user_course_index');
            $table->dropIndex('certificates_issued_at_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_points_index');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropIndex('tags_slug_index');
        });

        Schema::table('attendance_registers', function (Blueprint $table) {
            $table->dropIndex('attendance_registers_enrollment_unit_index');
        });
    }

    /**
     * Check if an index exists on a table (MySQL / MariaDB).
     */
    private function hasIndex(string $table, string $index): bool
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            $results = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index]);
            return count($results) > 0;
        }
        if ($driver === 'sqlite') {
            $results = DB::select("SELECT name FROM sqlite_master WHERE type = 'index' AND tbl_name = ? AND name = ?", [$table, $index]);
            return count($results) > 0;
        }
        return false;
    }
};
