<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds OJS 3.3 Public Profile fields to users table
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Homepage URL
            if (!Schema::hasColumn('users', 'homepage')) {
                $table->string('homepage', 500)->nullable()->after('orcid_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['homepage']);
        });
    }
};
