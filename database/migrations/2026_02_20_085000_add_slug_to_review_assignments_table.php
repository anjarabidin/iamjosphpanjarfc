<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('review_assignments', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('id');
        });

        // Backfill existing records with unique slugs
        \DB::table('review_assignments')->orderBy('id')->each(function ($row) {
            $slug = 'rv-' . Str::random(8);
            // Ensure uniqueness during backfill
            while (\DB::table('review_assignments')->where('slug', $slug)->exists()) {
                $slug = 'rv-' . Str::random(8);
            }
            \DB::table('review_assignments')->where('id', $row->id)->update(['slug' => $slug]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('review_assignments', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
