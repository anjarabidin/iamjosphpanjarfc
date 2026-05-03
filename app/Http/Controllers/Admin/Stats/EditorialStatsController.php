<?php

namespace App\Http\Controllers\Admin\Stats;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\SubmissionLog;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EditorialStatsController extends Controller
{
    /**
     * Display the Editorial Activity dashboard.
     */
    public function index()
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found');
        }

        return view('manager.statistics.editorial', [
            'journal' => $journal,
        ]);
    }

    /**
     * Get editorial statistics data as JSON.
     */
    public function getData(Request $request)
    {
        $journal = current_journal();

        if (!$journal) {
            return response()->json(['error' => 'Journal not found'], 404);
        }

        // Date range (default: last 12 months)
        $start = $request->get('start', now()->subYear()->toDateString());
        $end = $request->get('end', now()->toDateString());

        // =====================================================
        // KPI CALCULATIONS
        // =====================================================

        // 1. Total Submissions Received
        $totalReceived = Submission::where('journal_id', $journal->id)
            ->whereBetween('submitted_at', [$start, $end])
            ->whereNotNull('submitted_at')
            ->count();

        // 2. Accepted Count (status = accepted, scheduled, published, etc.)
        $acceptedCount = Submission::where('journal_id', $journal->id)
            ->whereBetween('submitted_at', [$start, $end])
            ->whereIn('status', [
                Submission::STATUS_ACCEPTED,
                Submission::STATUS_QUEUED_FOR_COPYEDITING,
                Submission::STATUS_IN_PRODUCTION,
                Submission::STATUS_SCHEDULED,
                Submission::STATUS_PUBLISHED,
            ])
            ->count();

        // 3. Rejected Count (desk reject + review reject)
        $rejectedCount = Submission::where('journal_id', $journal->id)
            ->whereBetween('submitted_at', [$start, $end])
            ->where('status', Submission::STATUS_REJECTED)
            ->count();

        // 4. Calculate Acceptance Rate
        $totalDecided = $acceptedCount + $rejectedCount;
        $acceptanceRate = $totalDecided > 0 ? round(($acceptedCount / $totalDecided) * 100, 1) : 0;

        // 5. Average Days to First Decision
        // First decision = first decision_made log entry after submission
        $avgDaysFirstDecision = $this->calculateAvgDaysToFirstDecision($journal->id, $start, $end);

        // 6. Average Days to Accept
        // Time from submitted_at to accepted_at
        $avgDaysAccept = Submission::where('journal_id', $journal->id)
            ->whereBetween('submitted_at', [$start, $end])
            ->whereNotNull('accepted_at')
            ->selectRaw('AVG(accepted_at::date - submitted_at::date) as avg_days')
            ->value('avg_days');

        // =====================================================
        // TREND DATA (Monthly)
        // =====================================================
        $startDate = Carbon::parse($start);
        $endDate = Carbon::parse($end);
        $period = CarbonPeriod::create($startDate->startOfMonth(), '1 month', $endDate->endOfMonth());

        $trendCategories = [];
        $trendReceived = [];
        $trendAccepted = [];
        $trendDeclined = [];

        foreach ($period as $month) {
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            $monthLabel = $month->format('M Y');

            $trendCategories[] = $monthLabel;

            // Received this month
            $received = Submission::where('journal_id', $journal->id)
                ->whereBetween('submitted_at', [$monthStart, $monthEnd])
                ->whereNotNull('submitted_at')
                ->count();
            $trendReceived[] = $received;

            // Accepted this month (by accepted_at date)
            $accepted = Submission::where('journal_id', $journal->id)
                ->whereBetween('accepted_at', [$monthStart, $monthEnd])
                ->count();
            $trendAccepted[] = $accepted;

            // Declined this month (by updated_at when status became rejected)
            $declined = Submission::where('journal_id', $journal->id)
                ->where('status', Submission::STATUS_REJECTED)
                ->whereBetween('updated_at', [$monthStart, $monthEnd])
                ->count();
            $trendDeclined[] = $declined;
        }

        // =====================================================
        // DECISION OUTCOMES (for Donut Chart)
        // =====================================================

        // Desk Reject: rejected at stage 1 (submission stage)
        $deskReject = Submission::where('journal_id', $journal->id)
            ->whereBetween('submitted_at', [$start, $end])
            ->where('status', Submission::STATUS_REJECTED)
            ->where('stage_id', 1)
            ->count();

        // Review Reject: rejected at stage 2+ (review stage)
        $reviewReject = Submission::where('journal_id', $journal->id)
            ->whereBetween('submitted_at', [$start, $end])
            ->where('status', Submission::STATUS_REJECTED)
            ->where('stage_id', '>', 1)
            ->count();

        // Withdrawn: we'll track this as draft after submission (simplified)
        $withdrawn = Submission::where('journal_id', $journal->id)
            ->whereBetween('submitted_at', [$start, $end])
            ->where('status', Submission::STATUS_DRAFT)
            ->whereNotNull('submitted_at')
            ->count();

        // In Progress (not yet decided)
        $inProgress = Submission::where('journal_id', $journal->id)
            ->whereBetween('submitted_at', [$start, $end])
            ->whereIn('status', [
                Submission::STATUS_SUBMITTED,
                Submission::STATUS_UNDER_REVIEW,
                Submission::STATUS_IN_REVIEW,
                Submission::STATUS_REVISION_REQUIRED,
            ])
            ->count();

        // =====================================================
        // EFFICIENCY TREND (Avg Days to Decision per Month)
        // =====================================================
        $efficiencyTrend = [];
        foreach ($period as $month) {
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            // Submissions decided this month
            $avgDays = $this->calculateAvgDaysToFirstDecision(
                $journal->id,
                $monthStart->toDateString(),
                $monthEnd->toDateString()
            );
            $efficiencyTrend[] = round($avgDays ?? 0);
        }

        return response()->json([
            'kpi' => [
                'received' => $totalReceived,
                'accepted' => $acceptedCount,
                'acceptance_rate' => $acceptanceRate,
                'avg_days_first' => round($avgDaysFirstDecision ?? 0),
                'avg_days_accept' => round($avgDaysAccept ?? 0),
            ],
            'trends' => [
                'categories' => $trendCategories,
                'received' => $trendReceived,
                'accepted' => $trendAccepted,
                'declined' => $trendDeclined,
            ],
            'efficiency' => [
                'categories' => $trendCategories,
                'data' => $efficiencyTrend,
            ],
            'outcomes' => [
                'labels' => ['Accepted', 'Desk Reject', 'Review Reject', 'In Progress', 'Withdrawn'],
                'data' => [$acceptedCount, $deskReject, $reviewReject, $inProgress, $withdrawn],
                'colors' => ['#10b981', '#f97316', '#ef4444', '#6366f1', '#94a3b8'],
            ],
        ]);
    }

    /**
     * Calculate average days to first editorial decision.
     */
    private function calculateAvgDaysToFirstDecision(string $journalId, string $start, string $end): ?float
    {
        // Get submissions with first decision log
        return DB::table('submissions')
            ->join('submission_logs', 'submissions.id', '=', 'submission_logs.submission_id')
            ->where('submissions.journal_id', $journalId)
            ->whereBetween('submissions.submitted_at', [$start, $end])
            ->whereNotNull('submissions.submitted_at')
            ->where('submission_logs.event_type', SubmissionLog::EVENT_DECISION_MADE)
            ->selectRaw('AVG(submission_logs.created_at::date - submissions.submitted_at::date) as avg_days')
            ->value('avg_days');
    }
}
