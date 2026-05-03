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
        Schema::table('journals', function (Blueprint $table) {
            if (!Schema::hasColumn('journals', 'summary')) {
                $table->longText('summary')->nullable()->after('description');
            }
            if (!Schema::hasColumn('journals', 'show_summary')) {
                $table->boolean('show_summary')->default(false)->after('summary');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn(['summary', 'show_summary']);
        });
    }
};
