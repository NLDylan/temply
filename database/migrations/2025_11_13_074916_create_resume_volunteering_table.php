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
        Schema::create('resume_volunteerings', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('resume_id')->constrained('resumes')->cascadeOnDelete();
            $table->string('organization');
            $table->string('role')->nullable();
            $table->string('location')->nullable();
            $table->date('started_on')->nullable();
            $table->date('ended_on')->nullable();
            $table->boolean('is_current')->default(false);
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->jsonb('metadata')->nullable();
            $table->timestampsTz();

            $table->index(['resume_id', 'sort_order']);
            $table->index(['resume_id', 'is_current']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resume_volunteerings');
    }
};
