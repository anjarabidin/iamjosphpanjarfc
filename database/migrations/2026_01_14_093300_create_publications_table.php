<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Publications table (OJS 3.3 style)
     * Each submission can have multiple publication versions
     */
    public function up(): void
    {
        Schema::create('publications', function (Blueprint $table) {
            // Primary Key UUID
            $table->uuid('id')->primary();

            // Relations
            $table->uuid('submission_id')->index();
            $table->uuid('section_id')->nullable()->index();
            $table->uuid('issue_id')->nullable()->index();

            // Versioning
            $table->integer('version')->default(1);

            // Status: 1=queued, 2=scheduled, 3=published, 4=unpublished
            $table->tinyInteger('status')->default(1)->index();

            // Content
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('abstract')->nullable();
            $table->text('keywords')->nullable(); // Comma-separated or JSON

            // Pagination
            $table->string('pages')->nullable(); // e.g., "10-25"
            $table->string('url_path')->nullable(); // Custom URL slug

            // Identifiers
            $table->string('doi')->nullable()->index();
            $table->string('doi_suffix')->nullable();

            // Copyright & License
            $table->string('copyright_holder')->nullable();
            $table->year('copyright_year')->nullable();
            $table->string('license_url')->nullable();

            // Dates
            $table->date('date_published')->nullable();

            // Metadata (JSON for flexibility)
            $table->jsonb('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index for efficient lookups
            $table->index(['submission_id', 'version']);
            $table->unique(['submission_id', 'version']);
        });

        // Add additional fields to submission_authors for OJS compatibility
        Schema::table('submission_authors', function (Blueprint $table) {
            $table->uuid('publication_id')->nullable()->after('submission_id')->index();
            $table->string('given_name')->nullable()->after('name');
            $table->string('family_name')->nullable()->after('given_name');
            $table->string('preferred_public_name')->nullable()->after('family_name');
            $table->string('url')->nullable()->after('orcid');
            $table->text('biography')->nullable()->after('url');
            $table->boolean('include_in_browse')->default(true)->after('is_corresponding');
            $table->string('user_group_id')->nullable()->after('include_in_browse'); // Role type
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submission_authors', function (Blueprint $table) {
            $table->dropColumn([
                'publication_id',
                'given_name',
                'family_name',
                'preferred_public_name',
                'url',
                'biography',
                'include_in_browse',
                'user_group_id',
            ]);
        });

        Schema::dropIfExists('publications');
    }
};
