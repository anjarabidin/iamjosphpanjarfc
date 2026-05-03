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
        Schema::create('publication_galleys', function (Blueprint $table) {
            // Primary Key UUID
            $table->uuid('id')->primary();

            // Relations
            $table->uuid('submission_id')->index();
            $table->uuid('file_id')->nullable()->index(); // FK to submission_files

            // Galley Properties
            $table->string('label'); // e.g., "PDF", "HTML", "EPUB"
            $table->string('locale')->default('en'); // Language code
            $table->string('url_remote')->nullable(); // For external links (e.g., hosted PDFs)

            // Ordering
            $table->integer('seq')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Index for efficient lookups
            $table->index(['submission_id', 'label']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publication_galleys');
    }
};
