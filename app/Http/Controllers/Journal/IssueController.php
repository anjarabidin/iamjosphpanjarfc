<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    /**
     * Show the form for creating a new issue
     */
    public function create()
    {
        $journal = current_journal();

        // Get the latest issue to suggest next values
        $latestIssue = Issue::where('journal_id', $journal->id)
            ->orderBy('year', 'desc')
            ->orderBy('volume', 'desc')
            ->orderBy('number', 'desc')
            ->first();

        // Suggest next issue values
        $suggestedYear = date('Y');
        $suggestedVolume = $latestIssue ? $latestIssue->volume : 1;
        $suggestedNumber = $latestIssue ? $latestIssue->number + 1 : 1;

        return view('editor.issues.create', compact(
            'journal',
            'suggestedYear',
            'suggestedVolume',
            'suggestedNumber'
        ));
    }

    /**
     * Display a listing of issues for the current journal
     */
    public function index()
    {
        $journal = current_journal();

        // Stats
        $totalIssues = Issue::where('journal_id', $journal->id)->count();
        $publishedCount = Issue::where('journal_id', $journal->id)->where('is_published', true)->count();
        $upcomingCount = Issue::where('journal_id', $journal->id)->where('is_published', false)->count();

        // Count all articles in all issues (or you can refine to published only if needed)
        // Assuming 'submissions' relation exists on Issue
        $totalArticles = Issue::where('journal_id', $journal->id)
            ->withCount('submissions')
            ->get()
            ->sum('submissions_count');

        // Future Issues (all of them, usually few)
        $futureIssues = Issue::where('journal_id', $journal->id)
            ->where('is_published', false)
            ->orderBy('year', 'asc') // Upcoming should probably be ascending or descending? Usually upcoming is nearest first.
            ->orderBy('volume', 'asc')
            ->orderBy('number', 'asc')
            ->withCount('submissions')
            ->get();

        // Back Issues (Published, paginated)
        $backIssues = Issue::where('journal_id', $journal->id)
            ->where('is_published', true)
            ->orderBy('published_at', 'desc') // Published recently first
            ->orderBy('year', 'desc')
            ->orderBy('volume', 'desc')
            ->orderBy('number', 'desc')
            ->withCount('submissions')
            ->paginate(12);

        return view('editor.issues.index', compact(
            'journal',
            'futureIssues',
            'backIssues',
            'totalIssues',
            'publishedCount',
            'upcomingCount',
            'totalArticles'
        ));
    }

    /**
     * Store a newly created issue
     */
    public function store(Request $request)
    {
        $journal = current_journal();

        $validated = $request->validate([
            'volume' => 'required|integer|min:1',
            'number' => 'required|integer|min:1',
            'year' => 'required|integer|min:1900|max:2100',
            'title' => 'nullable|string|max:255',
            'show_volume' => 'nullable|boolean',
            'show_number' => 'nullable|boolean',
            'show_year' => 'nullable|boolean',
            'show_title' => 'nullable|boolean',
            'description' => 'nullable|string',
            'url_path' => ['nullable', 'string', 'alpha_dash', 'unique:issues,url_path,NULL,id,journal_id,' . $journal->id],
            'cover' => 'nullable|image|max:2048',
        ]);

        $issueData = [
            'journal_id' => $journal->id,
            'volume' => $validated['volume'],
            'number' => $validated['number'],
            'year' => $validated['year'],
            'title' => $validated['title'] ?? null,
            'show_volume' => $request->boolean('show_volume', true),
            'show_number' => $request->boolean('show_number', true),
            'show_year' => $request->boolean('show_year', true),
            'show_title' => $request->boolean('show_title', false),
            'description' => $validated['description'] ?? null,
            'url_path' => $validated['url_path'] ?? null,
            'is_published' => false,
        ];

        // Handle cover image upload
        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->store('issues/covers', 'public');
            $issueData['cover_path'] = $path;
        }

        $issue = Issue::create($issueData);

        return redirect()
            ->route('journal.issues.index', ['journal' => $journal->slug])
            ->with('success', 'Issue created successfully.');
    }

    /**
     * Update the specified issue
     */
    public function update(Request $request, Issue $issue)
    {
        $journal = current_journal();

        $validated = $request->validate([
            'volume' => 'required|integer|min:1',
            'number' => 'required|integer|min:1',
            'year' => 'required|integer|min:1900|max:2100',
            'title' => 'nullable|string|max:255',
            'show_volume' => 'nullable|boolean',
            'show_number' => 'nullable|boolean',
            'show_year' => 'nullable|boolean',
            'show_title' => 'nullable|boolean',
            'description' => 'nullable|string',
            'url_path' => ['nullable', 'string', 'alpha_dash', 'unique:issues,url_path,' . $issue->id . ',id,journal_id,' . $journal->id],
            'cover' => 'nullable|image|max:2048',
        ]);

        $issueData = [
            'volume' => $validated['volume'],
            'number' => $validated['number'],
            'year' => $validated['year'],
            'title' => $validated['title'] ?? null,
            'show_volume' => $request->boolean('show_volume', true),
            'show_number' => $request->boolean('show_number', true),
            'show_year' => $request->boolean('show_year', true),
            'show_title' => $request->boolean('show_title', false),
            'description' => $validated['description'] ?? null,
            'url_path' => $validated['url_path'] ?? null,
        ];

        // Handle cover image upload
        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->store('issues/covers', 'public');
            $issueData['cover_path'] = $path;
        }

        $issue->update($issueData);

        return back()->with('success', 'Issue updated successfully.');
    }

    /**
     * Publish the issue
     */
    public function publish(Issue $issue)
    {
        $issue->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        return back()->with('success', "Issue {$issue->identifier} has been published.");
    }

    /**
     * Unpublish the issue
     */
    public function unpublish(Issue $issue)
    {
        $issue->update([
            'is_published' => false,
            'published_at' => null,
        ]);

        return back()->with('success', "Issue {$issue->identifier} has been unpublished.");
    }

    /**
     * Remove the specified issue
     */
    public function destroy(Issue $issue)
    {
        // Check if issue has any submissions
        if ($issue->submissions()->exists()) {
            return back()->with('error', 'Cannot delete issue with assigned submissions.');
        }

        $issue->delete();

        return back()->with('success', 'Issue deleted successfully.');
    }
}
