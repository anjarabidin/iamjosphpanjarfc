<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Fill missing url_path values in issues table
        $issues = DB::table('issues')->whereNull('url_path')->orWhere('url_path', '')->get();

        foreach ($issues as $issue) {
            // Priority 1: Use Slugified Title if available and show_title is true
            if (!empty($issue->title)) {
                $baseSlug = Str::slug($issue->title);
            } else {
                // Priority 2: Use v{volume}-n{number}-{year}
                $baseSlug = "v{$issue->volume}-n{$issue->number}-{$issue->year}";
            }

            $slug = $baseSlug;
            $counter = 1;

            // Ensure uniqueness within the same journal
            while (DB::table('issues')
                ->where('journal_id', $issue->journal_id)
                ->where('url_path', $slug)
                ->where('id', '!=', $issue->id)
                ->exists()) {
                $counter++;
                $slug = $baseSlug . '-' . $counter;
            }

            DB::table('issues')->where('id', $issue->id)->update([
                'url_path' => $slug
            ]);
        }

        // 2. Add Unique constraint if it does not exist
        try {
            Schema::table('issues', function (Blueprint $table) {
                // Using a composite unique index so url_path is unique per journal
                $table->unique(['journal_id', 'url_path'], 'issues_journal_id_url_path_unique');
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Index likely already exists, ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->dropUnique('issues_journal_id_url_path_unique');
        });
    }
};
