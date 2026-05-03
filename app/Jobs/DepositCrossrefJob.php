<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\Journal;
use App\Services\CrossrefDepositService;

class DepositCrossrefJob implements ShouldQueue
{
    use Queueable;

    public $submissionIds;
    public $journal;

    /**
     * Create a new job instance.
     */
    public function __construct($submissionIds, Journal $journal)
    {
        $this->submissionIds = $submissionIds;
        $this->journal = $journal;
    }

    /**
     * Execute the job.
     */
    public function handle(CrossrefDepositService $service): void
    {
        $service->deposit($this->submissionIds, $this->journal);
    }
}
