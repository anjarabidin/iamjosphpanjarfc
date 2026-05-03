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
        Schema::table('issues', function (Blueprint $table) {
            $table->boolean('show_volume')->default(true)->after('year');
            $table->boolean('show_number')->default(true)->after('show_volume');
            $table->boolean('show_year')->default(true)->after('show_number');
            $table->boolean('show_title')->default(false)->after('show_year');
            $table->longText('description')->nullable()->after('title');
            $table->string('url_path')->nullable()->after('cover_path');
            
            // Ensure url_path is unique per journal
            $table->unique(['journal_id', 'url_path']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->dropUnique(['journal_id', 'url_path']);
            $table->dropColumn([
                'show_volume',
                'show_number',
                'show_year',
                'show_title',
                'description',
                'url_path',
            ]);
        });
    }
};
