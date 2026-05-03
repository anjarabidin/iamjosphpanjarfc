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
     * 
     * Adds SEO-friendly slug and human-readable submission_code columns.
     * Includes backfill logic for existing records to prevent unique constraint failures.
     */
    public function up(): void
    {
        // Step 1: Add the columns as NULLABLE first (to allow backfill)
        Schema::table('submissions', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
            $table->string('submission_code', 50)->nullable()->after('slug');
        });

        // Step 2: Backfill existing records
        $this->backfillExistingSubmissions();

        // Step 3: Make columns NOT NULL and add unique indexes
        Schema::table('submissions', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->unique()->change();
            $table->string('submission_code', 50)->nullable(false)->unique()->change();

            // Add regular indexes for faster lookups
            $table->index('slug');
            $table->index('submission_code');
        });
    }

    /**
     * Backfill slug and submission_code for existing submissions.
     */
    private function backfillExistingSubmissions(): void
    {
        $submissions = DB::table('submissions')
            ->whereNull('slug')
            ->orWhereNull('submission_code')
            ->orderBy('created_at', 'asc')
            ->get();

        // Track slug usage for uniqueness
        $usedSlugs = [];

        // Track sequence per journal per year
        $sequenceCounters = [];

        foreach ($submissions as $submission) {
            // === Generate Slug ===
            $baseSlug = Str::slug($submission->title ?: 'untitled');
            $slug = $baseSlug;
            $counter = 1;

            // Ensure slug uniqueness (check both DB and our in-memory tracker)
            while (
                in_array($slug, $usedSlugs) ||
                DB::table('submissions')->where('slug', $slug)->where('id', '!=', $submission->id)->exists()
            ) {
                $counter++;
                $slug = $baseSlug . '-' . $counter;
            }
            $usedSlugs[] = $slug;

            // === Generate Submission Code ===
            // Format: [JOURNAL_ABBR]-[YEAR]-[SEQ]
            $journal = DB::table('journals')->where('id', $submission->journal_id)->first();
            $abbreviation = $journal->abbreviation ?? $journal->slug ?? 'SUB';
            $abbreviation = strtoupper(Str::limit($abbreviation, 5, ''));

            $year = date('Y', strtotime($submission->created_at));
            $key = "{$submission->journal_id}_{$year}";

            if (!isset($sequenceCounters[$key])) {
                // Initialize counter based on existing codes for this journal/year
                $existingCount = DB::table('submissions')
                    ->where('journal_id', $submission->journal_id)
                    ->whereNotNull('submission_code')
                    ->whereYear('created_at', $year)
                    ->count();
                $sequenceCounters[$key] = $existingCount;
            }

            $sequenceCounters[$key]++;
            $sequence = str_pad($sequenceCounters[$key], 3, '0', STR_PAD_LEFT);
            $submissionCode = "{$abbreviation}-{$year}-{$sequence}";

            // Ensure code uniqueness
            while (DB::table('submissions')->where('submission_code', $submissionCode)->where('id', '!=', $submission->id)->exists()) {
                $sequenceCounters[$key]++;
                $sequence = str_pad($sequenceCounters[$key], 3, '0', STR_PAD_LEFT);
                $submissionCode = "{$abbreviation}-{$year}-{$sequence}";
            }

            // === Update the record ===
            DB::table('submissions')
                ->where('id', $submission->id)
                ->update([
                    'slug' => $slug,
                    'submission_code' => $submissionCode,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropIndex(['submission_code']);
            $table->dropUnique(['slug']);
            $table->dropUnique(['submission_code']);
            $table->dropColumn(['slug', 'submission_code']);
        });
    }
};
