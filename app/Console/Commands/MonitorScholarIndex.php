<?php

namespace App\Console\Commands;

use App\Jobs\CheckArticleIndexJob;
use App\Models\Submission;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class MonitorScholarIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scholar:monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor Google Scholar indexing status for published articles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Scholar Watchdog...');

        // Get 10 submissions that are published AND explicitly monitored AND (never checked OR checked > 7 days ago)
        $submissions = Submission::where('status', 'published')
            ->whereHas('indexStat', function (Builder $q) {
                // Must be explicitly monitored
                $q->where('is_monitored', true)
                  ->where(function ($subQ) {
                      // AND (Checked > 7 days ago OR Check failure/null)
                      // Logic: If monitored, we assume a stat record exists. 
                      // Check timestamp.
                      $subQ->where('last_checked_at', '<', now()->subDays(7))
                           ->orWhereNull('last_checked_at');
                  });
            })
            ->limit(10)
            ->get();

        if ($submissions->isEmpty()) {
            $this->info('No submissions need checking right now.');
            return;
        }

        $this->info("Found {$submissions->count()} submissions to check.");

        foreach ($submissions as $index => $submission) {
            // Stagger jobs 2 minutes apart to avoid traffic spikes
            $delay = now()->addMinutes($index * 2);
            
            CheckArticleIndexJob::dispatch($submission->id)
                ->delay($delay);

            $this->info("Scheduled check for '{$submission->title}' at {$delay->toTimeString()}");
        }
    }
}
