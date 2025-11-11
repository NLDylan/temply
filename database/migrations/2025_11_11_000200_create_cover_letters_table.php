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
        Schema::create('cover_letters', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('template_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('cv_id')->nullable()->constrained('cvs')->nullOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->string('recipient_name')->nullable();
            $table->string('recipient_title')->nullable();
            $table->string('recipient_company')->nullable();
            $table->string('subject')->nullable();
            $table->longText('body');
            $table->jsonb('metadata')->nullable();
            $table->timestampsTz();

            $table->unique(['user_id', 'slug']);
            $table->index(['recipient_company', 'recipient_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cover_letters');
    }
};
