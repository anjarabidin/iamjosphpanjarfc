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
        Schema::table('users', function (Blueprint $table) {
            $table->string('affiliation')->nullable()->after('password'); // Institusi/Universitas
            $table->string('country')->nullable()->after('affiliation');
            $table->text('bio')->nullable()->after('country');
            $table->string('phone')->nullable()->after('bio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['affiliation', 'country', 'bio', 'phone']);
        });
    }
};
