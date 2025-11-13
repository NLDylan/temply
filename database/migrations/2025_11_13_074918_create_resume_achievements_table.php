<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resume_achievements', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('resume_id')->constrained('resumes')->cascadeOnDelete();
            $table->string('title');
            $table->string('issuer')->nullable();
            $table->date('achieved_on')->nullable();
            $table->string('category')->nullable();
            $table->string('url')->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->jsonb('metadata')->nullable();
            $table->timestampsTz();

            $table->index(['resume_id', 'sort_order']);
            $table->index(['resume_id', 'category']);
            $table->index(['resume_id', 'achieved_on']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resume_achievements');
    }
};
