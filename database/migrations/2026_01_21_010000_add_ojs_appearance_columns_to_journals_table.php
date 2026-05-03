<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds OJS 3.3 Website Appearance columns to journals table
     */
    public function up(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            // Homepage Image - shown on homepage or as header background
            $table->string('homepage_image_path')->nullable()->after('thumbnail_path');
            
            // Toggle: Show homepage image in header background instead of on page
            $table->boolean('show_homepage_image_in_header')->default(false)->after('homepage_image_path');
            
            // Page Footer - HTML content for footer area (from TinyMCE/CKEditor)
            $table->text('page_footer')->nullable()->after('show_homepage_image_in_header');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn([
                'homepage_image_path',
                'show_homepage_image_in_header',
                'page_footer',
            ]);
        });
    }
};
