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
        Schema::create('sections', function (Blueprint $table) {
            // Primary Key UUID
            $table->uuid('id')->primary();

            // Relasi ke Journal (tanpa constraint)
            $table->uuid('journal_id')->index();

            // Data Section
            $table->string('name'); // Misal: "Original Articles", "Review Articles"
            $table->string('abbreviation')->nullable(); // Misal: "OA", "RA"
            $table->text('policy')->nullable(); // Kebijakan section
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
