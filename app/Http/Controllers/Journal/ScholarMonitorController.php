<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Jobs\CheckArticleIndexJob;
use App\Models\Submission;
use App\Models\SubmissionIndexStat;
use Illuminate\Http\Request;

class ScholarMonitorController extends Controller
{
    /**
     * Display the Scholar Watchdog dashboard (UptimeRobot Style).
     */
    public function index()
    {
        $journal = current_journal();

        // 1. Fetch Published Submissions with their Stat
        // Ordered by latest check first (so problem items might pop up if ordered by status, 
        // but 'latest check' is good for monitoring activity. 
        // Or maybe order by 'is_indexed' asc to show errors first?)
        // Let's order by published_at desc for now, or add a sort filter in UI later.
        $submissions = Submission::with('indexStat')
            ->where('journal_id', $journal->id)
            ->where('status', 'published') // Only monitor published articles
            ->orderByDesc('published_at')
            ->paginate(20);

        // 2. Calculate Stats
        // We use a separate query or aggregate to get full counts (ignoring pagination)
        $totalMonitored = Submission::where('journal_id', $journal->id)
            ->where('status', 'published')
            ->count();
        
        // Count stats from valid published submissions
        $statsQuery = SubmissionIndexStat::where('journal_id', $journal->id)
            ->whereHas('submission', fn($q) => $q->where('status', 'published'));
            
        $indexedCount = (clone $statsQuery)->where('is_indexed', true)->count();
        $issuesCount = (clone $statsQuery)->where('is_indexed', false)->count();
        $pendingCount = $totalMonitored - ($indexedCount + $issuesCount); // Approximate pending/error

        // Calculate Success Rate
        $successRate = $totalMonitored > 0 
            ? round(($indexedCount / $totalMonitored) * 100, 1) 
            : 0;

        return view('journal.stats.scholar', compact(
            'journal',
            'submissions',
            'totalMonitored',
            'indexedCount',
            'issuesCount',
            'pendingCount',
            'successRate'
        ));
    }

    /**
     * Manually trigger a check for a specific submission.
     */
    public function check(Request $request, $journal, $submissionId)
    {
        $submission = Submission::where('journal_id', current_journal()->id)
            ->where('id', $submissionId)
            ->firstOrFail();

        // Dispatch Job immediately (or with short delay)
        CheckArticleIndexJob::dispatch($submission->id);

        return back()->with('success', 'Manual check has been scheduled for this article.');
    }

    /**
     * Update monitoring settings for a specific submission.
     */
    public function update(Request $request, $journal, $submissionId)
    {
        $request->validate([
            'scholar_url' => 'nullable|url',
            'action' => 'required|in:monitor,pause,update_url',
        ]);

        $submission = Submission::where('journal_id', current_journal()->id)
            ->where('id', $submissionId)
            ->firstOrFail();

        $stat = SubmissionIndexStat::firstOrCreate(
            ['submission_id' => $submission->id],
            ['journal_id' => $submission->journal_id]
        );

        if ($request->action === 'pause') {
            $stat->is_monitored = false;
            $stat->save();
            return back()->with('success', 'Monitoring paused for this article.');
        }

        // Action monitor or update_url
        $stat->scholar_url = $request->scholar_url;
        $stat->is_monitored = true;
        
        // Reset status to force a fresh check if URL changed or re-enabled
        // $stat->is_indexed = null; 
        
        $stat->save();

        // Dispatch check immediately if enabling monitoring
        if ($request->action === 'monitor') {
            CheckArticleIndexJob::dispatch($submission->id);
        }

        return back()->with('success', 'Monitoring updated. Check scheduled.');
    }
}
