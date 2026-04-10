<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('library_categories')->nullOnDelete();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('library_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_category_id')->nullable()->constrained('library_categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('abstract')->nullable();
            $table->longText('body')->nullable();
            $table->string('document_type', 40)->default('document'); // policy, speech, pamphlet, manual, resolution, other
            $table->string('language', 10)->default('en');
            $table->timestamp('published_at')->nullable();
            $table->string('access_rule', 20)->default('member'); // public, member, leadership
            $table->string('file_path', 500)->nullable(); // optional PDF/file for download
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['library_category_id', 'published_at']);
            $table->index(['access_rule', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_documents');
        Schema::dropIfExists('library_categories');
    }
};
