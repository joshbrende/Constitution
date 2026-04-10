<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Updates existing "Quiz" in unit and quiz titles to "Knowledge Check"
     * so the sidebar and footer show "Module 1: Knowledge Check" etc.
     */
    public function up(): void
    {
        // 1. Units: "Module X: Quiz" -> "Module X: Knowledge Check"
        if (Schema::hasTable('units') && (Schema::hasColumn('units', 'unit_type') || Schema::hasColumn('units', 'type'))) {
            $typeCol = Schema::hasColumn('units', 'unit_type') ? 'unit_type' : 'type';
            DB::table('units')
                ->where($typeCol, 'quiz')
                ->where('title', 'like', '%: Quiz%')
                ->update(['title' => DB::raw("REPLACE(title, ': Quiz', ': Knowledge Check')")]);
        }

        // 2. Units: "Module X Quiz" or "Quick Quiz" etc. -> "Knowledge Check"
        if (Schema::hasTable('units') && (Schema::hasColumn('units', 'unit_type') || Schema::hasColumn('units', 'type'))) {
            $typeCol = Schema::hasColumn('units', 'unit_type') ? 'unit_type' : 'type';
            // Only where it still ends with " Quiz" and is not already "Knowledge Check"
            $rows = DB::table('units')->where($typeCol, 'quiz')->where('title', 'like', '% Quiz')->get();
            foreach ($rows as $u) {
                if (str_ends_with($u->title, ' Quiz') && !str_contains($u->title, 'Knowledge Check')) {
                    DB::table('units')->where('id', $u->id)->update([
                        'title' => str_replace(' Quiz', ' Knowledge Check', $u->title),
                    ]);
                }
            }
        }

        // 3. Quizzes table: "Module X Quiz" -> "Module X Knowledge Check"
        if (Schema::hasTable('quizzes') && Schema::hasColumn('quizzes', 'title')) {
            $rows = DB::table('quizzes')->where('title', 'like', '% Quiz')->get();
            foreach ($rows as $q) {
                if (!str_contains($q->title, 'Knowledge Check')) {
                    DB::table('quizzes')->where('id', $q->id)->update([
                        'title' => str_replace(' Quiz', ' Knowledge Check', $q->title),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $typeCol = Schema::hasColumn('units', 'unit_type') ? 'unit_type' : 'type';

        if (Schema::hasTable('units')) {
            DB::table('units')->where($typeCol, 'quiz')
                ->where('title', 'like', '%: Knowledge Check%')
                ->update(['title' => DB::raw("REPLACE(title, ': Knowledge Check', ': Quiz')")]);

            $rows = DB::table('units')->where($typeCol, 'quiz')->where('title', 'like', '% Knowledge Check')->get();
            foreach ($rows as $u) {
                if (str_ends_with($u->title, ' Knowledge Check')) {
                    DB::table('units')->where('id', $u->id)->update([
                        'title' => str_replace(' Knowledge Check', ' Quiz', $u->title),
                    ]);
                }
            }
        }

        if (Schema::hasTable('quizzes') && Schema::hasColumn('quizzes', 'title')) {
            $rows = DB::table('quizzes')->where('title', 'like', '% Knowledge Check')->get();
            foreach ($rows as $q) {
                DB::table('quizzes')->where('id', $q->id)->update([
                    'title' => str_replace(' Knowledge Check', ' Quiz', $q->title),
                ]);
            }
        }
    }
};
