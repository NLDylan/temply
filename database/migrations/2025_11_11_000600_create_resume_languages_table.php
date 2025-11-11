<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resume_languages', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('resume_id')->constrained('resumes')->cascadeOnDelete();
            $table->string('language');
            $table->string('proficiency')->nullable();
            $table->boolean('is_native')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->jsonb('metadata')->nullable();
            $table->timestampsTz();

            $table->index(['resume_id', 'sort_order']);
            $table->index(['resume_id', 'is_native']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resume_languages');
    }
};
