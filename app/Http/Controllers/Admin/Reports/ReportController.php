<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\ArticleMetric;
use App\Models\ReviewAssignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Display the Report Center page.
     */
    public function index()
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found');
        }

        return view('manager.statistics.reports', [
            'journal' => $journal,
        ]);
    }

    /**
     * Preview report data (first 5 rows) as JSON.
     */
    public function preview(Request $request)
    {
        $journal = current_journal();

        if (!$journal) {
            return response()->json(['error' => 'Journal not found'], 404);
        }

        $query = $this->buildQuery($request, $journal);

        if (!$query) {
            return response()->json(['error' => 'Invalid report type'], 400);
        }

        $data = $query->limit(5)->get();

        return response()->json($data);
    }

    /**
     * Export report as CSV stream (memory efficient).
     */
    public function export(Request $request): StreamedResponse
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found');
        }

        $query = $this->buildQuery($request, $journal);

        if (!$query) {
            abort(400, 'Invalid report type');
        }

        $type = $request->input('type', 'report');
        $filename = $type . '_report_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // BOM for UTF-8 Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            $isHeaderWritten = false;

            // Chunk for memory efficiency (PostgreSQL compatible)
            $query->chunk(500, function ($rows) use ($handle, &$isHeaderWritten) {
                foreach ($rows as $row) {
                    $rowArray = $row instanceof \stdClass ? (array) $row : $row->toArray();

                    // Write header once
                    if (!$isHeaderWritten) {
                        fputcsv($handle, array_keys($rowArray));
                        $isHeaderWritten = true;
                    }

                    fputcsv($handle, array_values($rowArray));
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Build query based on report type.
     */
    private function buildQuery(Request $request, $journal)
    {
        $start = $request->input('start', now()->subYear()->toDateString());
        $end = $request->input('end', now()->toDateString());
        $type = $request->input('type');

        switch ($type) {
            case 'articles':
                return $this->buildArticlesQuery($journal, $start, $end);

            case 'reviews':
                return $this->buildReviewsQuery($journal, $start, $end);

            case 'usage':
                return $this->buildUsageQuery($journal, $start, $end);

            default:
                return null;
        }
    }

    /**
     * Build Articles Report Query.
     */
    private function buildArticlesQuery($journal, $start, $end)
    {
        return Submission::query()
            ->where('journal_id', $journal->id)
            ->whereBetween('submitted_at', [$start, $end])
            ->whereNotNull('submitted_at')
            ->leftJoin('sections', 'submissions.section_id', '=', 'sections.id')
            ->leftJoin('issues', 'submissions.issue_id', '=', 'issues.id')
            ->leftJoin('users', 'submissions.user_id', '=', 'users.id')
            ->select([
                'submissions.submission_code as code',
                'submissions.title',
                'users.name as author',
                'sections.name as section',
                'submissions.status',
                'submissions.stage',
                'issues.title as issue',
                DB::raw("TO_CHAR(submissions.submitted_at, 'YYYY-MM-DD') as submitted_at"),
                DB::raw("TO_CHAR(submissions.accepted_at, 'YYYY-MM-DD') as accepted_at"),
                DB::raw("TO_CHAR(submissions.published_at, 'YYYY-MM-DD') as published_at"),
            ])
            ->orderBy('submissions.submitted_at', 'desc');
    }

    /**
     * Build Reviews Report Query.
     */
    private function buildReviewsQuery($journal, $start, $end)
    {
        return ReviewAssignment::query()
            ->join('submissions', 'review_assignments.submission_id', '=', 'submissions.id')
            ->join('users', 'review_assignments.reviewer_id', '=', 'users.id')
            ->where('submissions.journal_id', $journal->id)
            ->whereBetween('review_assignments.created_at', [$start, $end])
            ->select([
                'submissions.submission_code as article_code',
                'submissions.title as article_title',
                'users.name as reviewer',
                'users.affiliation as reviewer_affiliation',
                'review_assignments.round',
                'review_assignments.status',
                'review_assignments.recommendation',
                DB::raw("TO_CHAR(review_assignments.assigned_at, 'YYYY-MM-DD') as assigned_at"),
                DB::raw("TO_CHAR(review_assignments.due_date, 'YYYY-MM-DD') as due_date"),
                DB::raw("TO_CHAR(review_assignments.responded_at, 'YYYY-MM-DD') as responded_at"),
                DB::raw("TO_CHAR(review_assignments.completed_at, 'YYYY-MM-DD') as completed_at"),
                DB::raw("CASE WHEN review_assignments.completed_at IS NOT NULL AND review_assignments.assigned_at IS NOT NULL 
                         THEN (review_assignments.completed_at::date - review_assignments.assigned_at::date) 
                         ELSE NULL END as days_taken"),
            ])
            ->orderBy('review_assignments.created_at', 'desc');
    }

    /**
     * Build Usage Report Query (COUNTER-style).
     */
    private function buildUsageQuery($journal, $start, $end)
    {
        return ArticleMetric::query()
            ->join('submissions', 'article_metrics.submission_id', '=', 'submissions.id')
            ->where('submissions.journal_id', $journal->id)
            ->whereBetween('article_metrics.date', [$start, $end])
            ->select([
                'submissions.submission_code as article_code',
                'submissions.title as article_title',
                'article_metrics.type as metric_type',
                DB::raw("TO_CHAR(article_metrics.date, 'YYYY-MM') as month"),
                DB::raw('COUNT(*) as count'),
            ])
            ->groupBy(
                'submissions.submission_code',
                'submissions.title',
                'article_metrics.type',
                DB::raw("TO_CHAR(article_metrics.date, 'YYYY-MM')")
            )
            ->orderBy(DB::raw("TO_CHAR(article_metrics.date, 'YYYY-MM')"), 'desc')
            ->orderBy('count', 'desc');
    }
}
