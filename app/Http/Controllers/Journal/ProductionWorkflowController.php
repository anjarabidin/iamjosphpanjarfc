<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Models\PublicationGalley;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Services\FileUploadSecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductionWorkflowController extends Controller
{
    public function __construct(
        protected FileUploadSecurityService $uploadSecurity
    ) {}
    /**
     * Store a new galley for the submission
     * Supports both local file uploads and remote URLs
     */
    public function storeGalley(Request $request, $journal, Submission $submission)
    {
        // Base validation rules
        $rules = [
            'label' => 'required|string|max:50',
            'locale' => 'nullable|string|max:5',
            'url_path' => [
                'nullable',
                'string',
                'max:100',
                'alpha_dash',
                // Unique within the submission
                function ($attribute, $value, $fail) use ($submission) {
                    if ($value && PublicationGalley::where('submission_id', $submission->id)
                        ->where('url_path', $value)
                        ->exists()
                    ) {
                        $fail('This URL path is already used by another galley in this submission.');
                    }
                },
            ],
            'is_remote' => 'boolean',
        ];

        // Conditional validation based on is_remote
        $isRemote = $request->boolean('is_remote');

        if ($isRemote) {
            $rules['url_remote'] = 'required|url|max:2048';
        } else {
            $rules['file'] = 'required|file';
        }

        $request->validate($rules);

        // Security: validate file via FileUploadSecurityService
        if (!$isRemote && $request->hasFile('file')) {
            $this->uploadSecurity->validate($request->file('file'), 'galley', $request);
        }

        $fileId = null;

        // Handle file upload if not remote
        if (!$isRemote && $request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            // Generate unique filename
            $filename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME))
                . '-' . Str::random(8)
                . '.' . $extension;

            // Store file in galleys directory
            $path = $file->storeAs(
                "journals/{$submission->journal_id}/galleys/{$submission->id}",
                $filename,
                'local'
            );

            // Create SubmissionFile record
            $submissionFile = SubmissionFile::create([
                'submission_id' => $submission->id,
                'uploaded_by' => auth()->id(),
                'file_name' => $originalName,
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'file_type' => 'galley',
                'stage' => SubmissionFile::STAGE_PRODUCTION,
            ]);

            $fileId = $submissionFile->id;
        }

        // Create PublicationGalley record
        $galley = PublicationGalley::create([
            'submission_id' => $submission->id,
            'file_id' => $fileId,
            'label' => strtoupper($request->label),
            'locale' => $request->locale ?? 'en',
            'url_path' => $request->url_path ?: null,
            'url_remote' => $isRemote ? $request->url_remote : null,
            'is_remote' => $isRemote,
            'seq' => $submission->galleys()->count(),
        ]);

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Galley '{$galley->label}' has been created successfully.",
                'galley' => $galley->load('file'),
            ]);
        }

        return redirect()->route('journal.workflow.show', [
            'journal' => $journal,
            'submission' => $submission->slug,
            'tab' => 'publication',
            'subtab' => 'galleys'
        ])->with('success', "Galley '{$galley->label}' has been created successfully.");
    }

    /**
     * Update galley details
     * Supports switching between local and remote
     */
    public function updateGalley(Request $request, $journal, Submission $submission, PublicationGalley $galley)
    {
        // Base validation rules
        $rules = [
            'label' => 'required|string|max:50',
            'locale' => 'nullable|string|max:5',
            'url_path' => [
                'nullable',
                'string',
                'max:100',
                'alpha_dash',
                // Unique within the submission (excluding current galley)
                function ($attribute, $value, $fail) use ($submission, $galley) {
                    if ($value && PublicationGalley::where('submission_id', $submission->id)
                        ->where('url_path', $value)
                        ->where('id', '!=', $galley->id)
                        ->exists()
                    ) {
                        $fail('This URL path is already used by another galley in this submission.');
                    }
                },
            ],
            'is_remote' => 'boolean',
        ];

        // Conditional validation based on is_remote
        $isRemote = $request->boolean('is_remote');

        if ($isRemote) {
            $rules['url_remote'] = 'required|url|max:2048';
        } else {
            // File is optional on update (keep existing if not provided)
            $rules['file'] = 'nullable|file';
        }

        $request->validate($rules);

        // Security: validate file via FileUploadSecurityService
        if (!$isRemote && $request->hasFile('file')) {
            $this->uploadSecurity->validate($request->file('file'), 'galley', $request);
        }

        $updateData = [
            'label' => strtoupper($request->label),
            'locale' => $request->locale ?? $galley->locale,
            'url_path' => $request->url_path ?: null,
            'is_remote' => $isRemote,
        ];

        // Handle remote URL
        if ($isRemote) {
            $updateData['url_remote'] = $request->url_remote;

            // Clean up old file if switching from local to remote
            if ($galley->file && !$galley->is_remote) {
                Storage::disk('local')->delete($galley->file->file_path);
                $galley->file->delete();
                $updateData['file_id'] = null;
            }
        } else {
            $updateData['url_remote'] = null;

            // Handle new file upload
            if ($request->hasFile('file')) {
                // Delete old file if exists
                if ($galley->file) {
                    Storage::disk('local')->delete($galley->file->file_path);
                    $galley->file->delete();
                }

                $file = $request->file('file');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();

                $filename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME))
                    . '-' . Str::random(8)
                    . '.' . $extension;

                $path = $file->storeAs(
                    "journals/{$submission->journal_id}/galleys/{$submission->id}",
                    $filename,
                    'local'
                );

                $submissionFile = SubmissionFile::create([
                    'submission_id' => $submission->id,
                    'uploaded_by' => auth()->id(),
                    'file_name' => $originalName,
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'file_type' => 'galley',
                    'stage' => SubmissionFile::STAGE_PRODUCTION,
                ]);

                $updateData['file_id'] = $submissionFile->id;
            }
        }

        $galley->update($updateData);

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Galley updated successfully.',
                'galley' => $galley->fresh()->load('file'),
            ]);
        }

        return redirect()->route('journal.workflow.show', [
            'journal' => $journal,
            'submission' => $submission->slug,
            'tab' => 'publication',
            'subtab' => 'galleys'
        ])->with('success', 'Galley updated successfully.');
    }

    /**
     * Delete a galley
     */
    public function destroyGalley($journal, Submission $submission, PublicationGalley $galley)
    {
        // Delete the associated file from storage if it's a local file
        if (!$galley->is_remote && $galley->file) {
            Storage::disk('local')->delete($galley->file->file_path);
            $galley->file->delete();
        }

        $galley->delete();
        
        return redirect()->route('journal.workflow.show', [
            'journal' => $journal,
            'submission' => $submission->slug,
            'tab' => 'publication',
            'subtab' => 'galleys'
        ])->with('success', 'Galley has been removed.');
    }


    /**
     * Assign submission to an issue
     */
    public function assignToIssue(Request $request, $journal, Submission $submission)
    {
        $request->validate([
            'issue_id' => 'required|uuid|exists:issues,id',
        ]);

        $issue = Issue::findOrFail($request->issue_id);

        $submission->update([
            'issue_id' => $issue->id,
        ]);

        return back()->with('success', "Submission scheduled for {$issue->identifier}.");
    }

    /**
     * Unschedule (remove from issue)
     */
    public function unschedule($journal, Submission $submission)
    {
        $submission->update([
            'issue_id' => null,
        ]);

        return back()->with('success', 'Submission has been unscheduled.');
    }

    /**
     * Publish the submission
     */
    public function publish(Request $request, $journal, Submission $submission)
    {
        // Validate prerequisites
        if (!$submission->hasGalleys()) {
            return back()->with('error', 'Cannot publish: No galley files uploaded. Please upload at least one galley (PDF) first.');
        }

        if (!$submission->issue_id) {
            return back()->with('error', 'Cannot publish: Submission is not assigned to an issue. Please schedule for publication first.');
        }

        // Update submission status
        $submission->update([
            'status' => Submission::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        // TODO: Trigger SubmissionPublished event to notify author
        // event(new SubmissionPublished($submission));

        return back()->with('success', 'Submission has been published successfully!');
    }

    /**
     * Unpublish the submission
     */
    public function unpublish($journal, Submission $submission)
    {
        $submission->update([
            'status' => Submission::STATUS_ACCEPTED, // Revert to accepted
            'published_at' => null,
        ]);

        return back()->with('success', 'Submission has been unpublished.');
    }

    /**
     * Get issues for dropdown (API endpoint)
     */
    public function getIssues($journal)
    {
        $journalModel = current_journal();

        $issues = Issue::where('journal_id', $journalModel->id)
            ->orderBy('year', 'desc')
            ->orderBy('volume', 'desc')
            ->orderBy('number', 'desc')
            ->get()
            ->map(function ($issue) {
                return [
                    'id' => $issue->id,
                    'label' => $issue->identifier,
                    'title' => $issue->title,
                    'is_published' => $issue->is_published,
                    'status_label' => $issue->is_published ? 'Published' : 'Future',
                ];
            });

        return response()->json($issues);
    }
}
