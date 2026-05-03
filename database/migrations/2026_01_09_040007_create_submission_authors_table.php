<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabel pivot untuk multiple authors per submission
     * Karena satu artikel bisa punya banyak penulis
     */
    public function up(): void
    {
        Schema::create('submission_authors', function (Blueprint $table) {
            // Primary Key UUID
            $table->uuid('id')->primary();

            // Relasi (tanpa constraint)
            $table->uuid('submission_id')->index();
            $table->uuid('user_id')->nullable()->index(); // Null jika author belum terdaftar

            // Data Author (untuk author yang belum terdaftar di sistem)
            $table->string('name');
            $table->string('email');
            $table->string('affiliation')->nullable();
            $table->string('country')->nullable();
            $table->string('orcid')->nullable(); // ORCID iD

            // Role & Order
            $table->boolean('is_corresponding')->default(false);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            // Unique constraint: satu user hanya bisa jadi author sekali per submission
            $table->unique(['submission_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_authors');
    }
};
