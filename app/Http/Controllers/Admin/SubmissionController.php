<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function logHistory($id)
    {
        $submission = Submission::with([
            'activityLogs.user', 
            'activityLogs.files',
            'notes.user'
        ])->findOrFail($id);

        // Return just the partial HTML, not the full page layout
        return view('admin.submissions.partials.log-modal-content', compact('submission'));
    }

    /**
     * Store a new internal note for this submission.
     */
    public function storeNote(Request $request, string $journalSlug, Submission $submission)
    {
        $user = auth()->user();
        $journal = \App\Models\Journal::where('slug', $journalSlug)->firstOrFail();

        // Must be Journal Manager (1) or Editor (2)
        if (!$user->hasJournalPermission([1, 2], $journal->id)) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'note' => 'required|string|max:10000',
        ]);

        $note = $submission->notes()->create([
            'user_id' => $user->id,
            'note' => $request->note,
        ]);

        \App\Models\SubmissionLog::log(
            $submission,
            \App\Models\SubmissionLog::EVENT_DISCUSSION_MESSAGE, // Closest matching event type or use custom string
            'Internal Note Added',
            "{$user->name} added an internal note to the submission."
        );

        return back()->with('success', 'Note added successfully.');
    }

    /**
     * Delete an internal note.
     */
    public function destroyNote(string $journalSlug, Submission $submission, \App\Models\SubmissionNote $note)
    {
        $user = auth()->user();
        $journal = \App\Models\Journal::where('slug', $journalSlug)->firstOrFail();

        // Must be Journal Manager (1) or Editor (2)
        if (!$user->hasJournalPermission([1, 2], $journal->id)) {
            abort(403, 'Unauthorized action.');
        }

        // Must be author of note, or a JM (admin)
        if ($note->user_id !== $user->id && !$user->hasJournalPermission([1], $journal->id)) {
            abort(403, 'You can only delete your own notes.');
        }

        $note->delete();

        return back()->with('success', 'Note deleted successfully.');
    }
}