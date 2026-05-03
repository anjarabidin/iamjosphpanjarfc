<?php

namespace App\Jobs;

use App\Models\MalwareScan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Symfony\Component\Finder\Finder;
use Throwable;

class ScanInitiatorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle()
    {
        \Illuminate\Support\Facades\Log::info('ScanInitiatorJob started');

        $scan = MalwareScan::create([
            'status' => MalwareScan::STATUS_SCANNING,
            'total_files' => 0,
            'processed_files' => 0,
            'threats_count' => 0,
        ]);

        $finder = new Finder();
        $finder->files()
            ->in(public_path())
            ->followLinks()
            ->ignoreDotFiles(true);

        $files = [];
        foreach ($finder as $file) {
            $files[] = $file->getRealPath();
        }

        $scan->update(['total_files' => count($files)]);

        $chunks = array_chunk($files, 20); // Small chunks to prevent timeouts
        $jobs = [];

        foreach ($chunks as $chunk) {
            $jobs[] = new ProcessFileBatchJob((string) $scan->id, $chunk);
        }
        
        if (empty($jobs)) {
            $scan->update(['status' => MalwareScan::STATUS_COMPLETED]);
            return;
        }

        $batch = Bus::batch($jobs)
            ->then(function (Batch $batch) use ($scan) {
                $scan->update(['status' => MalwareScan::STATUS_COMPLETED]);
            })
            ->catch(function (Batch $batch, Throwable $e) use ($scan) {
                $scan->update(['status' => MalwareScan::STATUS_FAILED]);
                \Illuminate\Support\Facades\Log::error('Malware scan batch failed: ' . $e->getMessage());
            })
            ->finally(function (Batch $batch) use ($scan) {
                // Optional: Cleanup or final logging
            })
            ->dispatch();

        $scan->update(['batch_id' => $batch->id]);
    }
}
