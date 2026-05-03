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
        Schema::create('issues', function (Blueprint $table) {
            // Primary Key UUID
            $table->uuid('id')->primary();

            // Relasi ke Journal (tanpa constraint)
            $table->uuid('journal_id')->index();

            // Identitas Issue
            $table->integer('volume');
            $table->integer('number'); // Issue number dalam volume
            $table->integer('year');
            $table->string('title')->nullable(); // Judul khusus jika ada (misal: Special Edition)

            // Status Publikasi
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();

            // Cover Issue
            $table->string('cover_path')->nullable();

            // Metadata fleksibel
            $table->jsonb('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Composite index untuk pencarian cepat
            $table->index(['journal_id', 'volume', 'number', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
