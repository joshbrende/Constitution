<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Indexes for high-traffic constitution, reader, dialogue, and version lookups.
     */
    public function up(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            $table->index(
                ['constitution_slug', 'part_id', 'order'],
                'chapters_constitution_part_order_idx'
            );
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->index(['chapter_id', 'order'], 'sections_chapter_order_idx');
            $table->index(['is_active', 'chapter_id'], 'sections_active_chapter_idx');
        });

        Schema::table('section_versions', function (Blueprint $table) {
            $table->index(
                ['section_id', 'status', 'effective_to'],
                'section_versions_section_status_effective_idx'
            );
        });

        Schema::table('article_aliases', function (Blueprint $table) {
            $table->index('alias_label', 'article_aliases_label_idx');
        });

        Schema::table('dialogue_messages', function (Blueprint $table) {
            $table->index(
                ['dialogue_thread_id', 'created_at'],
                'dialogue_messages_thread_created_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dialogue_messages', function (Blueprint $table) {
            $table->dropIndex('dialogue_messages_thread_created_idx');
        });

        Schema::table('article_aliases', function (Blueprint $table) {
            $table->dropIndex('article_aliases_label_idx');
        });

        Schema::table('section_versions', function (Blueprint $table) {
            $table->dropIndex('section_versions_section_status_effective_idx');
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->dropIndex('sections_active_chapter_idx');
            $table->dropIndex('sections_chapter_order_idx');
        });

        Schema::table('chapters', function (Blueprint $table) {
            $table->dropIndex('chapters_constitution_part_order_idx');
        });
    }
};
