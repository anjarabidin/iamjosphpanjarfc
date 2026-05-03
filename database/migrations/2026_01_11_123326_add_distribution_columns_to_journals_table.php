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
            $table->text('license_terms')->nullable();
            $table->string('license_url')->nullable(); // Added proactively based on UI
            $table->string('copyright_holder_type')->nullable();
            $table->string('copyright_year_basis')->nullable()->default('issue');
            $table->text('search_description')->nullable();
            $table->text('custom_headers')->nullable();
            $table->text('open_access_policy')->nullable();
            $table->boolean('enable_oai')->default(false);
            $table->boolean('enable_lockss')->default(false);
            $table->boolean('enable_clockss')->default(false);
            $table->text('archiving_policy')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn([
                'license_terms',
                'license_url',
                'copyright_holder_type',
                'copyright_year_basis',
                'search_description',
                'custom_headers',
                'open_access_policy',
                'enable_oai',
                'enable_lockss',
                'enable_clockss',
                'archiving_policy',
            ]);
        });
    }
};
