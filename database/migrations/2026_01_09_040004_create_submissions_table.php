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
        Schema::create('submissions', function (Blueprint $table) {
            // Primary Key UUID
            $table->uuid('id')->primary();

            // Relasi (tanpa constraint, hanya index)
            $table->uuid('journal_id')->index();
            $table->uuid('user_id')->index(); // Author utama
            $table->uuid('section_id')->index();
            $table->uuid('issue_id')->nullable()->index(); // Null sampai di-assign ke issue

            // Konten Artikel
            $table->string('title');
            $table->text('abstract')->nullable();
            $table->text('keywords')->nullable(); // Comma-separated atau bisa pakai jsonb

            // Status Workflow
            // Enum: draft, submitted, in_review, revision_required, accepted, rejected, published
            $table->string('status')->default('draft')->index();
            $table->string('stage')->default('submission')->index(); // submission, review, copyediting, production

            // Tanggal Penting
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('published_at')->nullable();

            // Metadata fleksibel (DOI, page numbers, dll)
            $table->jsonb('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
