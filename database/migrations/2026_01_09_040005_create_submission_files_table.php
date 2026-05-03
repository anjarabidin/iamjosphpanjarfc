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
        Schema::create('submission_files', function (Blueprint $table) {
            // Primary Key UUID
            $table->uuid('id')->primary();

            // Relasi ke Submission (tanpa constraint)
            $table->uuid('submission_id')->index();
            $table->uuid('uploaded_by')->nullable()->index(); // User yang upload

            // Data File
            $table->string('file_path');
            $table->string('file_name'); // Original filename
            $table->string('file_type'); // manuscript, revision, supplementary, galley
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable(); // Dalam bytes

            // Versioning
            $table->integer('version')->default(1);
            $table->string('stage')->default('submission'); // submission, review, copyediting, production

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
        Schema::dropIfExists('submission_files');
    }
};
