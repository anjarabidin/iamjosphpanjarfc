<?php

namespace App\Jobs;

use App\Services\OaiHarvesterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportOaiBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hour timeout per job/page

    protected $url;
    protected $journalId;
    protected $sectionId;
    protected $userId;
    protected $token;

    /**
     * Create a new job instance.
     *
     * @param string $url
     * @param int $journalId
     * @param int $sectionId
     * @param int $userId
     * @param string|null $token
     */
    public function __construct(string $url, $journalId, $sectionId, $userId, ?string $token = null)
    {
        $this->url = $url;
        $this->journalId = $journalId;
        $this->sectionId = $sectionId;
        $this->userId = $userId;
        $this->token = $token;
    }

    /**
     * Execute the job.
     */
    public function handle(OaiHarvesterService $service): void
    {
        Log::info("Starting OAI Batch Import for URL: {$this->url}, Token: " . ($this->token ?? 'Initial'));

        try {
            // Fetch records using service
            $xml = $service->fetchRecords($this->url, $this->token);

            // Process Records
            $count = 0;
            if (isset($xml->ListRecords->record)) {
                foreach ($xml->ListRecords->record as $record) {
                    if (isset($record->header['status']) && (string)$record->header['status'] === 'deleted') {
                        continue;
                    }

                    try {
                        $journal = \App\Models\Journal::find($this->journalId);
                        if ($journal) {
                            $service->importRecord($record, $journal, $this->sectionId, $this->userId);
                            $count++;
                        } else {
                             Log::error("Journal ID {$this->journalId} not found during OAI import job.");
                        }
                    } catch (\Exception $e) {
                         Log::error("Failed to import individual record: " . $e->getMessage());
                    }
                }
            }
            
            Log::info("Imported {$count} records in this batch.");

            // Check for Resumption Token
            if (isset($xml->ListRecords->resumptionToken) && (string)$xml->ListRecords->resumptionToken !== '') {
                $nextToken = (string)$xml->ListRecords->resumptionToken;
                
                // Dispatch next batch
                // Delay slightly to be nice to the source server
                self::dispatch($this->url, $this->journalId, $this->sectionId, $this->userId, $nextToken)
                    ->delay(now()->addSeconds(2));
                
                Log::info("Dispatched next batch with token: {$nextToken}");
            } else {
                Log::info("OAI Import completed.");
            }

        } catch (\Exception $e) {
            Log::error("OAI Import Job Failed: " . $e->getMessage());
            // Optionally release the job back to the queue
            // $this->release(60); 
            // But if it's a fatal parse error, better to fail.
            $this->fail($e);
        }
    }
}
