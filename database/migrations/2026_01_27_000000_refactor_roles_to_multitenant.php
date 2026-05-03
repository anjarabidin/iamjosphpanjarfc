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
        $tableNames = config('permission.table_names');

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            // Drop the old unique constraint (name, guard_name)
            // Default index name is table_column_unique
            $table->dropUnique('roles_name_guard_name_unique');

            // Add new columns
            $table->uuid('journal_id')->nullable()->after('id');
            $table->string('slug')->nullable()->after('name');
            $table->boolean('is_system')->default(false)->after('permission_level');

            // Add Foreign Key
            $table->foreign('journal_id')
                  ->references('id')
                  ->on('journals')
                  ->onDelete('cascade');

            // Add new unique constraint
            // Note: In standard SQL, (name, guard_name, NULL) is NOT unique-constrained against other NULLs.
            // However, to mimic OJS 3.3, scoped roles must be unique per journal.
            $table->unique(['name', 'guard_name', 'journal_id']);
        });

        // Optional: For Postgres, to ensure Global Roles (journal_id = NULL) are also unique
        if (DB::getDriverName() === 'pgsql') {
             // Create partial index for null journal_id
             DB::statement('CREATE UNIQUE INDEX roles_name_guard_name_global_unique ON roles (name, guard_name) WHERE journal_id IS NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement('DROP INDEX IF EXISTS roles_name_guard_name_global_unique');
            }
            $table->dropForeign(['journal_id']);
            $table->dropUnique(['name', 'guard_name', 'journal_id']);
            $table->dropColumn(['journal_id', 'slug', 'is_system']);
            
            // Restore old unique constraint
            $table->unique(['name', 'guard_name']);
        });
    }
};
