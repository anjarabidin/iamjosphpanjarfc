<?php
namespace App\Http\Controllers;

use App\Models\Section;

use App\Models\Journal;
use App\Models\ReviewAssignment;
use App\Models\SubmissionFile;
use App\Notifications\ReviewCompleted;
use App\Services\WaGateway;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\SubmissionLog;
use App\Models\User;
use App\Models\Role;

class ReviewerController extends Controller
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
     * Display list of submissions assigned to current reviewer for current journal.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $journal = $this->getJournal();

        // Filter assignments based on simple tab status
        $status = $request->get('status', 'myqueue');
        $search = $request->get('search');
        $sections = $request->get('sections', []);
        $statuses = $request->get('statuses', []);

        $assignments = ReviewAssignment::where('reviewer_id', $user->id)
            ->whereHas('submission', function ($query) use ($journal) {
                $query->where('journal_id', $journal->id);
            });

        // Apply Tab Filter (My Queue vs Archives)
        if ($status === 'myqueue') {
            $assignments->whereIn('status', [ReviewAssignment::STATUS_PENDING, ReviewAssignment::STATUS_ACCEPTED]);
        } elseif ($status === 'archives') {
            $assignments->whereIn('status', [ReviewAssignment::STATUS_COMPLETED, ReviewAssignment::STATUS_DECLINED, ReviewAssignment::STATUS_CANCELLED]);
        }

        // Apply Search
        if ($search) {
            $assignments->whereHas('submission', function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('submission_code', 'ilike', "%{$search}%");
            });
        }

        // Apply Section Filters
        if (!empty($sections)) {
            $assignments->whereHas('submission', function ($q) use ($sections) {
                $q->whereIn('section_id', $sections);
            });
        }

        // Apply Status Filters (Review Assignment Specific)
        if (!empty($statuses)) {
            $assignments->where(function ($q) use ($statuses) {
                foreach ($statuses as $stat) {
                    if ($stat === 'overdue') {
                        $q->orWhere(function ($oq) {
                            $oq->where('due_date', '<', now())
                               ->whereNotIn('status', [
                                   ReviewAssignment::STATUS_COMPLETED,
                                   ReviewAssignment::STATUS_DECLINED,
                                   ReviewAssignment::STATUS_CANCELLED
                               ]);
                        });
                    } else {
                        $q->orWhere('status', $stat);
                    }
                }
            });
        }

        $assignments = $assignments->with(['submission' => function ($query) {
            $query->with(['journal', 'section']);
        }])
            ->orderBy('due_date', 'asc') // Consistent sorting
            ->paginate(15)
            ->withQueryString();

        // Count for tabs (keep it simple, ignoring advanced filters for the tab counts)
        $statusCounts = [
            'myqueue' => ReviewAssignment::where('reviewer_id', $user->id)
                ->whereHas('submission', fn($q) => $q->where('journal_id', $journal->id))
                ->whereIn('status', [ReviewAssignment::STATUS_PENDING, ReviewAssignment::STATUS_ACCEPTED])->count(),
            'archives' => ReviewAssignment::where('reviewer_id', $user->id)
                ->whereHas('submission', fn($q) => $q->where('journal_id', $journal->id))
                ->whereIn('status', [ReviewAssignment::STATUS_COMPLETED, ReviewAssignment::STATUS_DECLINED, ReviewAssignment::STATUS_CANCELLED])->count(),
        ];

        // Fetch dynamic categories for filter checkboxes
        $journalSections = Section::where('journal_id', $journal->id)->get();

        return view('reviewer.index', compact('assignments', 'statusCounts', 'journal', 'journalSections', 'status'));
    }

    /**
     * Accept review invitation.
     */
    public function accept(string $journalSlug, ReviewAssignment $assignment): RedirectResponse
    {
        $journal = $this->getJournal();
        $this->authorizeReviewer($assignment, $journal);

        if ($assignment->status !== ReviewAssignment::STATUS_PENDING) {
            return back()->with('error', 'This invitation has already been responded to.');
        }

        $assignment->update([
            'status' => ReviewAssignment::STATUS_ACCEPTED,
            'responded_at' => now(),
        ]);

        // Notify editors via WhatsApp
        try {
            $editors = User::whereHas('journalRoles', fn($q) => $q->where('journal_id', $journal->id)->whereIn('permission_level', [Role::LEVEL_SUPER_ADMIN, Role::LEVEL_EDITOR, Role::LEVEL_MANAGER]))->get();
            foreach ($editors as $editor) {
                WaGateway::sendTemplate($editor, 'reviewer_accepted', [
                    'name' => $editor->name,
                    'reviewer_name' => $assignment->reviewer->name,
                    'title' => $assignment->submission->title ?? 'Naskah',
                ], $assignment->submission->journal_id);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp notification for reviewer acceptance: ' . $e->getMessage());
        }

        return redirect()->route('journal.reviewer.show', ['journal' => $journal->slug, 'identifier' => $assignment->slug])
            ->with('success', 'Review invitation accepted. You can now submit your review.');
    }

    /**
     * Decline review invitation.
     */
    public function decline(Request $request, string $journalSlug, ReviewAssignment $assignment): RedirectResponse
    {
        $journal = $this->getJournal();
        $this->authorizeReviewer($assignment, $journal);

        if ($assignment->status !== ReviewAssignment::STATUS_PENDING) {
            return back()->with('error', 'This invitation has already been responded to.');
        }

        $metadata = $assignment->metadata ?? [];
        $metadata['decline_reason'] = $request->input('reason');

        $assignment->update([
            'status' => ReviewAssignment::STATUS_DECLINED,
            'responded_at' => now(),
            'metadata' => $metadata,
        ]);

        // Notify editors via WhatsApp
        try {
            $assignment->load(['reviewer', 'submission']);
            $editors = \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('name', ['Editor', 'Admin', 'Super Admin']))->get();
            foreach ($editors as $editor) {
                WaGateway::sendTemplate($editor, 'reviewer_declined', [
                    'name' => $editor->name,
                    'reviewer_name' => $assignment->reviewer->name,
                    'title' => $assignment->submission->title ?? 'Naskah',
                ], $assignment->submission->journal_id);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp notification for reviewer decline: ' . $e->getMessage());
        }

        return redirect()->route('journal.reviewer.index', ['journal' => $journal->slug])
            ->with('success', 'Review invitation declined.');
    }

    /**
     * Show review form for a specific submission.
     */
    public function show(string $journalSlug, string $identifier): View
    {
        $journal = $this->getJournal();
        $assignment = ReviewAssignment::findByIdentifier($identifier);
        $this->authorizeReviewer($assignment, $journal);

        // Load submission with blind review (hide author info)
        $submission = $assignment->submission;
        $submission->load(['journal', 'section', 'editorialAssignments.user', 'authors.user']);

        // Get manuscript files (latest version only)
        $manuscriptFiles = SubmissionFile::where('submission_id', $submission->id)
            ->whereIn('file_type', ['manuscript', 'revision'])
            ->where('stage', 'review')
            ->orderBy('version', 'desc')
            ->get();

        // Prepare participants for discussion (Editors and Authors)
        $participants = collect();
        foreach ($submission->editorialAssignments->where('is_active', true) as $editAssignment) {
            if ($editAssignment->user && !$participants->contains('id', $editAssignment->user->id)) {
                $participants->push($editAssignment->user);
            }
        }
        // Add authors as participants
        foreach ($submission->authors as $author) {
            if ($author->user && !$participants->contains('id', $author->user->id)) {
                $participants->push($author->user);
            }
        }

        // Get reviewer attachments for this assignment
        $reviewerAttachments = SubmissionFile::where('submission_id', $submission->id)
            ->where('stage', 'review')
            ->where('metadata->review_assignment_id', $assignment->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reviewer.show', compact('assignment', 'submission', 'manuscriptFiles', 'journal', 'participants', 'reviewerAttachments'));
    }

    /**
     * Submit the review.
     */
    public function submit(Request $request, string $journalSlug, ReviewAssignment $assignment): RedirectResponse
    {
        $journal = $this->getJournal();
        $this->authorizeReviewer($assignment, $journal);

        if (!in_array($assignment->status, [ReviewAssignment::STATUS_PENDING, ReviewAssignment::STATUS_ACCEPTED])) {
            return back()->with('error', 'This review cannot be submitted.');
        }

        $validated = $request->validate([
            'recommendation' => 'required|in:accept,minor_revision,major_revision,resubmit,reject',
            'comments_for_author' => 'required|string|min:10',
            'comments_for_editor' => 'nullable|string',
        ]);

        $assignment->update([
            'status' => ReviewAssignment::STATUS_COMPLETED,
            'recommendation' => $validated['recommendation'],
            'comments_for_author' => $validated['comments_for_author'],
            'comments_for_editor' => $validated['comments_for_editor'] ?? null,
            'completed_at' => now(),
        ]);

        // Notify editors that review is completed
        try {
            $this->notifyEditorsReviewCompleted($assignment);
        } catch (\Exception $e) {
            Log::error('Failed to notify editors about completed review: ' . $e->getMessage());
        }

        return redirect()->route('journal.reviewer.index', ['journal' => $journal->slug])
            ->with('success', 'Review submitted successfully. Thank you for your contribution!');
    }

    /**
     * Check if current user is the assigned reviewer and submission belongs to journal.
     */
    private function authorizeReviewer(ReviewAssignment $assignment, Journal $journal): void
    {
        if ($assignment->reviewer_id !== auth()->id()) {
            abort(403, 'You are not authorized to access this review.');
        }

        // Ensure the submission belongs to this journal
        if ($assignment->submission->journal_id !== $journal->id) {
            abort(404);
        }
    }

    /**
     * Notify editors when review is completed.
     */
    private function notifyEditorsReviewCompleted(ReviewAssignment $assignment): void
    {
        // Get editors with permission to make decisions
        $editors = \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('name', ['Editor', 'Admin', 'Super Admin']))->get();

        foreach ($editors as $editor) {
            // Send email notification
            $editor->notify(new ReviewCompleted($assignment));

            // Send WhatsApp notification
            WaGateway::sendTemplate($editor, 'review_submitted', [
                'name' => $editor->name,
                'title' => $assignment->submission->title ?? 'Naskah',
            ], $assignment->submission->journal_id);
        }
    }
    /**
     * Assign a reviewer (Editor Action).
     */
    public function assign(Request $request, \App\Models\Submission $submission)
    {
        // Ensure user has editor role in the journal
        $journal = $submission->journal;
        if (!auth()->user()->hasRoleInJournal(['Editor', 'Section Editor', 'Journal Manager', 'Admin', 'Super Admin'], $journal)) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'reviewer_id' => 'required|exists:users,id',
        ]);

        $reviewRound = $submission->currentReviewRound();

        // Ensure a review round exists
        if (!$reviewRound) {
            $reviewRound = \App\Models\ReviewRound::create([
                'submission_id' => $submission->id,
                'round' => 1,
                'status' => \App\Models\ReviewRound::STATUS_PENDING,
            ]);
        }

        // Check if already assigned
        $exists = ReviewAssignment::where('review_round_id', $reviewRound->id)
            ->where('reviewer_id', $request->reviewer_id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Reviewer already assigned.'], 422);
        }

        // Create Assignment
        ReviewAssignment::create([
            'submission_id' => $submission->id,
            'review_round_id' => $reviewRound->id,
            'reviewer_id' => $request->reviewer_id,
            'review_method' => 'double_blind', // Default
            'response_due_date' => now()->addWeeks(1),
            'due_date' => now()->addWeeks(4),
            'assigned_at' => now(),
            'round' => $reviewRound->round,
            'status' => ReviewAssignment::STATUS_PENDING,
        ]);

        return response()->json(['message' => 'Reviewer assigned successfully']);
    }
    /**
     * Upload reviewer attachment.
     */
    public function uploadAttachment(Request $request, string $journalSlug, ReviewAssignment $assignment)
    {
        $journal = $this->getJournal();
        $this->authorizeReviewer($assignment, $journal);

        if (!in_array($assignment->status, [ReviewAssignment::STATUS_PENDING, ReviewAssignment::STATUS_ACCEPTED])) {
            return response()->json(['message' => 'Review cannot be edited.'], 403);
        }

        $request->validate([
            'file' => 'required|file|mimes:doc,docx,pdf,rtf|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $submission = $assignment->submission;

        // Ensure submissions specific directory matching SubmissionFileController
        $directory = "submissions/{$submission->id}/reviewer-attachments";
        
        $originalName = $file->getClientOriginalName();
        $safeName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $originalName);
        $fileName = time() . '_' . $safeName;
        
        $path = $file->storeAs($directory, $fileName, 'local');

        $submissionFile = SubmissionFile::create([
            'submission_id' => $submission->id,
            'uploaded_by' => auth()->id(),
            'file_name' => $originalName,
            'file_path' => $path,
            'file_type' => 'review_attachment',
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'stage' => 'review',
            'metadata' => ['review_assignment_id' => $assignment->id],
        ]);

        // Audit Trail implementation
        SubmissionLog::log(
            submission: $submission,
            eventType: 'file_uploaded',
            title: 'Review Attachment Uploaded',
            description: "Reviewer " . auth()->user()?->name . " uploaded a review attachment: {$originalName}",
            fileIds: [$submissionFile->id]
        );

        return response()->json([
            'message' => 'File uploaded successfully',
            'file' => $submissionFile
        ]);
    }

    /**
     * Delete reviewer attachment.
     */
    public function deleteAttachment(Request $request, string $journalSlug, ReviewAssignment $assignment, SubmissionFile $file)
    {
        $journal = $this->getJournal();
        $this->authorizeReviewer($assignment, $journal);

        if (!in_array($assignment->status, [ReviewAssignment::STATUS_PENDING, ReviewAssignment::STATUS_ACCEPTED])) {
            return response()->json(['message' => 'Review cannot be edited.'], 403);
        }

        // Verify this file belongs to this assignment
        $metadata = $file->metadata ?? [];
        if (($metadata['review_assignment_id'] ?? null) != $assignment->id) {
            abort(403, 'Unauthorized access to file.');
        }

        $originalName = $file->file_name;

        if (Storage::disk('local')->exists($file->file_path)) {
            Storage::disk('local')->delete($file->file_path);
        }

        $file->delete();

        // Audit Trail implementation
        SubmissionLog::log(
            submission: $assignment->submission,
            eventType: 'metadata_updated',
            title: 'Review Attachment Deleted',
            description: "Reviewer " . auth()->user()?->name . " deleted a review attachment: {$originalName}"
        );

        return response()->json(['message' => 'File deleted successfully']);
    }
}
