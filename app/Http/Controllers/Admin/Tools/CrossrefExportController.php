<?php

namespace App\Http\Controllers\Admin\Tools;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\Submission;
use Illuminate\Http\Request;

class CrossrefExportController extends Controller
{
    /**
     * Display the OJS 3.3 Style Interface
     */
    public function index(Request $request, $journalPath)
    {
        $journal = Journal::where('path', $journalPath)->firstOrFail();
        
        // 1. Filter Logic (OJS Style)
        $status = $request->input('status', 'not_deposited'); // Default OJS usually shows 'not_deposited'
        $tab = $request->input('tab', 'settings');

        // 2. Base Query
        $query = Submission::where('journal_id', $journal->id)
            ->where('status', Submission::STATUS_PUBLISHED) // Only Published Articles
            ->with(['authors', 'issue', 'currentPublication']);

        // 3. Apply Status Filter (Simulated Logic)
        if ($status == 'not_deposited') {
            // Logic: Articles that don't have a 'registered' flag yet
            // $query->whereNull('doi_status'); 
        } elseif ($status == 'active') {
            // $query->where('doi_status', 'active');
        }

        // 4. Pagination
        $submissions = $query->latest('published_at')->paginate(20);

        return view('journal.tools.crossref_index', compact('journal', 'submissions', 'status', 'tab'));
    }

    // 2. Save Settings Logic
    public function saveSettings(Request $request, $journalPath)
    {
        $journal = Journal::where('path', $journalPath)->firstOrFail();

        $validated = $request->validate([
            'depositor_name' => 'required|string|max:255',
            'depositor_email' => 'required|email|max:255',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'automatic_deposit' => 'sometimes|boolean',
            'test_mode' => 'sometimes|boolean',
        ]);

        $journal->setSetting('crossref_depositor_name', $validated['depositor_name']);
        $journal->setSetting('crossref_depositor_email', $validated['depositor_email']);
        $journal->setSetting('crossref_username', $validated['username']);
        
        if (!empty($validated['password'])) {
             $journal->setSetting('crossref_password', $validated['password']);
        }

        $journal->setSetting('crossref_automatic_deposit', $request->boolean('automatic_deposit'));
        $journal->setSetting('crossref_test_mode', $request->boolean('test_mode'));
        $journal->save();

        return redirect()->back()->with('success', 'Crossref settings saved successfully.');
    }

    // 2. XML Export Logic
    public function export(Request $request, $journalPath)
    {
        // 1. Fetch Data
        $journal = \App\Models\Journal::where('path', $journalPath)->firstOrFail();
        $ids = $request->input('submission_ids', []);

        if (empty($ids)) {
            return back()->with('error', 'Please select at least one article.');
        }

        $submissions = \App\Models\Submission::whereIn('id', $ids)
            ->where('journal_id', $journal->id)
            ->with(['authors', 'issue', 'currentPublication'])
            ->get();

        $batchId = '_' . time();
        $filename = 'crossref-' . $journal->path . '-' . date('YmdHis') . '.xml';

        // 2. Render View (Without XML Header)
        $content = view('journal.tools.crossref_xml', compact('submissions', 'journal', 'batchId'))->render();

        // 3. Aggressive Cleaning (Remove BOM & Whitespace)
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content); // Remove BOM if present
        $content = trim($content);
        
        // Minify: Remove any whitespaces existing strictly between XML brackets.
        $content = preg_replace('/>\s+</', '><', $content);

        // 4. Construct Final XML
        $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n" . $content;

        // 5. Clean System Buffer
        // Discard unwanted whitespace/newlines from system/config files
        if (ob_get_length()) ob_clean();

        // 6. Return Response
        return response($xml, 200, [
            'Content-Type' => 'text/xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // 4. API Deposit Logic
    public function deposit(Request $request, $journalPath)
    {
        $journal = \App\Models\Journal::where('path', $journalPath)->firstOrFail();
        $ids = $request->input('submission_ids', []);

        if (empty($ids)) {
            return back()->with('error', 'Please select at least one article for deposit.');
        }

        $hasDepositorInfo = $journal->getSetting('crossref_depositor_name') 
            && $journal->getSetting('crossref_depositor_email') 
            && $journal->getSetting('crossref_username');

        if (!$hasDepositorInfo) {
            return back()->with('error', 'Crossref username and depositor information must be configured first.');
        }

        // Check if there are DOIs assigned
        $submissions = \App\Models\Submission::whereIn('id', $ids)
            ->where('journal_id', $journal->id)
            ->with(['currentPublication'])
            ->get();
            
        $invalidCount = 0;
        foreach ($submissions as $sub) {
            if (!$sub->currentPublication || empty($sub->currentPublication->doi)) {
                $invalidCount++;
            }
        }
        
        if ($invalidCount > 0 && $invalidCount == $submissions->count()) {
             return back()->with('error', 'None of the selected articles have DOIs assigned.');
        }

        // For large numbers, we dispatch to Job, otherwise process here or dispatch anyway.
        // Let's use the Job pattern for UI action to avoid slow API hanging the server.
        \App\Jobs\DepositCrossrefJob::dispatch($ids, $journal);

        return back()->with('success', 'Selected articles have been queued for Crossref deposit.');
    }
}
