<?php

namespace App\Jobs;

use App\Models\MalwareFinding;
use App\Models\MalwareScan;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class ProcessFileBatchJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;

    protected $scanId;
    protected $files;

    public function __construct(string $scanId, array $files)
    {
        $this->scanId = $scanId;
        $this->files = $files;
    }

    public function handle()
    {
        \Illuminate\Support\Facades\Log::info("ProcessFileBatchJob started for Scan ID: {$this->scanId}, Files: " . count($this->files));
        
        $scan = MalwareScan::find($this->scanId);
        if (!$scan) {
            return;
        }

        $signatures = Config::get('malware.signatures', []);
        $whitelist = Config::get('malware.whitelist_files', []);
        $processedCount = 0;

        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        foreach ($this->files as $filePath) {
            // Check if scan was cancelled
            if ($processedCount % 5 === 0) {
                if ($this->batch() && $this->batch()->cancelled()) {
                     return;
                }
            }
            
            $processedCount++;

             // Check whitelist
            $isWhitelisted = false;
            foreach ($whitelist as $whitelistedFile) {
                if (str_ends_with($filePath, $whitelistedFile)) {
                    $isWhitelisted = true;
                    break;
                }
            }

            if ($isWhitelisted) {
                continue;
            }

            try {
                if (!file_exists($filePath) || !is_readable($filePath)) {
                    continue;
                }
                
                // 1. Extension Whitelist Check
                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                $allowedExtensions = ['php', 'phtml', 'html', 'js', 'htaccess', 'env'];
                if (!in_array($extension, $allowedExtensions)) {
                    // Skip files not in the allowed list to prevent scanning binaries/media
                    continue;
                }

                // 2. Strict Size Check (1MB Limit to prevent memory exhaustion)
                $fileSize = @filesize($filePath);
                if ($fileSize === false || $fileSize > 1 * 1024 * 1024) { 
                     // \Illuminate\Support\Facades\Log::warning("Skipping large file: {$filePath}");
                    continue;
                }

                // 3. Mime-Type Check (Safety Net against renamed binaries)
                $mimeType = @mime_content_type($filePath);
                $isText = false;
                if ($mimeType) {
                    if (str_starts_with($mimeType, 'text/') || 
                        $mimeType === 'application/x-php' || 
                        $mimeType === 'application/javascript' ||
                        $mimeType === 'application/json' ||
                        $mimeType === 'application/xml') {
                        $isText = true;
                    }
                }
                
                if (!$isText) {
                     // Last resort: Check first 512 bytes for null byte
                     $handle = @fopen($filePath, 'r');
                    if ($handle) {
                        $bytes = fread($handle, 512);
                        fclose($handle);
                        if (strpos($bytes, "\0") !== false) {
                             continue; // Is Binary
                        }
                    } else {
                        continue; // Cannot read
                    }
                }
                
                // If we passed all checks, scan it!
                $content = @file_get_contents($filePath);
                if ($content === false) {
                    continue;
                }
                
                foreach ($signatures as $signature) {
                    if (@preg_match($signature, $content, $matches)) {
                        MalwareFinding::create([
                            'scan_id' => $scan->id,
                            'file_path' => \Illuminate\Support\Str::limit($filePath, 250),
                            'threat_name' => \Illuminate\Support\Str::limit('Suspicious Pattern: ' . $signature, 250),
                            'snippet' => isset($matches[0]) ? substr($matches[0], 0, 1000) : null,
                            'action_taken' => 'none',
                        ]);

                        $scan->increment('threats_count');
                        break; // Stop checking signatures for this file if one is found
                    }
                }
            } catch (\Throwable $e) {
                // Log warning but continue
                \Illuminate\Support\Facades\Log::warning("Error scanning file {$filePath}: " . $e->getMessage());
                continue;
            }
        }

        // Bulk update processed count
        if ($processedCount > 0) {
            $scan->increment('processed_files', $processedCount);
        }

        // Check if scan is complete
        // We use fresh instance to get latest count from DB
        $currentScan = MalwareScan::find($this->scanId);
        if ($currentScan && $currentScan->processed_files >= $currentScan->total_files) {
            $currentScan->update(['status' => 'completed']);
        }
    }
}
