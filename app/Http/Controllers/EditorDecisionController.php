<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\Submission;
use App\Models\User;
use App\Models\ReviewAssignment;
use App\Notifications\SubmissionDecision;
use App\Notifications\ReviewInvitation;
use App\Services\WaGateway;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class EditorDecisionController extends Controller
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
     * Show submission detail for editor with review results.
     */
    public function show(string $journalSlug, Submission $submission): View
    {
        $journal = $this->getJournal();
        $this->authorizeEditor();

        // Ensure submission belongs to this journal
        if ($submission->journal_id !== $journal->id) {
            abort(404);
        }

        $submission->load([
            'journal',
            'section',
            'author',
            'issue',
            'files',
            'authors' => fn($q) => $q->orderBy('sort_order'),
            'reviewAssignments' => fn($q) => $q->with('reviewer')->orderBy('round')->orderBy('created_at'),
        ]);

        // Get available reviewers for assignment
        $reviewers = User::role('Reviewer')
            ->whereNotIn('id', $submission->reviewAssignments->pluck('reviewer_id'))
            ->get();

        // Calculate review summary
        $reviewSummary = $this->getReviewSummary($submission);

        return view('editor.submissions.show', compact('submission', 'reviewers', 'reviewSummary', 'journal'));
    }

    /**
     * Assign a reviewer to submission.
     */
    public function assignReviewer(Request $request, string $journalSlug, Submission $submission): RedirectResponse|JsonResponse
    {
        $journal = $this->getJournal();
        $this->authorizeEditor();

        // Ensure submission belongs to this journal
        if ($submission->journal_id !== $journal->id) {
            abort(404);
        }

        $validated = $request->validate([
            'reviewer_id' => 'required|uuid|exists:users,id',
            'due_date' => 'nullable|date|after:today',
            'message' => 'nullable|string|max:1000',
        ]);

        // Check if already assigned
        if ($submission->reviewAssignments()->where('reviewer_id', $validated['reviewer_id'])->exists()) {
            $error = 'This reviewer is already assigned to this submission.';
            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $error], 422)
                : back()->with('error', $error);
        }

        // Determine current round
        $currentRound = $submission->reviewAssignments()->max('round') ?? 1;

        // Create assignment
        $assignment = ReviewAssignment::create([
            'submission_id' => $submission->id,
            'reviewer_id' => $validated['reviewer_id'],
            'round' => $currentRound,
            'status' => ReviewAssignment::STATUS_PENDING,
            'assigned_at' => now(),
            'due_date' => $validated['due_date'] ?? now()->addDays(14),
            'metadata' => [
                'invitation_message' => $validated['message'] ?? null,
            ],
        ]);

        // Notify reviewer
        $reviewer = User::find($validated['reviewer_id']);
        $reviewer->notify(new ReviewInvitation($assignment));

        // Update submission stage if needed
        if ($submission->stage === Submission::STAGE_SUBMISSION) {
            $submission->update(['stage' => Submission::STAGE_REVIEW]);
        }

        $success = 'Reviewer assigned successfully.';
        return $request->wantsJson()
            ? response()->json(['success' => true, 'message' => $success, 'assignment' => $assignment])
            : back()->with('success', $success);
    }

    /**
     * Cancel/remove a reviewer assignment.
     */
    public function cancelReviewer(string $journalSlug, ReviewAssignment $assignment): RedirectResponse
    {
        $journal = $this->getJournal();
        $this->authorizeEditor();

        // Ensure the assignment's submission belongs to this journal
        if ($assignment->submission->journal_id !== $journal->id) {
            abort(404);
        }

        if ($assignment->status === ReviewAssignment::STATUS_COMPLETED) {
            return back()->with('error', 'Cannot remove a completed review.');
        }

        $assignment->update([
            'status' => ReviewAssignment::STATUS_CANCELLED,
        ]);

        return back()->with('success', 'Reviewer assignment cancelled.');
    }

    /**
     * Record editorial decision.
     */
    public function recordDecision(Request $request, string $journalSlug, Submission $submission): RedirectResponse|JsonResponse
    {
        $journal = $this->getJournal();
        $this->authorizeEditor();

        // Ensure submission belongs to this journal
        if ($submission->journal_id !== $journal->id) {
            abort(404);
        }

        $validated = $request->validate([
            'decision' => 'required|in:accept,reject,revision',
            'comments' => 'nullable|string|max:5000',
            'notify_author' => 'boolean',
        ]);

        $previousStatus = $submission->status;
        $updates = [];
        $notificationDecision = '';

        switch ($validated['decision']) {
            case 'accept':
                $updates = [
                    'status' => Submission::STATUS_ACCEPTED,
                    'stage' => Submission::STAGE_PRODUCTION,
                ];
                $notificationDecision = 'accepted';
                break;

            case 'reject':
                $updates = [
                    'status' => Submission::STATUS_REJECTED,
                ];
                $notificationDecision = 'rejected';
                break;

            case 'revision':
                $updates = [
                    'status' => Submission::STATUS_REVISION_REQUIRED,
                    'stage' => Submission::STAGE_REVISION,
                ];
                $notificationDecision = 'revision_required';
                break;
        }

        // Store decision metadata
        $metadata = $submission->metadata ?? [];
        $metadata['decisions'] = $metadata['decisions'] ?? [];
        $metadata['decisions'][] = [
            'decision' => $validated['decision'],
            'comments' => $validated['comments'] ?? null,
            'made_by' => auth()->id(),
            'made_at' => now()->toISOString(),
            'previous_status' => $previousStatus,
        ];
        $updates['metadata'] = $metadata;

        $submission->update($updates);

        // Notify author if requested
        if ($request->boolean('notify_author', true)) {
            $submission->author->notify(new SubmissionDecision(
                $submission,
                $notificationDecision,
                $validated['comments'] ?? null
            ));

            // Send WhatsApp notification based on decision type
            try {
                $waTemplate = match ($validated['decision']) {
                    'accept' => 'submission_accepted',
                    'reject' => 'submission_rejected',
                    'revision' => 'revision_request',
                    default => 'decision_update',
                };

                $statusText = match ($validated['decision']) {
                    'accept' => 'Diterima',
                    'reject' => 'Ditolak',
                    'revision' => 'Perlu Revisi',
                    default => 'Diperbarui',
                };

                WaGateway::sendTemplate($submission->author, $waTemplate, [
                    'name' => $submission->author->name,
                    'title' => $submission->title,
                    'status' => $statusText,
                ], $submission->journal_id);
            } catch (\Exception $e) {
                Log::error('Failed to send WhatsApp notification for decision: ' . $e->getMessage());
            }
        }

        $messages = [
            'accept' => 'Submission accepted. Author has been notified.',
            'reject' => 'Submission declined. Author has been notified.',
            'revision' => 'Revision requested. Author has been notified.',
        ];

        $success = $messages[$validated['decision']];

        return $request->wantsJson()
            ? response()->json(['success' => true, 'message' => $success])
            : back()->with('success', $success);
    }

    /**
     * Send submission back to review (new round).
     */
    public function sendToReview(string $journalSlug, Submission $submission): RedirectResponse
    {
        $journal = $this->getJournal();
        $this->authorizeEditor();

        // Ensure submission belongs to this journal
        if ($submission->journal_id !== $journal->id) {
            abort(404);
        }

        $submission->update([
            'status' => Submission::STATUS_UNDER_REVIEW,
            'stage' => Submission::STAGE_REVIEW,
        ]);

        return back()->with('success', 'Submission sent back to review.');
    }

    /**
     * Get review summary for a submission.
     */
    private function getReviewSummary(Submission $submission): array
    {
        $reviews = $submission->reviewAssignments;

        return [
            'total' => $reviews->count(),
            'pending' => $reviews->where('status', 'pending')->count(),
            'accepted' => $reviews->where('status', 'accepted')->count(),
            'completed' => $reviews->where('status', 'completed')->count(),
            'declined' => $reviews->where('status', 'declined')->count(),
            'recommendations' => $reviews->where('status', 'completed')
                ->groupBy('recommendation')
                ->map(fn($group) => $group->count()),
        ];
    }

    /**
     * Check if user is editor.
     */
    private function authorizeEditor(): void
    {
        if (!auth()->user()->hasAnyRole(['Editor', 'Admin', 'Super Admin'])) {
            abort(403, 'You are not authorized to perform this action.');
        }
    }
}
