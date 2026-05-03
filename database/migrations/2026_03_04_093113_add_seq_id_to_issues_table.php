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
        Schema::table('issues', function (Blueprint $table) {
            $table->bigInteger('seq_id')->unsigned()->nullable()->unique();
        });

        // Use PostgreSQL sequence explicitly or let Laravel handle it if possible.
        // A safer way for existing tables is to create a sequence and set default.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE SEQUENCE IF NOT EXISTS issues_seq_id_seq');
            DB::statement("ALTER TABLE issues ALTER COLUMN seq_id SET DEFAULT nextval('issues_seq_id_seq')");
            DB::statement("UPDATE issues SET seq_id = nextval('issues_seq_id_seq') WHERE seq_id IS NULL");
            DB::statement("ALTER TABLE issues ALTER COLUMN seq_id SET NOT NULL");
        } else {
             // Fallback for others if needed (though project uses pgsql)
             // Using a manual approach for PG is safer for adding auto-increments to existing tables.
             // If local uses sqlite/mysql, they won't execute the pgsql block.
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
             DB::statement('ALTER TABLE issues ALTER COLUMN seq_id DROP DEFAULT');
             DB::statement('DROP SEQUENCE IF EXISTS issues_seq_id_seq');
        }

        Schema::table('issues', function (Blueprint $table) {
            $table->dropColumn('seq_id');
        });
    }
};

