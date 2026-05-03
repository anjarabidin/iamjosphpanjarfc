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
        Schema::create('review_assignments', function (Blueprint $table) {
            // Primary Key UUID
            $table->uuid('id')->primary();

            // Relasi (tanpa constraint, hanya index)
            $table->uuid('submission_id')->index();
            $table->uuid('reviewer_id')->index(); // User dengan role Reviewer

            // Status Review
            // Enum: pending, accepted, declined, completed, cancelled
            $table->string('status')->default('pending')->index();

            // Rekomendasi Reviewer
            // Enum: accept, minor_revision, major_revision, reject, null
            $table->string('recommendation')->nullable();

            // Review Content
            $table->text('comments_for_author')->nullable();
            $table->text('comments_for_editor')->nullable(); // Private untuk editor
            $table->integer('quality_rating')->nullable(); // 1-5 rating

            // Deadline & Tanggal
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('responded_at')->nullable(); // Saat accept/decline
            $table->timestamp('completed_at')->nullable();

            // Review Round (untuk multiple review rounds)
            $table->integer('round')->default(1);

            // Metadata
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
        Schema::dropIfExists('review_assignments');
    }
};
