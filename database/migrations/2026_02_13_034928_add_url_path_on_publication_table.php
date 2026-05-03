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
        if (!Schema::hasColumn('publications', 'url_path')) {
            Schema::table('publications', function (Blueprint $table) {
                $table->string('url_path')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Assuming we only drop it if this specific migration added it, 
        // but since it might have been in the original create migration, it's safer not to drop it here
        // or check for it. Let's just wrap it.
        if (Schema::hasColumn('publications', 'url_path')) {
            Schema::table('publications', function (Blueprint $table) {
                $table->dropColumn('url_path');
            });
        }
    }
};
