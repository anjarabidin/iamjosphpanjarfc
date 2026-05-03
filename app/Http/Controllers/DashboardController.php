<?php

namespace App\Http\Controllers;

use App\Jobs\SendToWhatsappNotificationJob;
use App\Models\ReviewAssignment;
use App\Models\Submission;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the journal-specific dashboard.
     */
    public function index(): View
    {
        $user = auth()->user();
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal context not found.');
        }

        // 1. CHECK FOR REVIEWER ROLE (and NOT Journal Manager/Editor/Admin)
        // We want to show the specialized dashboard ONLY for users who are primarily Reviewers.
        // If they have higher privileges, they should see the standard dashboard with more tools.
        $hasHigherRole = $user->hasAnyRole(['Journal Manager', 'Editor', 'Section Editor', 'Admin', 'Super Admin']);

        if ($user->hasRole('Reviewer') && !$hasHigherRole) {

            // Fetch Data specific for Reviewer
            $assignments = ReviewAssignment::where('reviewer_id', $user->id)
                ->whereHas('submission', function ($query) use ($journal) {
                    $query->where('journal_id', $journal->id);
                })
                ->with('submission')
                ->whereIn('status', [
                    ReviewAssignment::STATUS_PENDING,
                    ReviewAssignment::STATUS_ACCEPTED
                ])
                ->orderBy('due_date', 'asc')
                ->get();

            // Calculate Stats
            $stats = [
                'pending'   => $assignments->count(),
                'completed' => ReviewAssignment::where('reviewer_id', $user->id)
                    ->whereHas('submission', function ($query) use ($journal) {
                        $query->where('journal_id', $journal->id);
                    })
                    ->where('status', ReviewAssignment::STATUS_COMPLETED)
                    ->count(),
            ];

            // Return NEW View
            return view('dashboard.reviewer', compact('assignments', 'stats', 'journal'));
        }

        // 2. FALLBACK FOR EVERYONE ELSE (Author, Reader, Editor, Admin, etc.)

        // Get user's submission stats for this journal
        $submissionStats = [
            'total' => Submission::where('user_id', $user->id)
                ->where('journal_id', $journal->id)
                ->count(),
            'in_review' => Submission::where('user_id', $user->id)
                ->where('journal_id', $journal->id)
                ->whereIn('status', [
                    Submission::STATUS_SUBMITTED,
                    Submission::STATUS_IN_REVIEW,
                ])->count(),
            'published' => Submission::where('user_id', $user->id)
                ->where('journal_id', $journal->id)
                ->where('status', Submission::STATUS_PUBLISHED)
                ->count(),
            'rejected' => Submission::where('user_id', $user->id)
                ->where('journal_id', $journal->id)
                ->where('status', Submission::STATUS_REJECTED)
                ->count(),
        ];

        // Get recent submissions for this journal
        $recentSubmissions = Submission::where('user_id', $user->id)
            ->where('journal_id', $journal->id)
            ->with(['journal', 'section'])
            ->latest()
            ->take(5)
            ->get();

        // For editors: get queue count for this journal
        $queueCount = 0;
        if ($user->hasAnyRole(['Editor', 'Admin', 'Super Admin'])) {
            $queueCount = Submission::where('journal_id', $journal->id)
                ->inQueue()
                ->count();
        }

        // For reviewers (who are also editors/admins): get pending reviews for this journal
        $pendingReviews = 0;
        if ($user->hasRole('Reviewer')) {
            $pendingReviews = ReviewAssignment::where('reviewer_id', $user->id)
                ->whereHas('submission', function ($q) use ($journal) {
                    $q->where('journal_id', $journal->id);
                })
                ->pending()
                ->count();
        }

        return view('dashboard', compact(
            'journal',
            'submissionStats',
            'recentSubmissions',
            'queueCount',
            'pendingReviews'
        ));
    }
}
