<?php

namespace App\Http\Controllers\Admin\Tools;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Models\Submission;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CopernicusExportController extends Controller
{
    /**
     * Display the Copernicus XML Exporter page.
     */
    public function index()
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found');
        }

        // Fetch published submissions for the articles tab
        $submissions = Submission::where('journal_id', $journal->id)
            ->where('status', Submission::STATUS_PUBLISHED)
            ->with(['authors', 'issue', 'section', 'currentPublication', 'galleys'])
            ->latest('published_at')
            ->get();

        // Validate completeness for Index Copernicus
        foreach ($submissions as $submission) {
            $submission->ici_missing_fields = $this->getMissingIciFields($submission);
        }

        // Fetch published issues for the issues tab
        $issues = Issue::where('journal_id', $journal->id)
            ->where('is_published', true)
            ->withCount('submissions')
            ->latest()
            ->get();

        return view('manager.tools.copernicus.index', compact('journal', 'submissions', 'issues'));
    }

    /**
     * Export selected articles as XML.
     */
    public function exportArticles(Request $request)
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found');
        }

        $ids = $request->input('submission_ids', []);

        if (empty($ids)) {
            return back()->with('error', 'Please select at least one article to export.');
        }

        $submissions = Submission::whereIn('id', $ids)
            ->where('journal_id', $journal->id)
            ->with(['authors', 'issue', 'section', 'currentPublication', 'galleys'])
            ->get();

        if ($submissions->isEmpty()) {
            return back()->with('error', 'No articles found.');
        }

        $filename = 'copernicus-articles-' . date('Ymd-His') . '.xml';

        return response()->streamDownload(function () use ($submissions, $journal) {
            $content = view('manager.tools.copernicus.article_xml', compact('submissions', 'journal'))->render();
            // Clean up XML spacing
            $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
            $content = trim($content);
            echo '<?xml version="1.0" encoding="utf-8"?>' . "\n" . $content;
        }, $filename, ['Content-Type' => 'application/xml']);
    }

    /**
     * Export selected issues as XML (including nested articles).
     */
    public function exportIssues(Request $request)
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found');
        }

        $ids = $request->input('issue_ids', []);

        if (empty($ids)) {
            return back()->with('error', 'Please select at least one issue to export.');
        }

        $issues = Issue::whereIn('id', $ids)
            ->where('journal_id', $journal->id)
            ->with(['submissions' => function($query) {
                // Preload article relationships when fetching issues
                $query->with(['authors', 'section', 'currentPublication', 'galleys']);
            }])
            ->get();

        if ($issues->isEmpty()) {
            return back()->with('error', 'No issues found.');
        }

        $filename = 'copernicus-issues-' . date('Ymd-His') . '.xml';

        return response()->streamDownload(function () use ($issues, $journal) {
            $content = view('manager.tools.copernicus.issue_xml', compact('issues', 'journal'))->render();
            // Clean up XML spacing
            $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
            $content = trim($content);
            echo '<?xml version="1.0" encoding="utf-8"?>' . "\n" . $content;
        }, $filename, ['Content-Type' => 'application/xml']);
    }

    /**
     * Helper to validate article structure against Index Copernicus requirements.
     */
    private function getMissingIciFields(Submission $submission): array
    {
        $missing = [];
        
        $doi = $submission->currentPublication?->doi ?? $submission->doi ?? '';
        if (empty($doi)) {
            $missing[] = 'DOI';
        }
        
        $abstract = $submission->currentPublication?->abstract ?? $submission->abstract ?? '';
        $cleanAbstract = trim(strip_tags(html_entity_decode(str_replace('&nbsp;', ' ', $abstract), ENT_QUOTES, 'UTF-8')));
        if (empty($cleanAbstract)) {
            $missing[] = 'Abstract';
        }
        
        $hasValidAuthor = false;
        foreach ($submission->authors as $author) {
            $firstName = $author->given_name ?? '';
            $lastName = $author->family_name ?? '';
            $name = $author->name ?? '';
            
            if ((!empty($firstName) || !empty($lastName) || !empty($name)) && !empty($author->email) && !empty($author->affiliation)) {
                $hasValidAuthor = true;
                break;
            }
        }
        if (!$hasValidAuthor) {
            $missing[] = 'Authors';
        }
        
        if ($submission->galleys->isEmpty()) {
            $missing[] = 'PDF';
        }
        
        $refs = $submission->currentPublication?->references ?? $submission->references;
        if (empty(trim($refs))) {
            $missing[] = 'References';
        }
        
        return $missing;
    }
}
