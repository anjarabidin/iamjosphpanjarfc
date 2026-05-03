<?php

namespace App\Services;

use App\Models\Journal;
use App\Models\Submission;
use App\Models\CrossrefLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CrossrefDepositService
{
    /**
     * Generate Crossref XML content.
     */
    public function generateXml($submissions, Journal $journal)
    {
        $batchId = '_' . time();
        $content = view('journal.tools.crossref_xml', compact('submissions', 'journal', 'batchId'))->render();

        // Aggressive Cleaning (Remove BOM & Whitespace)
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content); // Remove BOM if present
        $content = trim($content);
        
        // Minify: Remove any whitespaces existing strictly between XML brackets.
        $content = preg_replace('/>\s+</', '><', $content);

        return '<?xml version="1.0" encoding="utf-8"?>' . "\n" . $content;
    }

    /**
     * Send XML to Crossref.
     */
    public function deposit($submissionIds, Journal $journal)
    {
        $hasDepositorInfo = $journal->getSetting('crossref_depositor_name') 
            && $journal->getSetting('crossref_depositor_email') 
            && $journal->getSetting('crossref_username');

        if (!$hasDepositorInfo) {
            return [
                'status' => 'Failed',
                'message' => 'Crossref depositor info or username is missing.'
            ];
        }

        $submissions = Submission::whereIn('id', (array) $submissionIds)
            ->where('journal_id', $journal->id)
            ->with(['authors', 'issue', 'currentPublication'])
            ->get();

        if ($submissions->isEmpty()) {
            return [
                'status' => 'Failed',
                'message' => 'No valid submissions found.'
            ];
        }

        $xmlString = $this->generateXml($submissions, $journal);
        $filename = 'crossref-' . $journal->path . '-' . date('YmdHis') . '.xml';

        $isTestMode = $journal->getSetting('crossref_test_mode');
        
        // API Endpoints
        $url = $isTestMode 
            ? 'https://test.crossref.org/servlet/deposit' 
            : 'https://doi.crossref.org/servlet/deposit';
        
        $username = $journal->getSetting('crossref_username');
        $password = $journal->getSetting('crossref_password') ?? '';

        try {
            $response = Http::attach('fname', $xmlString, $filename)
                ->post($url, [
                    'operation' => 'doMDataUpload',
                    'login_id' => $username,
                    'login_passwd' => $password,
                ]);

            if ($response->successful()) {
                $status = 'Success';
                $message = 'Deposit successful. Crossref Response: ' . $response->body();
                // Depending on Crossref response, you might extract batch_id from the XML response string.
                // It usually returns a generic HTML/XML response stating queued processing.
            } else {
                $status = 'Failed';
                $message = 'HTTP Error ' . $response->status() . ': ' . $response->body();
            }
        } catch (\Exception $e) {
            $status = 'Failed';
            $message = 'Request failed: ' . $e->getMessage();
        }

        // Log the result
        foreach ($submissions as $submission) {
            CrossrefLog::create([
                'id' => (string) Str::uuid(),
                'journal_id' => $journal->id,
                'submission_id' => $submission->id,
                'status' => $status,
                'message' => substr($message, 0, 500),
            ]);
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }
}
