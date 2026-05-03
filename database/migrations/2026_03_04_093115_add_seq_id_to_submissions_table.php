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
        Schema::table('submissions', function (Blueprint $table) {
            $table->bigInteger('seq_id')->unsigned()->nullable()->unique();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE SEQUENCE IF NOT EXISTS submissions_seq_id_seq');
            DB::statement("ALTER TABLE submissions ALTER COLUMN seq_id SET DEFAULT nextval('submissions_seq_id_seq')");
            DB::statement("UPDATE submissions SET seq_id = nextval('submissions_seq_id_seq') WHERE seq_id IS NULL");
            DB::statement("ALTER TABLE submissions ALTER COLUMN seq_id SET NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
             DB::statement('ALTER TABLE submissions ALTER COLUMN seq_id DROP DEFAULT');
             DB::statement('DROP SEQUENCE IF EXISTS submissions_seq_id_seq');
        }

        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn('seq_id');
        });
    }
};

