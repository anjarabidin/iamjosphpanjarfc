<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class EditorialController extends Controller
{
    /**
     * Get the current journal from context.
     */
    protected function getJournal(): Journal
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal context not found.');
        }

        return $journal;
    }

    /**
     * Display submission queue for editors.
     * Supports OJS 3.3 style queue types: unassigned, active, all
     */
    public function queue(Request $request): View
    {
        $journal = $this->getJournal();
        $queueType = $request->get('queue', 'all'); // unassigned, active, all
        $status = $request->get('status');
        $section = $request->get('section');
        $search = $request->get('search');

        // Base query
        $query = Submission::where('journal_id', $journal->id)
            ->with(['author', 'journal', 'section']);

        // Apply queue type filter (OJS 3.3 style)
        switch ($queueType) {
            case 'unassigned':
                // New submissions waiting for editor assignment
                $query->unassigned();
                break;
            case 'active':
                // Submissions that have been picked up (in review)
                $query->assigned();
                break;
            default:
                // All submissions in the queue (not archived)
                $query->inQueue();
                break;
        }

        // Additional status filter
        if ($status) {
            $query->where('status', $status);
        }

        // Filter by section
        if ($section) {
            $query->where('section_id', $section);
        }

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                    ->orWhereHas('author', function ($q) use ($search) {
                        $q->where('name', 'ilike', "%{$search}%");
                    });
            });
        }

        $submissions = $query->latest('submitted_at')->paginate(15);

        // Queue type counts for tabs
        $queueCounts = [
            'unassigned' => Submission::where('journal_id', $journal->id)->unassigned()->count(),
            'active' => Submission::where('journal_id', $journal->id)->assigned()->count(),
            'all' => Submission::where('journal_id', $journal->id)->inQueue()->count(),
        ];

        // Status counts
        $statusCounts = [
            'total' => Submission::where('journal_id', $journal->id)->inQueue()->count(),
            'submitted' => Submission::where('journal_id', $journal->id)->where('status', Submission::STATUS_SUBMITTED)->count(),
            'in_review' => Submission::where('journal_id', $journal->id)->where('status', Submission::STATUS_IN_REVIEW)->count(),
            'revision' => Submission::where('journal_id', $journal->id)->where('status', Submission::STATUS_REVISION_REQUIRED)->count(),
        ];

        return view('editorial.queue', compact('submissions', 'statusCounts', 'queueCounts', 'queueType', 'status', 'search', 'journal'));
    }

    /**
     * Display archived submissions.
     */
    public function archives(Request $request): View
    {
        $journal = $this->getJournal();
        $status = $request->get('status');
        $year = $request->get('year');
        $search = $request->get('search');

        $query = Submission::where('journal_id', $journal->id)
            ->archived()
            ->with(['author', 'journal', 'section', 'issue']);

        // Filter by status
        if ($status) {
            $query->where('status', $status);
        }

        // Filter by year
        if ($year) {
            $query->whereYear('created_at', $year);
        }

        // Search
        if ($search) {
            $query->where('title', 'ilike', "%{$search}%");
        }

        $submissions = $query->latest()->paginate(15);

        return view('editorial.archives', compact('submissions', 'status', 'year', 'search', 'journal'));
    }

    /**
     * Assign submission to editor (take ownership).
     */
    public function assign(string $journalSlug, Submission $submission): RedirectResponse
    {
        $journal = $this->getJournal();

        // Ensure submission belongs to this journal
        if ($submission->journal_id !== $journal->id) {
            abort(404);
        }

        // Update submission to in_review status
        $submission->update([
            'status' => Submission::STATUS_IN_REVIEW,
            'stage' => Submission::STAGE_REVIEW,
        ]);

        return back()->with('success', 'Submission assigned to you for review.');
    }

    /**
     * Accept submission.
     */
    public function accept(string $journalSlug, Submission $submission): RedirectResponse
    {
        $journal = $this->getJournal();

        // Ensure submission belongs to this journal
        if ($submission->journal_id !== $journal->id) {
            abort(404);
        }

        $submission->update([
            'status' => Submission::STATUS_ACCEPTED,
            'accepted_at' => now(),
        ]);

        return back()->with('success', 'Submission accepted.');
    }

    /**
     * Reject submission.
     */
    public function reject(Request $request, string $journalSlug, Submission $submission): RedirectResponse
    {
        $journal = $this->getJournal();

        // Ensure submission belongs to this journal
        if ($submission->journal_id !== $journal->id) {
            abort(404);
        }

        $request->validate([
            'reason' => 'nullable|string',
        ]);

        $metadata = $submission->metadata ?? [];
        $metadata['rejection_reason'] = $request->reason;

        $submission->update([
            'status' => Submission::STATUS_REJECTED,
            'metadata' => $metadata,
        ]);

        return back()->with('success', 'Submission rejected.');
    }

    /**
     * Request revision from author.
     */
    public function requestRevision(Request $request, string $journalSlug, Submission $submission): RedirectResponse
    {
        $journal = $this->getJournal();

        // Ensure submission belongs to this journal
        if ($submission->journal_id !== $journal->id) {
            abort(404);
        }

        $request->validate([
            'comments' => 'required|string',
        ]);

        $metadata = $submission->metadata ?? [];
        $metadata['revision_comments'] = $request->comments;
        $metadata['revision_requested_at'] = now()->toISOString();

        $submission->update([
            'status' => Submission::STATUS_REVISION_REQUIRED,
            'metadata' => $metadata,
        ]);

        return back()->with('success', 'Revision requested from author.');
    }
}
