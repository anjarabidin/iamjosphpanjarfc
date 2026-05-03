<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds url_path and is_remote fields to support:
     * - Custom SEO-friendly URLs (e.g., /article/{slug}/pdf)
     * - Remote/external galley files hosted on separate websites
     */
    public function up(): void
    {
        Schema::table('publication_galleys', function (Blueprint $table) {
            // Custom URL path (e.g., 'pdf', 'html-fulltext') - unique per submission
            $table->string('url_path')->nullable()->after('locale');

            // Flag to indicate remote galley (external URL)
            $table->boolean('is_remote')->default(false)->after('url_remote');

            // Unique constraint: url_path must be unique within the same submission
            $table->unique(['submission_id', 'url_path'], 'galleys_submission_url_path_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publication_galleys', function (Blueprint $table) {
            $table->dropUnique('galleys_submission_url_path_unique');
            $table->dropColumn(['url_path', 'is_remote']);
        });
    }
};
