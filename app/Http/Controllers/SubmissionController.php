<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Issue;
use App\Models\Journal;
use App\Models\Section;
use Illuminate\View\View;
use App\Models\Discussion;
use App\Models\Submission;

use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use App\Models\SubmissionLog;
use App\Models\SubmissionFile;
use App\Models\SubmissionAuthor;
use App\Models\DiscussionMessage;
use Illuminate\Support\Facades\DB;
use App\Models\SubmissionChecklist;
use Illuminate\Support\Facades\Log;
use App\Models\DiscussionParticipant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SendSubmissionNotifications;
use App\Models\Role;
use App\Models\JournalUserRole;
use App\Models\Scopes\SubmissionQueryScope;

class SubmissionController extends Controller
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
     * Display a listing of submissions for current journal.
     * Role-based filtering: Authors see only their own, Editors see all with OJS 3.3 filters.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $journal = $this->getJournal();
        $filter = $request->get('filter', 'queue');

        // Determine if user has editor+ privileges (Journal Manager or Editor)
        $isEditor = $user->hasJournalPermission([1, 2], $journal->id);

        // Base query - restrict by journal, eager load relationships
        // Include discussions & reviewAssignments for OJS-style list (discussion count, reviewer X/Y)
        $query = Submission::where('journal_id', $journal->id)
            ->with([
                'journal',
                'section',
                'issue',
                'editorialAssignments.user',
                'authors',
                'discussions',        // For discussion count badge
                'reviewAssignments',  // For reviewer progress (X/Y)
            ])
            ->withCount('galleys');

        if (!$isEditor) {
            // === AUTHOR VIEW ===
            // Authors can ONLY see their own submissions
            $query->where('user_id', $user->id);

            if ($filter === 'archives') {
                $query->whereIn('status', [Submission::STATUS_PUBLISHED, Submission::STATUS_REJECTED]);
            } else {
                // Default: Active submissions (My Queue for authors)
                $query->whereNotIn('status', [Submission::STATUS_PUBLISHED, Submission::STATUS_REJECTED]);
            }
        } else {
            // === EDITOR+ VIEW ===
            switch ($filter) {
                case 'queue':
                    // My Queue: Submissions assigned to this editor
                    $query->whereHas('editorialAssignments', function ($q) use ($user) {
                        $q->where('user_id', $user->id)->where('is_active', true);
                    })->whereNotIn('status', [Submission::STATUS_PUBLISHED, Submission::STATUS_REJECTED]);
                    break;

                case 'unassigned':
                    // Unassigned: No active editor assigned, status = submitted
                    $query->where('status', Submission::STATUS_SUBMITTED)
                        ->whereDoesntHave('editorialAssignments', function ($q) {
                            $q->where('is_active', true);
                        });
                    break;

                case 'active':
                    // All Active: All non-archived submissions
                    $query->whereNotIn('status', [Submission::STATUS_PUBLISHED, Submission::STATUS_REJECTED]);
                    break;

                case 'archives':
                    // Archives: Published or Declined
                    $query->whereIn('status', [Submission::STATUS_PUBLISHED, Submission::STATUS_REJECTED]);
                    break;

                default:
                    // Fallback to queue
                    $query->whereHas('editorialAssignments', function ($q) use ($user) {
                        $q->where('user_id', $user->id)->where('is_active', true);
                    })->whereNotIn('status', [Submission::STATUS_PUBLISHED, Submission::STATUS_REJECTED]);
            }

            // === ADVANCED FILTERS (OJS 3.3 Style) ===
            
            // 1. Filter by Section
            if ($request->has('sections')) {
                $query->whereIn('section_id', $request->get('sections'));
            }

            // 2. Filter by Stage
            if ($request->has('stages')) {
                $query->whereIn('stage', $request->get('stages'));
            }

            // 3. Filter by Issue (Archives tab)
            if ($filter === 'archives' && $request->has('issue_ids')) {
                $query->whereIn('issue_id', $request->get('issue_ids'));
            }

            // 4. Search
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('submission_code', 'like', "%{$search}%")
                      ->orWhereHas('authors', function($aq) use ($search) {
                          $aq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            }
        }

        $submissions = $query->latest('submitted_at')->paginate(10);

        // Status counts for sidebar badges
        $statusCounts = $this->getStatusCounts($journal, $user, $isEditor);

        // Data for Filters
        $sections = Section::where('journal_id', $journal->id)->get();
        $issues = $filter === 'archives' 
            ? Issue::where('journal_id', $journal->id)->orderByDesc('year')->orderByDesc('volume')->get() 
            : collect();

        return view('submissions.index', compact(
            'submissions', 
            'statusCounts', 
            'filter', 
            'journal', 
            'isEditor',
            'sections',
            'issues'
        ));
    }

    /**
     * Get status counts for sidebar badges.
     */
    private function getStatusCounts(Journal $journal, $user, bool $isEditor): array
    {
        $base = Submission::where('journal_id', $journal->id);

        if (!$isEditor) {
            // Author counts - only their own submissions
            return [
                'active' => (clone $base)->where('user_id', $user->id)
                    ->whereNotIn('status', [Submission::STATUS_PUBLISHED, Submission::STATUS_REJECTED])->count(),
                'archives' => (clone $base)->where('user_id', $user->id)
                    ->whereIn('status', [Submission::STATUS_PUBLISHED, Submission::STATUS_REJECTED])->count(),
            ];
        }

        // Editor counts - full OJS 3.3 style
        return [
            'queue' => (clone $base)->whereHas(
                'editorialAssignments',
                fn($q) =>
                $q->where('user_id', $user->id)->where('is_active', true)
            )->whereNotIn('status', [Submission::STATUS_PUBLISHED, Submission::STATUS_REJECTED])->count(),

            'unassigned' => (clone $base)->where('status', Submission::STATUS_SUBMITTED)
                ->whereDoesntHave('editorialAssignments', fn($q) => $q->where('is_active', true))->count(),

            'active' => (clone $base)
                ->whereNotIn('status', [Submission::STATUS_PUBLISHED, Submission::STATUS_REJECTED])->count(),

            'archives' => (clone $base)
                ->whereIn('status', [Submission::STATUS_PUBLISHED, Submission::STATUS_REJECTED])->count(),
        ];
    }

    /**
     * Show the form for creating a new submission.
     */
    /**
     * Show the form for creating a new submission.
     */
    public function create(): View
    {
        $journal = $this->getJournal();

        // Get active sections
        $sections = Section::where('journal_id', $journal->id)
            ->active()
            ->ordered()
            ->get();

        // Get submission requirements
        $submissionChecklists = SubmissionChecklist::where('journal_id', $journal->id)
            ->ordered()
            ->get();

        return view('submissions.create', compact('journal', 'sections', 'submissionChecklists'));
    }

    /**
     * Handle TinyMCE image uploads.
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,gif,webp|max:5120', // 5MB max
        ]);

        $journal = $this->getJournal();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store("journals/{$journal->id}/images", 'public');

            return response()->json([
                'location' => Storage::url($path),
            ]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }

    /**
     * Store a newly created submission.
     */
    public function store(Request $request): RedirectResponse
    {
        $journal = $this->getJournal();
        $user = auth()->user();

        // Race Condition Handling: Lock the submission process for this user
        $lock = Cache::lock('submission_store_' . $user->id, 15);

        if (!$lock->get()) {
            return redirect()->route('journal.submissions.index', ['journal' => $journal->slug])
                ->with('error', 'Your submission is being processed. Please do not submit again.');
        }

        try {
            // Count required checklists to validate all are checked
            $requiredChecklistCount = SubmissionChecklist::where('journal_id', $journal->id)->count();

            $validated = $request->validate([
                'section_id' => 'required|uuid|exists:sections,id',
                'requirements' => $requiredChecklistCount > 0 ? ['required', 'array', "size:$requiredChecklistCount"] : 'nullable',
                'requirements.*' => 'required',
                'manuscript' => 'required|file|mimes:doc,docx,pdf|max:10240',
                'title' => 'required|string|max:500',
                'subtitle' => 'nullable|string|max:500',
                'abstract' => 'required|string',
                'keywords' => 'nullable|array',
                'keywords.*' => 'string|max:100',
                'references' => 'nullable|string',

                'authors' => 'required|array|min:1',
                'authors.*.first_name' => 'required|string|max:255',
                'authors.*.last_name' => 'required|string|max:255',
                'authors.*.email' => 'required|email|max:255',
                'authors.*.affiliation' => 'nullable|string|max:255',
                'authors.*.country' => 'nullable|string|max:100',

                'primary_contact' => 'required|integer|min:0',

                'comments_for_editor' => 'nullable|string|max:5000',
            ]);

            DB::beginTransaction();

            try {
                // 1. Create Submission
                $submission = Submission::create([
                    'journal_id' => $journal->id,
                    'user_id' => $user->id,
                    'section_id' => $validated['section_id'],
                    'title' => $validated['title'],
                    'subtitle' => $validated['subtitle'] ?? null,
                    'abstract' => $validated['abstract'],
                    'references' => $validated['references'] ?? null,
                    'status' => Submission::STATUS_SUBMITTED,
                    'stage' => Submission::STAGE_SUBMISSION,
                    'stage_id' => 1,
                    'submitted_at' => now(),
                ]);

                // 1.5 Sync Keywords (Many-to-Many)
                if (!empty($validated['keywords'])) {
                    $keywordIds = [];
                    foreach ($validated['keywords'] as $content) {
                        $content = trim($content);
                        if (empty($content)) {
                            continue;
                        }
                        $keyword = \App\Models\Keyword::firstOrCreate(['content' => $content]);
                        $keywordIds[] = $keyword->id;
                    }
                    $submission->keywords()->sync($keywordIds);
                }

                // 2. Upload File
                if ($request->hasFile('manuscript')) {
                    $file = $request->file('manuscript');
                    $path = $file->store("journals/{$journal->id}/submissions/{$submission->id}", 'local');

                    $submission->update(['submission_file_path' => $path]);

                    SubmissionFile::create([
                        'submission_id' => $submission->id,
                        'uploaded_by' => $user->id,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => SubmissionFile::TYPE_MANUSCRIPT,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'version' => 1,
                        'stage' => Submission::STAGE_SUBMISSION,
                    ]);
                }

                // 3. Save Authors
                foreach ($validated['authors'] as $index => $authorData) {
                    SubmissionAuthor::create([
                        'submission_id' => $submission->id,
                        'user_id' => ($authorData['email'] === $user->email) ? $user->id : null,
                        'first_name' => $authorData['first_name'],
                        'last_name' => $authorData['last_name'],
                        'name' => $authorData['first_name'] . ' ' . $authorData['last_name'],
                        'email' => $authorData['email'],
                        'affiliation' => $authorData['affiliation'] ?? null,
                        'country' => $authorData['country'] ?? null,
                        'is_primary_contact' => (int)$validated['primary_contact'] === $index,
                        'is_corresponding' => (int)$validated['primary_contact'] === $index,
                        'sort_order' => $index,
                    ]);
                }

                // 4. Create Discussion for "Comments for the Editor" (if provided)
                if (!empty($validated['comments_for_editor'])) {
                    $discussion = Discussion::create([
                        'submission_id' => $submission->id,
                        'user_id' => $user->id,
                        'subject' => 'Comments for the Editor',
                        'stage_id' => 1, // Submission stage
                        'is_open' => true,
                    ]);

                    // create discussion participant
                    DiscussionParticipant::create([
                        'discussion_id' => $discussion->id,
                        'user_id' => $user->id,
                    ]);

                    DiscussionMessage::create([
                        'discussion_id' => $discussion->id,
                        'user_id' => $user->id,
                        'body' => $validated['comments_for_editor'],
                    ]);
                }

                DB::commit();

                // Log the submission event
                SubmissionLog::log(
                    $submission,
                    SubmissionLog::EVENT_SUBMITTED,
                    'Submission Created',
                    "{$user->name} submitted the article for review.",
                    ['section' => $submission->section->title ?? null]
                );

                // ====== NOTIFICATIONS (Background Job) ======
                SendSubmissionNotifications::dispatch($submission, $user);

                return redirect()->route('journal.submissions.index', ['journal' => $journal->slug])
                    ->with('success', 'Submission created successfully! Your article is now under review.');
            } catch (\Exception $e) {
                DB::rollBack();

                // Log error for debugging
                Log::error('Submission creation failed', [
                    'user_id' => $user->id,
                    'journal_id' => $journal->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                return back()->withInput()
                    ->with('error', 'Failed to create submission. Please try again.');
            }
        } finally {
            $lock->release();
        }
    }

    /**
     * Display the specified submission.
     */
    public function show(string $journalSlug, Submission $submission): View
    {
        $journal = $this->getJournal();


        // Ensure submission belongs to this journal
        if ($submission->journal_id !== $journal->id) {
            abort(404);
        }

        // Ensure user can view this submission
        // $this->authorize('view', $submission);

        $user = auth()->user();

        // Determine if user is the author (owns the submission) and NOT an editor
        $isAuthorView = $submission->user_id === $user->id &&
            !$user->hasJournalPermission([1, 2], $journal->id);

        // Base eager loading (common for all roles)
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
            'discussions.participantRecords',
            'editorialAssignments.user',
        ]);

        // Role-specific loading
        if (!$isAuthorView) {
            // Editor/Admin view: Load reviewers and all review data
            $submission->load([
                'reviewAssignments.reviewer',
            ]);
        }

        $issues = Issue::where('journal_id', $journal->id)
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

        // Add the submission author
        if ($submission->author) {
            $participants->push($submission->author);
        }

        // Add assigned editors
        foreach ($submission->editorialAssignments->where('is_active', true) as $assignment) {
            if ($assignment->user && !$participants->contains('id', $assignment->user->id)) {
                $participants->push($assignment->user);
            }
        }

        // Add current user if not already in list
        if (!$participants->contains('id', $user->id)) {
            $participants->push($user);
        }

        // ========== AUTHOR-SPECIFIC DATA ==========
        $authorReviewData = [];
        if ($isAuthorView) {
            // 1. Promoted/Shared Files (Files the Editor shared with Author)
            // Only files that have the decision_type metadata (promoted by editor)
            // and NOT uploaded by the author themselves
            $authorReviewData['promotedFiles'] = SubmissionFile::where('submission_id', $submission->id)
                ->where('stage', 'revision')
                ->where('uploaded_by', '!=', $user->id) // Exclude author's own uploads
                ->whereJsonContains('metadata->decision_type', 'revision_request') // Only editor-promoted files
                ->orderBy('created_at', 'desc')
                ->get();

            // 2. Author's Revision Files (uploaded by the author)
            $authorReviewData['revisionFiles'] = SubmissionFile::where('submission_id', $submission->id)
                ->where('stage', 'revision')
                ->where('uploaded_by', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // 3. Decision History (from submission metadata)
            $authorReviewData['decisionHistory'] = collect($submission->metadata['decisions'] ?? [])
                ->sortByDesc('made_at')
                ->map(function ($decision) {
                    return [
                        'type' => $decision['type'] ?? 'decision',
                        'type_label' => match ($decision['type'] ?? 'decision') {
                            'revision_request' => 'Revisions Requested',
                            'accept' => 'Submission Accepted',
                            'decline' => 'Submission Declined',
                            default => 'Editorial Decision',
                        },
                        'made_at' => $decision['made_at'] ?? null,
                        'email_body' => $decision['email_body'] ?? null,
                        'new_review_round' => $decision['new_review_round'] ?? false,
                        'round' => $decision['round'] ?? 1,
                    ];
                })
                ->values();

            // 4. Current Review Round Info
            $authorReviewData['currentRound'] = $submission->currentReviewRound();

            // 5. All Review Rounds
            $authorReviewData['reviewRounds'] = $submission->reviewRounds()
                ->orderBy('round')
                ->get();
        }

        // 6. Google Scholar SEO Analysis
        $validator = new \App\Services\GoogleScholarValidator();
        $seoAnalysis = $validator->validate($submission);

        // 7. Potential Editors (for Assign Editor modal) and not assigned yet
        // Get IDs of users already assigned as editors (active only)
        $activeEditorIds = $submission->editorialAssignments
            ->where('is_active', true)
            ->pluck('user_id')
            ->filter()
            ->toArray();

        $potentialEditors = User::whereHas('journalRoles', function ($query) use ($journal) {
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
            // Get all role names for this journal
            $roles = $user->journalRoles->map(fn($jr) => $jr->role->name)->toArray();
            
            // Determine primary role for filtering (logic: Manager > Editor > Section Editor)
            // But for display, we might want to show specific ones.
            // Let's just join them for display and filtering
            $user->role_names = $roles;
            $user->role_display = implode(', ', $roles);
            return $user;
        });

        return view('submissions.show', array_merge(
            compact('submission', 'journal', 'issues', 'issueOptions', 'participants', 'isAuthorView', 'seoAnalysis', 'potentialEditors'),
            $isAuthorView ? ['authorReviewData' => $authorReviewData] : []
        ));
    }

    /**
     * Show the form for editing the submission.
     */
    public function edit(string $journalSlug, Submission $submission): View
    {
        $journal = $this->getJournal();

        // Ensure submission belongs to this journal
        if ($submission->journal_id !== $journal->id) {
            abort(404);
        }

        $this->authorize('update', $submission);

        if (!$submission->isEditable()) {
            abort(403, 'This submission cannot be edited.');
        }

        $sections = Section::where('journal_id', $journal->id)
            ->active()
            ->ordered()
            ->get();

        $submission->load(['authors', 'files']);

        return view('submissions.edit', compact('submission', 'sections', 'journal'));
    }

    /**
     * Update the specified submission.
     */
    public function update(Request $request, string $journalSlug, Submission $submission): RedirectResponse
    {
        $journal = $this->getJournal();

        // Ensure submission belongs to this journal
        if ($submission->journal_id !== $journal->id) {
            abort(404);
        }

        $this->authorize('update', $submission);

        if (!$submission->isEditable()) {
            return back()->with('error', 'This submission cannot be edited.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:500',
            'abstract' => 'required|string',
            'keywords' => 'nullable|array',
            'keywords.*' => 'string|max:100',
            'references' => 'nullable|string',
            'section_id' => 'required|uuid',
        ]);

        // Capture before state for audit diff
        $before = [
            'title'      => $submission->title,
            'abstract'   => $submission->abstract,
            'section_id' => $submission->section_id,
        ];

        // Update basic fields (excluding keywords)
        $submission->update([
            'title'      => $validated['title'],
            'abstract'   => $validated['abstract'],
            'references' => $validated['references'] ?? null,
            'section_id' => $validated['section_id'],
        ]);

        // Sync keywords (many-to-many)
        if (isset($validated['keywords'])) {
            $keywordIds = [];
            foreach ($validated['keywords'] as $content) {
                $content = trim($content);
                if (empty($content)) {
                    continue;
                }
                $keyword = \App\Models\Keyword::firstOrCreate(['content' => $content]);
                $keywordIds[] = $keyword->id;
            }
            $submission->keywords()->sync($keywordIds);
        }

        // Log metadata diff if any tracked field changed
        $after = [
            'title'      => $submission->title,
            'abstract'   => $submission->abstract,
            'section_id' => $submission->section_id,
        ];
        if ($before !== $after) {
            try {
                SubmissionLog::logMetadataDiff($submission, $before, $after);
            } catch (\Throwable $e) {
                Log::warning('SubmissionLog: failed to log metadata diff', ['error' => $e->getMessage()]);
            }
        }

        return redirect()->route('journal.submissions.show', ['journal' => $journal->slug, 'submission' => $submission])
            ->with('success', 'Submission updated successfully.');
    }

    /**
     * Remove the specified submission (soft delete).
     */
    public function destroy(string $journalSlug, Submission $submission): RedirectResponse
    {
        $journal = $this->getJournal();

        // Ensure submission belongs to this journal
        if ($submission->journal_id !== $journal->id) {
            abort(404);
        }

        $this->authorize('delete', $submission);

        if ($submission->status !== Submission::STATUS_DRAFT) {
            return back()->with('error', 'Only draft submissions can be deleted.');
        }

        $submission->delete();

        return redirect()->route('journal.submissions.index', ['journal' => $journal->slug])
            ->with('success', 'Submission deleted successfully.');
    }
}
