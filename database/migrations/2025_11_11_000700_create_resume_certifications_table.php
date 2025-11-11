<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resume_certifications', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('resume_id')->constrained('resumes')->cascadeOnDelete();
            $table->string('name');
            $table->string('issuer')->nullable();
            $table->date('issued_on')->nullable();
            $table->date('expires_on')->nullable();
            $table->string('credential_id')->nullable();
            $table->string('credential_url')->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->jsonb('metadata')->nullable();
            $table->timestampsTz();

            $table->index(['resume_id', 'sort_order']);
            $table->index(['resume_id', 'issued_on']);
            $table->index(['resume_id', 'expires_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resume_certifications');
    }
};
