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
        Schema::table('submission_authors', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('user_id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->boolean('is_primary_contact')->default(false)->after('country');
            $table->string('name')->nullable()->change(); // Make original name nullable as we might split it
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submission_authors', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'is_primary_contact']);
            // Reverting nullable change on formatted name is risky if data exists with nulls, skipping change() revert
        });
    }
};
