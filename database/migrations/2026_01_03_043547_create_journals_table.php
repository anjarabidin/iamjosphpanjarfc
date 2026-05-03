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
        Schema::create('journals', function (Blueprint $table) {
            // 1. Primary Key UUID (Wajib agar sinkron dengan tabel users & roles)
            $table->uuid('id')->primary();

            // 2. Identitas Utama (Wajib diisi)
            $table->string('name'); // Nama Jurnal, misal: "Jurnal Teknologi Informasi"
            $table->string('path')->unique(); // Slug URL, misal: 'jti' -> domain.com/jti
            $table->string('abbreviation')->nullable(); // Singkatan, misal: "JTI"

            // 3. Deskripsi & Metadata
            $table->text('description')->nullable(); // Deskripsi lengkap jurnal
            $table->string('publisher')->nullable(); // Nama institusi penerbit
            $table->string('issn_print')->nullable();
            $table->string('issn_online')->nullable();

            // 4. Status Jurnal
            $table->boolean('enabled')->default(true); // Bisa dimatikan sementara oleh Super Admin
            $table->boolean('visible')->default(true); // Tampil di halaman depan portal atau tersembunyi

            // 5. Branding (Path ke storage)
            $table->string('logo_path')->nullable(); // Logo header
            $table->string('thumbnail_path')->nullable(); // Cover kecil untuk list jurnal

            // 6. THE MAGIC COLUMN (Pengganti tabel journal_settings OJS)
            // Di sini kita simpan: Contact info, Mailing Address, Policies, Guidelines, Theme Colors
            $table->jsonb('settings')->nullable();

            // 7. Standard Timestamps & Soft Deletes
            $table->timestamps();
            $table->softDeletes(); // Agar kalau terhapus tidak hilang permanen
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
