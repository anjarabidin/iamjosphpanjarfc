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
        Schema::create('categories', function (Blueprint $table) {
            // Primary Key UUID
            $table->uuid('id')->primary();

            // Relasi ke Journal
            $table->uuid('journal_id')->index();

            // Category Data
            $table->string('name'); // e.g., "Computer Science"
            $table->string('path')->index(); // Slug, e.g., "cs"
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Unique constraint: path must be unique per journal
            $table->unique(['journal_id', 'path']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
