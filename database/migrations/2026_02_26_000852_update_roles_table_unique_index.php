<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    if (DB::getDriverName() === 'sqlite') {
        return;
    }

    // 1. Cek apakah indeks baru sudah ada di sistem PostgreSQL
    $indexExists = DB::select("
        SELECT 1 FROM pg_indexes 
        WHERE tablename = 'roles' 
        AND indexname = 'roles_name_guard_name_journal_id_unique'
    ");

    Schema::table('roles', function (Blueprint $table) use ($indexExists) {
        // 2. Hapus constraint lama Spatie jika masih ada secara aman
        DB::statement('ALTER TABLE roles DROP CONSTRAINT IF EXISTS roles_name_guard_name_unique');

        // 3. Hanya tambahkan jika indeks belum terdaftar di database
        if (empty($indexExists)) {
            $table->unique(['name', 'guard_name', 'journal_id'], 'roles_name_guard_name_journal_id_unique');
        }
    });
}

public function down(): void
{
    if (DB::getDriverName() === 'sqlite') {
        return;
    }

    Schema::table('roles', function (Blueprint $table) {
        // Balikkan perubahan dengan aman
        DB::statement('ALTER TABLE roles DROP CONSTRAINT IF EXISTS roles_name_guard_name_journal_id_unique');
        
        // Cek apakah constraint global perlu dipasang kembali
        $globalExists = DB::select("SELECT 1 FROM pg_indexes WHERE indexname = 'roles_name_guard_name_unique'");
        if (empty($globalExists)) {
            $table->unique(['name', 'guard_name'], 'roles_name_guard_name_unique');
        }
    });
}
};
