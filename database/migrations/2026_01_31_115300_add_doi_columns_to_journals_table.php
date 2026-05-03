<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds DOI (Digital Object Identifier) settings columns to journals table.
     * Implements OJS 3.3 DOI Plugin functionality:
     * - doi_enabled: Master toggle for DOI feature
     * - doi_objects: JSON array specifying which objects get DOIs (issues, articles, galleys)
     * - doi_prefix: DOI prefix (must start with "10.")
     * - doi_suffix_type: How suffixes are generated (default, manual, custom_pattern)
     * - doi_custom_pattern: Custom pattern for suffix generation
     */
    public function up(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            // DOI Enabled - Master toggle for DOI feature
            $table->boolean('doi_enabled')->default(false)->after('settings');
            
            // DOI Objects - Which objects should have DOIs assigned
            // Stores JSON array like: ['issues', 'articles', 'galleys']
            $table->json('doi_objects')->nullable()->after('doi_enabled');
            
            // DOI Prefix - Format: 10.xxxx (assigned by DOI registration agency)
            $table->string('doi_prefix')->nullable()->after('doi_objects');
            
            // DOI Suffix Type - How suffix is generated
            // Values: 'default', 'manual', 'custom_pattern'
            $table->enum('doi_suffix_type', ['default', 'manual', 'custom_pattern'])
                  ->default('default')
                  ->after('doi_prefix');
            
            // DOI Custom Pattern - Used when doi_suffix_type = 'custom_pattern'
            // Placeholders: %j (journal path), %v (volume), %i (issue), %Y (year), %a (article id)
            $table->string('doi_custom_pattern')->nullable()->after('doi_suffix_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn([
                'doi_enabled',
                'doi_objects',
                'doi_prefix',
                'doi_suffix_type',
                'doi_custom_pattern',
            ]);
        });
    }
};
