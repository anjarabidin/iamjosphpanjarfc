<?php

namespace App\Jobs;

use App\Models\Submission;
use App\Models\SubmissionIndexStat;
use App\Services\ScholarCheckerService;
use App\Services\WaGateway;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckArticleIndexJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $submissionId;

    /**
     * Create a new job instance.
     */
    public function __construct($submissionId)
    {
        $this->submissionId = $submissionId;
    }

    /**
     * Execute the job.
     */
    public function handle(ScholarCheckerService $checker): void
    {
        // 1. Random Delay to prevent blocking
        $sleepTime = rand(5, 15);
        sleep($sleepTime);

        $submission = Submission::with('journal')->find($this->submissionId);

        if (!$submission) {
            Log::error("CheckArticleIndexJob: Submission not found: {$this->submissionId}");
            return;
        }

        try {
            // 2. Perform Check
            $isIndexed = $checker->isIndexed($submission);
            
            // 3. Get or Create Stat Record
            $stat = SubmissionIndexStat::firstOrCreate(
                ['submission_id' => $submission->id],
                ['journal_id' => $submission->journal_id]
            );

            // 4. Check for De-indexing (Previous was true, current is false)
            // We only alert if it was previously KNOWN to be indexed (is_indexed === true)
            if ($stat->is_indexed === true && $isIndexed === false) {
                $this->notifyJournalManager($submission);
            }

            // 5. Update Stats
            $stat->is_indexed = $isIndexed;
            $stat->last_check_status = $isIndexed ? 'found' : 'not_found';
            $stat->last_checked_at = now();
            // Optional: update scholar_url if found (feature not fully implemented in service yet)
            $stat->save();

        } catch (\Exception $e) {
            Log::error('CheckArticleIndexJob Error', ['error' => $e->getMessage()]);
            
            // Mark as error
            $stat = SubmissionIndexStat::firstOrCreate(
                ['submission_id' => $submission->id],
                ['journal_id' => $submission->journal_id]
            );
            $stat->last_check_status = 'error';
            $stat->last_checked_at = now();
            $stat->save();
        }
    }

    protected function notifyJournalManager(Submission $submission)
    {
        // Find Journal Manager
        $journal = $submission->journal;
        // Assuming 'journal_manager' is the role name
        $manager = $journal->usersWithRole('journal_manager')->first();

        if ($manager && $manager->phone) {
            $message = "⚠️ Warning: Article '{$submission->title}' appears to have dropped from Google Scholar index.";
            WaGateway::send($manager, $message);
            Log::info("ScholarWatchdog: Alert sent to manager for submission {$submission->id}");
        } else {
            Log::warning("ScholarWatchdog: No journal manager with phone found for journal {$journal->id}");
        }
    }
}
