<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resume_skills', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('resume_id')->constrained('resumes')->cascadeOnDelete();
            $table->string('name');
            $table->string('category')->nullable();
            $table->string('proficiency')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->jsonb('metadata')->nullable();
            $table->timestampsTz();

            $table->index(['resume_id', 'sort_order']);
            $table->index(['resume_id', 'category']);
            $table->index(['resume_id', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resume_skills');
    }
};
