<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Discussion;
use App\Models\Submission;
use App\Services\WaGateway;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DiscussionFile;
use App\Models\DiscussionMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Notifications\NewDiscussionMessageNotification;

class SubmissionDiscussionController extends Controller
{
    private function getJournal()
    {
        return current_journal();
    }

    /**
     * Check if user can participate in discussions for this submission.
     */
    private function canParticipate(Submission $submission): bool
    {
        $user = auth()->user();

        $journal = $submission->journal ?? null;
        if (!$journal) return false;

        // 1. Check permission level for internal staff (Managers, Editors, Section Editors, Assistants)
        if ($user->hasJournalPermission([1, 2, 3, 4, 5], $journal->id)) {
            return true;
        }

        // Author can participate in their own submissions
        if ($submission->user_id === $user->id) {
            return true;
        }

        // Check if user is assigned as editor
        return $submission->editorialAssignments()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Store a new discussion topic (Create Discussion).
     * 
     * OJS 3.3 Behavior:
     * - Stage isolation: Each discussion is bound to a specific stage_id
     * - Participants: Users selected + current user (creator) always included
     * - Notification: All participants except creator receive notification
     */
    public function store(Request $request, string $journalSlug, $submission)
    {
        // $submission = Submission::findOrFail($id);cari berdasarkan slug ataupun id
       $submissionModel = Submission::where('slug', $submission);

        if (Str::isUuid($submission)) {
            $submissionModel->orWhere('id', $submission);
        }

        $submission = $submissionModel->firstOrFail();
        $journal = $this->getJournal();
        if ($submission->journal_id !== $journal->id) abort(404);

        // Check participation permission
        if (!$this->canParticipate($submission)) {
            abort(403, 'You do not have permission to create discussions for this submission.');
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'stage_id' => 'required|integer|in:1,2,3,4',
            'participants' => 'required|array|min:1',
            'participants.*' => 'required|uuid|exists:users,id',
            'attached_files' => 'nullable|array',
            'attached_files.*.id' => 'required|exists:discussion_files,id',
            'attached_files.*.name' => 'nullable|string|max:255',
        ]);
        $discussion = null;
        $firstMessage = null;
        $currentUserId = auth()->id();

        DB::transaction(function () use ($request, $submission, &$discussion, &$firstMessage, $currentUserId) {
            // 1. Create Discussion with stage_id for stage isolation
            $discussion = Discussion::create([
                'submission_id' => $submission->id,
                'user_id' => $currentUserId,
                'subject' => $request->subject,
                'stage_id' => $request->stage_id,
                'is_open' => true,
            ]);

            // 2. Sync Participants - ALWAYS include creator
            $participantIds = collect($request->participants)
                ->push($currentUserId)
                ->unique()
                ->values()
                ->toArray();

            $discussion->addParticipants($participantIds);

            // 3. Create First Message
            $firstMessage = DiscussionMessage::create([
                'discussion_id' => $discussion->id,
                'user_id' => $currentUserId,
                'body' => $request->body,
            ]);

            $submissionFileIds = [];

            // 4. Link Attached Files
            if ($request->filled('attached_files')) {
                foreach ($request->attached_files as $fileData) {
                    DiscussionFile::where('id', $fileData['id'])
                        ->update([
                            'discussion_message_id' => $firstMessage->id,
                            'original_name' => $fileData['name'] ?? DB::raw('original_name'),
                        ]);

                    // Auto-copy to SubmissionFiles for audit trail
                    $discussionFile = DiscussionFile::find($fileData['id']);
                    if ($discussionFile) {
                        $submissionFile = \App\Models\SubmissionFile::create([
                            'id' => (string) Str::uuid(),
                            'submission_id' => $submission->id,
                            'uploaded_by' => $currentUserId,
                            'file_path' => $discussionFile->file_path,
                            'file_name' => $fileData['name'] ?? $discussionFile->original_name,
                            'file_type' => 'document',
                            'mime_type' => 'application/octet-stream',
                            'file_size' => $discussionFile->file_size,
                            'stage' => 'discussion',
                            'version' => 1,
                            'metadata' => [
                                'discussion_id' => $discussion->id,
                                'discussion_message_id' => $firstMessage->id,
                                'copied_from_discussion' => $discussionFile->id,
                            ],
                        ]);
                        $submissionFileIds[] = $submissionFile->id;
                    }
                }
            }

            // 5. Log activity and attach files
            \App\Models\SubmissionLog::log(
                submission:  $submission,
                eventType:   \App\Models\SubmissionLog::EVENT_DISCUSSION_CREATED,
                title:       'Discussion Created',
                description: auth()->user()->name . ' started a new discussion: "' . $discussion->subject . '"',
                metadata:    ['discussion_id' => $discussion->id],
                user:        auth()->user(),
                fileIds:     $submissionFileIds,
                stage:       $submission->stage,
            );
        });

        // 6. Send Notifications to all participants EXCEPT the creator
        if ($discussion && $firstMessage) {
            $this->notifyParticipants($discussion, $firstMessage, $currentUserId);
        }

        return back()->with('success', 'Discussion created successfully.');
    }

    /**
     * Add a reply to a discussion (Create Message).
     * 
     * OJS 3.3 Behavior:
     * - User must be a participant OR have editor privileges
     * - Touch parent discussion to update sorting
     * - Notify all participants except sender
     */
    public function storeReply(Request $request, string $journalSlug, Submission $submission, Discussion $discussion)
    {
        $journal = $this->getJournal();
        if ($submission->journal_id !== $journal->id) abort(404);
        if ($discussion->submission_id !== $submission->id) abort(404);

        // Check if discussion is open
        if (!$discussion->is_open) {
            return back()->with('error', 'This discussion is closed.');
        }

        $currentUserId = auth()->id();
        $isEditor =  auth()->user()->hasJournalPermission([1,2], $journal->id);

        // Check if user can reply (is participant or editor)
        if (!$isEditor && !$discussion->hasParticipant($currentUserId)) {
            abort(403, 'You are not a participant in this discussion.');
        }

        $request->validate([
            'body' => 'required|string',
            'attached_files' => 'nullable|array',
            'attached_files.*.id' => 'required|exists:discussion_files,id',
            'attached_files.*.name' => 'nullable|string|max:255',
        ]);

        $message = null;

        DB::transaction(function () use ($request, $discussion, &$message, $currentUserId) {
            // 1. Create Message
            $message = DiscussionMessage::create([
                'discussion_id' => $discussion->id,
                'user_id' => $currentUserId,
                'body' => $request->body,
            ]);

            $submissionFileIds = [];

            // 2. Link Attached Files
            if ($request->filled('attached_files')) {
                foreach ($request->attached_files as $fileData) {
                    DiscussionFile::where('id', $fileData['id'])
                        ->update([
                            'discussion_message_id' => $message->id,
                            'original_name' => $fileData['name'] ?? DB::raw('original_name'),
                        ]);

                    // Auto-copy to SubmissionFiles for audit trail
                    $discussionFile = DiscussionFile::find($fileData['id']);
                    if ($discussionFile) {
                        $submissionFile = \App\Models\SubmissionFile::create([
                            'id' => (string) Str::uuid(),
                            'submission_id' => $discussion->submission_id,
                            'uploaded_by' => $currentUserId,
                            'file_path' => $discussionFile->file_path,
                            'file_name' => $fileData['name'] ?? $discussionFile->original_name,
                            'file_type' => 'document',
                            'mime_type' => 'application/octet-stream',
                            'file_size' => $discussionFile->file_size,
                            'stage' => 'discussion',
                            'version' => 1,
                            'metadata' => [
                                'discussion_id' => $discussion->id,
                                'discussion_message_id' => $message->id,
                                'copied_from_discussion' => $discussionFile->id,
                            ],
                        ]);
                        $submissionFileIds[] = $submissionFile->id;
                    }
                }
            }

            // 3. Touch parent discussion to update updated_at (for sorting)
            $discussion->touch();

            // 4. Add sender as participant if not already (edge case)
            if (!$discussion->hasParticipant($currentUserId)) {
                $discussion->addParticipants([$currentUserId]);
            }

            // 5. Log activity and attach files
            \App\Models\SubmissionLog::log(
                submission:  $discussion->submission,
                eventType:   \App\Models\SubmissionLog::EVENT_DISCUSSION_MESSAGE,
                title:       'Discussion Message Added',
                description: auth()->user()->name . ' replied to discussion: "' . $discussion->subject . '"',
                metadata:    ['discussion_id' => $discussion->id, 'message_id' => $message->id],
                user:        auth()->user(),
                fileIds:     $submissionFileIds,
                stage:       $discussion->submission->stage,
            );
        });

        // 5. Notify all participants except sender
        if ($message) {
            $this->notifyParticipants($discussion, $message, $currentUserId);
        }

        return back()->with('success', 'Reply added successfully.');
    }

    /**
     * Update a message (Edit Feature).
     * 
     * OJS 3.3 Behavior:
     * - Editor/Admin: Can edit ANY message in the discussion
     * - Author: Can only edit their OWN messages
     */
    public function updateMessage(Request $request, string $journalSlug, Submission $submission, Discussion $discussion, DiscussionMessage $message)
    {
        $journal = $this->getJournal();
        if ($submission->journal_id !== $journal->id) abort(404);
        if ($discussion->submission_id !== $submission->id) abort(404);
        if ($message->discussion_id !== $discussion->id) abort(404);

        $currentUserId = auth()->id();
        $isEditor = auth()->user()->hasJournalPermission([1,2], $journal->id);
        $isOwner = $message->user_id === $currentUserId;

        // Permission check
        if (!$isEditor && !$isOwner) {
            abort(403, 'You do not have permission to edit this message.');
        }

        $request->validate([
            'body' => 'required|string',
        ]);

        $message->update([
            'body' => $request->body,
        ]);

        return back()->with('success', 'Message updated successfully.');
    }

    /**
     * Close a discussion.
     * Only Editors can close discussions.
     */
    public function close(Request $request, string $journalSlug, Submission $submission, Discussion $discussion)
    {
        $journal = $this->getJournal();
        if ($submission->journal_id !== $journal->id) abort(404);
        if ($discussion->submission_id !== $submission->id) abort(404);

        // Only Editors/Managers can close discussions
        if (!auth()->user()->hasJournalPermission([1, 2, 3], $journal->id)) {
            abort(403, 'You do not have permission to close discussions.');
        }

        $discussion->close(auth()->id());

        return back()->with('success', 'Discussion closed successfully.');
    }

    /**
     * Reopen a discussion.
     * Only Editors can reopen discussions.
     */
    public function reopen(Request $request, string $journalSlug, Submission $submission, Discussion $discussion)
    {
        $journal = $this->getJournal();
        if ($submission->journal_id !== $journal->id) abort(404);
        if ($discussion->submission_id !== $submission->id) abort(404);

        // Only Editors/Managers can reopen discussions
        if (!auth()->user()->hasJournalPermission([1, 2, 3], $journal->id)) {
            abort(403, 'You do not have permission to reopen discussions.');
        }

        $discussion->reopen();

        return back()->with('success', 'Discussion reopened.');
    }

    /**
     * Notify all participants except the sender.
     */
    private function notifyParticipants(Discussion $discussion, DiscussionMessage $message, string $excludeUserId): void
    {
        $discussion->load('participants');

        $participantsToNotify = $discussion->participants
            ->reject(fn($user) => $user->id === $excludeUserId);

        $sender = auth()->user();

        foreach ($participantsToNotify as $participant) {
            try {
                // Send email notification
                $participant->notify(new NewDiscussionMessageNotification($discussion, $message, $sender));

                // Send WhatsApp notification
                WaGateway::sendTemplate($participant, 'discussion_message', [
                    'name' => $participant->name,
                    'subject' => $discussion->subject,
                    'title' => $discussion->submission->title ?? 'Naskah',
                ], $discussion->submission?->journal_id);
            } catch (\Exception $e) {
                // Log but don't fail the request
                Log::warning('Failed to send discussion notification', [
                    'discussion_id' => $discussion->id,
                    'participant_id' => $participant->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Handle CKEditor Image Upload.
     */
    public function uploadCkeditorImage(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads/ckeditor', $filename, 'local');
            $url = route('files.serve.ckeditor', ['filename' => $filename]);

            return response()->json([
                'url' => $url,
                'uploaded' => true
            ]);
        }
        return response()->json(['uploaded' => false, 'error' => ['message' => 'No file uploaded']]);
    }

    /**
     * Upload Discussion File (Wizard Step 1).
     */
    public function uploadDiscussionFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240', // 10MB
        ]);

        $file = $request->file('file');
        $path = $file->store('discussion-files', 'local');

        $discussionFile = DiscussionFile::create([
            'id' => (string) Str::uuid(),
            'user_id' => auth()->id(),
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'file_type' => 'document',
            'file_size' => $file->getSize(),
        ]);

        return response()->json([
            'id' => $discussionFile->id,
            'name' => $discussionFile->original_name,
            'size' => $discussionFile->file_size,
            'url' => route('journal.discussion.file.download', ['journal' => $this->getJournal()->slug, 'file' => $discussionFile->id]),
        ]);
    }

    /**
     * Download a discussion file.
     */
    public function download(string $journalSlug, DiscussionFile $file)
    {
        $user = auth()->user();
        $journal = $this->getJournal();
        $canDownload = false;

        // 1. Owner always has access
        if ($file->user_id === $user->id) {
            $canDownload = true;
        }
        // 2. Journal Staff (Editors, Managers, etc.)
        elseif ($user->hasJournalPermission([1, 2, 3, 4, 16], $journal->id)) {
            $canDownload = true;
        }
        // 3. Discussion Participants
        elseif ($file->discussionMessage && $file->discussionMessage->discussion) {
            $discussion = $file->discussionMessage->discussion;

            // Ensure discussion belongs to this journal
            if ($discussion->submission->journal_id === $journal->id) {
                if ($discussion->hasParticipant($user->id)) {
                    $canDownload = true;
                }
            }
        }

        if (!$canDownload) {
            abort(403);
        }

        $disk = 'public'; // samakan dengan upload

        if (!Storage::disk($disk)->exists($file->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk($disk)
            ->download($file->file_path, $file->original_name);
    }

    /**
     * Mark a discussion as read.
     */
    public function markAsRead(Request $request, string $journalSlug, $submission, Discussion $discussion)
    {
        $currentUserId = auth()->id();
        
        // Ensure user is a participant
        $participant = $discussion->participantRecords()->where('user_id', $currentUserId)->first();
        
        if ($participant) {
            $participant->update(['last_read_at' => now()]);
        }
        
        return response()->json(['success' => true]);
    }

}
