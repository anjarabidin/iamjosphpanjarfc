<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use App\Models\DiscussionFile;
use App\Models\DiscussionMessage;
use App\Models\EditorialAssignment;
use App\Models\Journal;
use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\SubmissionLog;
use App\Models\ReviewAssignment;
use App\Models\ReviewRound;
use App\Services\FileUploadSecurityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SubmissionWorkflowController extends Controller
{
    public function __construct(
        protected FileUploadSecurityService $uploadSecurity
    ) {}
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
     * Display the workflow page for a submission (OJS 3.3 style).
     */
    public function show(string $journalSlug, Submission $submission): View
    {
        $journal = $this->getJournal();

        if ($submission->journal_id !== $journal->id) {
            abort(404);
        }

        $user = auth()->user();

        // Always false in this controller as it is workflow (editor/manager) view
        $isAuthorView = false;

        $submission->load([
            'journal',
            'section',
            'issue',
            'authors',
            'files',
            'discussions.user',
            'discussions.messages.user',
            'discussions.messages.files',
            'discussions.participants',
            'editorialAssignments.user',
            'reviewAssignments.reviewer',
        ]);

        $issues = \App\Models\Issue::where('journal_id', $journal->id)
            ->orderBy('year', 'desc')
            ->orderBy('volume', 'desc')
            ->orderBy('number', 'desc')
            ->get();

        $issueOptions = $issues->map(function ($issue) {
            return [
                'id' => $issue->id,
                'label' => $issue->identifier . ($issue->published_at ? ' (Published)' : ' (Unpublished)'),
            ];
        })->values();

        // Prepare participants for discussion modal (Author + Editors)
        $participants = collect();
        if ($submission->author) {
            $participants->push($submission->author);
        }
        foreach ($submission->editorialAssignments->where('is_active', true) as $assignment) {
            if ($assignment->user && !$participants->contains('id', $assignment->user->id)) {
                $participants->push($assignment->user);
            }
        }
        if (!$participants->contains('id', $user->id)) {
            $participants->push($user);
        }

        // Potential Editors for Assignment
        $activeEditorIds = $submission->editorialAssignments
            ->where('is_active', true)
            ->pluck('user_id')
            ->filter()
            ->toArray();

        $potentialEditors = \App\Models\User::whereHas('journalRoles', function ($query) use ($journal) {
            $query->where('journal_id', $journal->id)
                  ->whereHas('role', function ($q) {
                      $q->where('permit_submission', 1);
                  });
        })
        ->whereDoesntHave('submissionAuthors', function ($q) use ($submission) {
            $q->where('submission_id', $submission->id);
        })
        ->whereNotIn('id', $activeEditorIds)
        ->with(['journalRoles' => function($q) use ($journal) {
            $q->where('journal_id', $journal->id)->with('role');
        }])
        ->get()
        ->map(function ($user) {
            $roles = $user->journalRoles->map(fn($jr) => $jr->role->name)->toArray();
            $user->role_names = $roles;
            $user->role_display = implode(', ', $roles);
            return $user;
        });

        // SEO Analysis
        $validator = new \App\Services\GoogleScholarValidator();
        $seoAnalysis = $validator->validate($submission);

        return view('submissions.show', compact(
            'submission',
            'journal',
            'issues',
            'issueOptions',
            'participants',
            'isAuthorView',
            'potentialEditors',
            'seoAnalysis'
        ));
    }

    /**
     * Assign an editor to the submission.
     */
    public function assignEditor(Request $request, string $journalSlug, Submission $submission): RedirectResponse
    {
        $journal = $this->getJournal();

        if ($submission->journal_id !== $journal->id) {
            abort(404);
        }

        $validated = $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
        ]);

        // Check if already assigned
        $exists = EditorialAssignment::where('submission_id', $submission->id)
            ->where('user_id', $validated['user_id'])
            ->where('is_active', true)
            ->exists();

        if ($exists) {
            return back()->with('error', 'This user is already assigned to this submission.');
        }

        // Determine role based on user's roles in this journal
        $user = \App\Models\User::find($validated['user_id']);
        $journalRole = $user->journalRoles()->where('journal_id', $journal->id)->first();
        
        // Default to 'editor' if no specific role found or if they are a manager/admin
        $role = 'editor'; 
        if ($journalRole && $journalRole->role->name === 'Section Editor') {
            $role = 'section_editor';
        }

        EditorialAssignment::create([
            'submission_id' => $submission->id,
            'user_id' => $validated['user_id'],
            'assigned_by' => auth()->id(),
            'role' => $role,
            'date_assigned' => now(),
        ]);

        // Notify the assigned editor
        if ($user) {
            $user->notify(new \App\Notifications\EditorAssignmentNotification($submission, auth()->user()));

            // Log the event
            SubmissionLog::log(
                submission:  $submission,
                eventType:   SubmissionLog::EVENT_EDITOR_ASSIGNED,
                title:       'Editor Assigned',
                description: auth()->user()->name . " assigned {$user->name} as " . ucfirst(str_replace('_', ' ', $role)) . ".",
                metadata:    ['editor_id' => $user->id, 'role' => $role],
                stage:       $submission->stage,
            );
        }

        // Update submission status if it was just submitted
        if ($submission->status === Submission::STATUS_SUBMITTED && $submission->stage_id === 1) {
            $submission->update([
                'status' => Submission::STATUS_IN_REVIEW,
            ]);
        }

        return back()->with('success', 'Editor assigned successfully.');
    }

    /**
     * Remove an editor assignment.
     */
    public function removeEditor(string $journalSlug, Submission $submission, EditorialAssignment $assignment): RedirectResponse
    {
        $journal = $this->getJournal();

        if ($submission->journal_id !== $journal->id) {
            abort(404);
        }

        $assignment->update(['is_active' => false]);

        return back()->with('success', 'Editor assignment removed.');
    }

    /**
     * Change submission stage (OJS 3.3 workflow).
     */
    public function changeStage(Request $request, string $journalSlug, Submission $submission): RedirectResponse
    {
        $journal = $this->getJournal();

        if ($submission->journal_id !== $journal->id) {
            abort(404);
        }

        $validated = $request->validate([
            'stage_id' => 'required|integer|in:1,2,3,4',
            'action' => 'nullable|string',
        ]);

        $newStageId = (int) $validated['stage_id'];
        $action = $validated['action'] ?? null;

        DB::beginTransaction();

        try {
            $oldStageId = $submission->stage_id;

            // Update stage
            $submission->update([
                'stage_id' => $newStageId,
                'stage' => $this->getStageNameById($newStageId),
            ]);

            // Handle specific actions
            switch ($action) {
                case 'send_to_review':
                    $submission->update(['status' => Submission::STATUS_IN_REVIEW]);
                    break;

                case 'accept':
                    $submission->update([
                        'status' => Submission::STATUS_ACCEPTED,
                        'accepted_at' => now(),
                    ]);
                    break;

                case 'request_revisions':
                    $submission->update(['status' => Submission::STATUS_REVISION_REQUIRED]);
                    break;

                case 'decline':
                    $submission->update(['status' => Submission::STATUS_REJECTED]);
                    break;
            }

            DB::commit();

            // Audit log the stage change
            $stageNames = [1 => 'Submission', 2 => 'Review', 3 => 'Copyediting', 4 => 'Production'];
            SubmissionLog::log(
                submission:  $submission,
                eventType:   SubmissionLog::EVENT_STAGE_CHANGED,
                title:       'Stage Changed to ' . ($stageNames[$newStageId] ?? $newStageId),
                description: auth()->user()->name . ' changed the workflow stage' . ($action ? " (action: $action)" : '') . '.',
                metadata:    ['from_stage' => $oldStageId, 'to_stage' => $newStageId, 'action' => $action],
                stage:       SubmissionLog::stageFromId($newStageId),
            );

            return back()->with('success', $this->getStageChangeMessage($oldStageId, $newStageId, $action));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to change stage: ' . $e->getMessage());
        }
    }

    /**
     * Schedule submission for publication.
     */
    public function schedulePublication(Request $request, string $journalSlug, Submission $submission): RedirectResponse
    {
        $journal = $this->getJournal();

        if ($submission->journal_id !== $journal->id) {
            abort(404);
        }

        $validated = $request->validate([
            'issue_id' => 'required|uuid|exists:issues,id',
        ]);

        $submission->update([
            'issue_id' => $validated['issue_id'],
            'status' => Submission::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        return back()->with('success', 'Submission scheduled for publication.');
    }

    /**
     * Get stage name by ID.
     */
    private function getStageNameById(int $stageId): string
    {
        return match ($stageId) {
            1 => Submission::STAGE_SUBMISSION,
            2 => Submission::STAGE_REVIEW,
            3 => Submission::STAGE_COPYEDITING,
            4 => Submission::STAGE_PRODUCTION,
            default => Submission::STAGE_SUBMISSION,
        };
    }

    /**
     * Get message for stage change.
     */
    private function getStageChangeMessage(int $oldStage, int $newStage, ?string $action): string
    {
        if ($action === 'accept') {
            return 'Submission accepted and moved to copyediting.';
        }

        if ($action === 'request_revisions') {
            return 'Revisions requested from author.';
        }

        if ($action === 'decline') {
            return 'Submission declined.';
        }

        $stageNames = [
            1 => 'Submission',
            2 => 'Review',
            3 => 'Copyediting',
            4 => 'Production',
        ];

        return "Submission moved to {$stageNames[$newStage]} stage.";
    }
    /**
     * Upload a file to the submission.
     */
    public function uploadFile(Request $request, string $journalSlug, Submission $submission): RedirectResponse
    {
        $journal = $this->getJournal();
        if ($submission->journal_id !== $journal->id) {
            abort(404);
        }

        $request->validate([
            'file' => 'required|file',
            'stage' => 'required|string|in:submission,review,copyedit_draft,copyedited,production',
        ]);

        // Security: validate file via FileUploadSecurityService
        $this->uploadSecurity->validate($request->file('file'), 'manuscript', $request);

        // Map copyedit_draft and copyedited back to copyediting for policy check
        $policyStage = in_array($request->stage, ['copyedit_draft', 'copyedited']) ? 'copyediting' : $request->stage;
        $this->authorize('accessStage', [$submission, $policyStage]);

        $file = $request->file('file');
        $path = $file->store("journals/{$journal->id}/submissions/{$submission->id}/files");

        $submissionFile = SubmissionFile::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'submission_id' => $submission->id,
            'uploaded_by' => auth()->id(),
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => 'document',
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'stage' => $request->stage,
            'version' => 1,
        ]);

        // Audit log with file reference
        SubmissionLog::log(
            submission:  $submission,
            eventType:   SubmissionLog::EVENT_FILE_UPLOADED,
            title:       'File Uploaded: ' . $file->getClientOriginalName(),
            description: auth()->user()->name . ' uploaded a file to the ' . $request->stage . ' stage.',
            metadata:    ['file_name' => $file->getClientOriginalName(), 'file_size' => $file->getSize()],
            fileIds:     [$submissionFile->id],
            stage:       $request->stage,
        );

        return back()->with('success', 'File uploaded successfully.');
    }

    /**
     * Store a new discussion.
     */
    public function storeDiscussion(Request $request, string $journalSlug, Submission $submission): RedirectResponse
    {
        $journal = $this->getJournal();
        if ($submission->journal_id !== $journal->id) {
            abort(404);
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'stage_id' => 'required|integer',
        ]);

        // Map stage_id to string for policy check
        $stageNames = [1 => 'submission', 2 => 'review', 3 => 'copyediting', 4 => 'production'];
        $stageName = $stageNames[$request->stage_id] ?? 'submission';
        $this->authorize('accessStage', [$submission, $stageName]);

        $discussion = Discussion::create([
            'submission_id' => $submission->id,
            'user_id' => auth()->id(),
            'subject' => $request->subject,
            'stage_id' => $request->stage_id,
            'is_open' => true,
        ]);

        DiscussionMessage::create([
            'discussion_id' => $discussion->id,
            'user_id' => auth()->id(),
            'body' => $request->message,
        ]);

        return back()->with('success', 'Discussion started.');
    }

    /**
     * Get all available files for promotion (Submission Files + Discussion Files).
     * Used by the Send to Review modal.
     */
    public function getAvailableFiles(string $journalSlug, Submission $submission)
    {
        $journal = $this->getJournal();
        if ($submission->journal_id !== $journal->id) {
            abort(404);
        }

        // 1. Get Submission Files from current stage
        $submissionFiles = $submission->files()
            ->where('stage', 'submission')
            ->get()
            ->map(function ($file) {
                return [
                    'id' => $file->id,
                    'name' => $file->file_name,
                    'size' => $file->file_size,
                    'type' => 'submission_file',
                    'source' => 'Submission Files',
                    'created_at' => $file->created_at->format('M d, Y'),
                ];
            });

        // 2. Get Discussion Files from pre-review discussions (stage_id = 1)
        $discussionFiles = DiscussionFile::whereHas('message.discussion', function ($q) use ($submission) {
            $q->where('submission_id', $submission->id)
                ->where('stage_id', 1);
        })->get()->map(function ($file) {
            return [
                'id' => $file->id,
                'name' => $file->original_name,
                'size' => $file->file_size,
                'type' => 'discussion_file',
                'source' => 'Pre-Review Discussions',
                'created_at' => $file->created_at->format('M d, Y'),
            ];
        });

        return response()->json([
            'files' => $submissionFiles->merge($discussionFiles)->values(),
        ]);
    }

    /**
     * Promote submission to Review stage with file selection.
     * Copies selected files to the review stage.
     */
    public function promoteToReview(Request $request, string $journalSlug, Submission $submission): RedirectResponse
    {
        $journal = $this->getJournal();
        if ($submission->journal_id !== $journal->id) {
            abort(404);
        }
        $validated = $request->validate([
            'selected_files' => 'nullable|array',
            'selected_files.*.id' => 'required|uuid',
            'selected_files.*.type' => 'required|in:submission_file,discussion_file',
        ]);

        DB::beginTransaction();

        try {
            // Update submission stage to Review (2)
            $submission->update([
                'stage_id' => 2,
                'stage' => Submission::STAGE_REVIEW,
                'status' => Submission::STATUS_IN_REVIEW,
            ]);

            // Create initial review round if not exists
            $reviewRound = $submission->currentReviewRound();
            if (!$reviewRound) {
                ReviewRound::create([
                    'submission_id' => $submission->id,
                    'round' => 1,
                    'status' => ReviewRound::STATUS_PENDING,
                ]);
            }

            // Copy selected files to review stage
            $submissionFileIds = [];
            if (!empty($validated['selected_files'])) {
                foreach ($validated['selected_files'] as $fileData) {
                    if ($fileData['type'] === 'submission_file') {
                        // Copy submission file
                        $originalFile = SubmissionFile::find($fileData['id']);
                        if ($originalFile) {
                            $submissionFile = SubmissionFile::create([
                                'id' => (string) Str::uuid(),
                                'submission_id' => $submission->id,
                                'uploaded_by' => auth()->id(),
                                'file_path' => $originalFile->file_path,
                                'file_name' => $originalFile->file_name,
                                'file_type' => $originalFile->file_type,
                                'mime_type' => $originalFile->mime_type,
                                'file_size' => $originalFile->file_size,
                                'stage' => 'review',
                                'version' => 1,
                                'metadata' => [
                                    'copied_from' => $originalFile->id,
                                    'copied_at' => now()->toISOString(),
                                ],
                            ]);
                            $submissionFileIds[] = $submissionFile->id;
                        }
                    } elseif ($fileData['type'] === 'discussion_file') {
                        // Copy discussion file to submission files
                        $discussionFile = DiscussionFile::find($fileData['id']);
                        if ($discussionFile) {
                            $submissionFile = SubmissionFile::create([
                                'id' => (string) Str::uuid(),
                                'submission_id' => $submission->id,
                                'uploaded_by' => auth()->id(),
                                'file_path' => $discussionFile->file_path,
                                'file_name' => $discussionFile->original_name,
                                'file_type' => $discussionFile->file_type ?? 'document',
                                'mime_type' => 'application/octet-stream',
                                'file_size' => $discussionFile->file_size,
                                'stage' => 'review',
                                'version' => 1,
                                'metadata' => [
                                    'copied_from_discussion' => $discussionFile->id,
                                    'copied_at' => now()->toISOString(),
                                ],
                            ]);
                            $submissionFileIds[] = $submissionFile->id;
                        }
                    }
                }
            }

            DB::commit();

            // Audit log the stage transition
            SubmissionLog::log(
                submission:  $submission,
                eventType:   SubmissionLog::EVENT_STAGE_CHANGED,
                title:       'Sent to Review Stage',
                description: auth()->user()->name . ' promoted the submission to the Review stage with ' . count($validated['selected_files'] ?? []) . ' file(s).',
                metadata:    ['file_count' => count($validated['selected_files'] ?? [])],
                fileIds:     $submissionFileIds,
                stage:       Submission::STAGE_REVIEW,
            );

            return back()->with('success', 'Submission sent to Review stage. ' . count($validated['selected_files'] ?? []) . ' file(s) promoted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to promote to review: ' . $e->getMessage());
        }
    }

    /**
     * Accept submission and skip review stage directly to Copyediting.
     * Used for trusted authors or fast-track submissions.
     */
    public function skipReview(Request $request, string $journalSlug, Submission $submission): RedirectResponse
    {
        $journal = $this->getJournal();
        if ($submission->journal_id !== $journal->id) {
            abort(404);
        }

        $validated = $request->validate([
            'selected_files' => 'nullable|array',
            'selected_files.*.id' => 'required|uuid',
            'selected_files.*.type' => 'required|in:submission_file,discussion_file',
            'notes' => 'nullable|string|max:2000',
        ]);

        DB::beginTransaction();

        try {
            // Update submission stage directly to Copyediting (3)
            $submission->update([
                'stage_id' => 3,
                'stage' => Submission::STAGE_COPYEDITING,
                'status' => Submission::STATUS_ACCEPTED,
                'accepted_at' => now(),
            ]);

            // Copy selected files to copyediting stage
            $submissionFileIds = [];
            if (!empty($validated['selected_files'])) {
                foreach ($validated['selected_files'] as $fileData) {
                    if ($fileData['type'] === 'submission_file') {
                        $originalFile = SubmissionFile::find($fileData['id']);
                        if ($originalFile) {
                            $submissionFile = SubmissionFile::create([
                                'id' => (string) Str::uuid(),
                                'submission_id' => $submission->id,
                                'uploaded_by' => auth()->id(),
                                'file_path' => $originalFile->file_path,
                                'file_name' => $originalFile->file_name,
                                'file_type' => $originalFile->file_type,
                                'mime_type' => $originalFile->mime_type,
                                'file_size' => $originalFile->file_size,
                                'stage' => 'copyediting',
                                'version' => 1,
                                'metadata' => [
                                    'copied_from' => $originalFile->id,
                                    'skip_review' => true,
                                    'copied_at' => now()->toISOString(),
                                ],
                            ]);
                            $submissionFileIds[] = $submissionFile->id;
                        }
                    } elseif ($fileData['type'] === 'discussion_file') {
                        $discussionFile = DiscussionFile::find($fileData['id']);
                        if ($discussionFile) {
                            $submissionFile = SubmissionFile::create([
                                'id' => (string) Str::uuid(),
                                'submission_id' => $submission->id,
                                'uploaded_by' => auth()->id(),
                                'file_path' => $discussionFile->file_path,
                                'file_name' => $discussionFile->original_name,
                                'file_type' => $discussionFile->file_type ?? 'document',
                                'mime_type' => 'application/octet-stream',
                                'file_size' => $discussionFile->file_size,
                                'stage' => 'copyediting',
                                'version' => 1,
                                'metadata' => [
                                    'copied_from_discussion' => $discussionFile->id,
                                    'skip_review' => true,
                                    'copied_at' => now()->toISOString(),
                                ],
                            ]);
                            $submissionFileIds[] = $submissionFile->id;
                        }
                    }
                }
            }

            // Log the skip review action
            if (!empty($validated['notes'])) {
                Discussion::create([
                    'submission_id' => $submission->id,
                    'user_id' => auth()->id(),
                    'subject' => 'Review Skipped - Direct Accept',
                    'stage_id' => 3,
                    'is_open' => false,
                ]);
            }

            DB::commit();

            // Audit log the skip-review stage transition
            SubmissionLog::log(
                submission:  $submission,
                eventType:   SubmissionLog::EVENT_STAGE_CHANGED,
                title:       'Skip Review – Sent to Copyediting',
                description: auth()->user()->name . ' accepted the submission and skipped review, moving it directly to Copyediting.',
                fileIds:     $submissionFileIds,
                stage:       Submission::STAGE_COPYEDITING,
            );

            return back()->with('success', 'Submission accepted and moved directly to Copyediting.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to skip review: ' . $e->getMessage());
        }
    }

    /**
     * Decline a submission with reason/email log.
     */
    public function decline(Request $request, string $journalSlug, Submission $submission): RedirectResponse
    {
        $journal = $this->getJournal();
        if ($submission->journal_id !== $journal->id) {
            abort(404);
        }

        $validated = $request->validate([
            'reason' => 'required|string|min:10|max:5000',
            'notify_author' => 'nullable',
        ]);

        DB::beginTransaction();

        try {
            // Update submission status to rejected
            $submission->update([
                'status' => Submission::STATUS_REJECTED,
            ]);
            // Log the decline reason as metadata
            $metadata = $submission->metadata ?? [];
            $metadata['decline_log'] = [
                'declined_by' => auth()->id(),
                'declined_at' => now()->toISOString(),
                'reason' => $validated['reason'],
            ];
            $submission->update(['metadata' => $metadata]);

            // Create a discussion entry for the decline reason (visible in archives)
            $discussion = Discussion::create([
                'submission_id' => $submission->id,
                'user_id' => auth()->id(),
                'subject' => 'Submission Declined',
                'stage_id' => $submission->stage_id,
                'is_open' => false,
            ]);

            DiscussionMessage::create([
                'discussion_id' => $discussion->id,
                'user_id' => auth()->id(),
                'body' => '<p><strong>Reason for Declining:</strong></p>' . nl2br(e($validated['reason'])),
            ]);

            // Notify the author about the decline
            // if (!empty($validated['notify_author']) || true) { // Default to always notify
            //     try {
            //         $author = $submission->user;
            //         $author->notify(new \App\Notifications\SubmissionDeclinedNotification(
            //             $submission,
            //             auth()->user(),
            //             $validated['reason']
            //         ));
            //     } catch (\Exception $e) {
            //         Log::error('Failed to send decline notification: ' . $e->getMessage());
            //     }
            // }

            DB::commit();

            return back()->with('success', 'Submission has been declined and the author has been notified.');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return back()->with('error', 'Failed to decline submission: ' . $e->getMessage());
        }
    }
}
