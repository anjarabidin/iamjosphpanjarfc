<?php

namespace App\Console\Commands;

use App\Models\ReviewAssignment;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BackfillReviewerIds extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'reviewer:backfill-ids
                            {--dry-run : Preview changes without persisting to database}';

    /**
     * The console command description.
     */
    protected $description = 'Backfill all review_assignments slugs to the new REV-YYYY-XXXXX academic format';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        $assignments = ReviewAssignment::withTrashed()
            ->whereNull('slug')
            ->orWhere('slug', 'not like', 'REV-%')
            ->get();

        if ($assignments->isEmpty()) {
            $this->info('All reviewer IDs are already in the correct format.');
            return self::SUCCESS;
        }

        $this->info("Found {$assignments->count()} record(s) to update." . ($isDryRun ? ' [DRY RUN]' : ''));

        $bar = $this->output->createProgressBar($assignments->count());
        $bar->start();

        foreach ($assignments as $assignment) {
            // Generate a unique REV-YYYY-XXXXX slug
            do {
                $newSlug = 'REV-' . now()->year . '-' . Str::upper(Str::random(5));
            } while (ReviewAssignment::withTrashed()->where('slug', $newSlug)->exists());

            if ($isDryRun) {
                $this->newLine();
                $this->line("  [{$assignment->id}] {$assignment->slug} → <comment>{$newSlug}</comment>");
            } else {
                // Use withTrashed query to bypass SoftDeletes on update
                ReviewAssignment::withTrashed()
                    ->where('id', $assignment->id)
                    ->update(['slug' => $newSlug]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        if (!$isDryRun) {
            $this->info("✓ Successfully updated {$assignments->count()} reviewer ID(s) to REV-YYYY-XXXXX format.");
        }

        return self::SUCCESS;
    }
}
