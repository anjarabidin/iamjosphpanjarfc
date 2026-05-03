<?php

namespace App\Http\Controllers\Admin\Stats;

use App\Http\Controllers\Controller;
use App\Models\JournalUserRole;
use App\Models\ReviewAssignment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserStatsController extends Controller
{
    /**
     * Display the User Statistics dashboard.
     */
    public function index()
    {
        $journal = current_journal();

        if (!$journal) {
            abort(404, 'Journal not found');
        }

        return view('manager.statistics.users', [
            'journal' => $journal,
        ]);
    }

    /**
     * Get user statistics data as JSON.
     */
    public function getData(Request $request)
    {
        $journal = current_journal();

        if (!$journal) {
            return response()->json(['error' => 'Journal not found'], 404);
        }

        // =====================================================
        // BASE QUERY: Users belonging to this journal
        // =====================================================

        // Get user IDs that have a role in this journal
        $journalUserIds = JournalUserRole::where('journal_id', $journal->id)
            ->distinct()
            ->pluck('user_id');

        // =====================================================
        // KPI CALCULATIONS
        // =====================================================

        // 1. Total Users in this journal
        $totalUsers = User::whereIn('id', $journalUserIds)->count();

        // 2. New Users (registered in last 30 days)
        $newUsers = User::whereIn('id', $journalUserIds)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        // 3. Active Users (logged in within last 90 days)
        $activeUsers = User::whereIn('id', $journalUserIds)
            ->where('date_last_login', '>=', now()->subDays(90))
            ->count();

        // =====================================================
        // ROLE DISTRIBUTION (for Donut Chart)
        // =====================================================

        $roleDistribution = DB::table('journal_user_roles')
            ->join('roles', 'journal_user_roles.role_id', '=', 'roles.id')
            ->where('journal_user_roles.journal_id', $journal->id)
            ->select('roles.name', DB::raw('count(*) as count'))
            ->groupBy('roles.id', 'roles.name')
            ->orderByDesc('count')
            ->get();

        // =====================================================
        // GROWTH CHART (PostgreSQL: TO_CHAR for date grouping)
        // =====================================================

        $growth = User::whereIn('id', $journalUserIds)
            ->where('created_at', '>=', now()->subYear())
            ->selectRaw("TO_CHAR(created_at, 'YYYY-MM') as month, count(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // =====================================================
        // TOP REVIEWERS LEADERBOARD
        // =====================================================

        $topReviewers = User::whereIn('users.id', $journalUserIds)
            ->select('users.id', 'users.name', 'users.given_name', 'users.family_name', 'users.avatar')
            ->withCount(['reviewAssignments as completed_reviews' => function ($query) use ($journal) {
                $query->where('review_assignments.status', ReviewAssignment::STATUS_COMPLETED)
                    ->whereHas('submission', function ($q) use ($journal) {
                        $q->where('journal_id', $journal->id);
                    });
            }])
            ->whereHas('reviewAssignments', function ($query) use ($journal) {
                $query->where('status', ReviewAssignment::STATUS_COMPLETED)
                    ->whereHas('submission', function ($q) use ($journal) {
                        $q->where('journal_id', $journal->id);
                    });
            })
            ->orderByDesc('completed_reviews')
            ->take(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name ?: trim($user->given_name . ' ' . $user->family_name),
                    'avatar' => $user->avatar,
                    'count' => $user->completed_reviews,
                ];
            });

        // =====================================================
        // TOP AUTHORS (by submission count)
        // =====================================================

        $topAuthors = User::whereIn('users.id', $journalUserIds)
            ->select('users.id', 'users.name', 'users.given_name', 'users.family_name', 'users.avatar')
            ->withCount(['submissions' => function ($query) use ($journal) {
                $query->where('journal_id', $journal->id)
                    ->whereNotNull('submitted_at');
            }])
            ->whereHas('submissions', function ($query) use ($journal) {
                $query->where('journal_id', $journal->id)
                    ->whereNotNull('submitted_at');
            })
            ->orderByDesc('submissions_count')
            ->take(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name ?: trim($user->given_name . ' ' . $user->family_name),
                    'avatar' => $user->avatar,
                    'count' => $user->submissions_count,
                ];
            });

        // =====================================================
        // ACTIVITY BREAKDOWN (New vs Returning Users by Month)
        // =====================================================

        // Users who registered this year and belong to this journal
        $registrationsThisYear = User::whereIn('id', $journalUserIds)
            ->whereYear('created_at', now()->year)
            ->count();

        // Users who logged in this month
        $activeThisMonth = User::whereIn('id', $journalUserIds)
            ->whereMonth('date_last_login', now()->month)
            ->whereYear('date_last_login', now()->year)
            ->count();

        return response()->json([
            'kpi' => [
                'total' => $totalUsers,
                'new' => $newUsers,
                'active' => $activeUsers,
                'registered_this_year' => $registrationsThisYear,
                'active_this_month' => $activeThisMonth,
            ],
            'roles' => [
                'labels' => $roleDistribution->pluck('name'),
                'series' => $roleDistribution->pluck('count'),
            ],
            'growth' => [
                'categories' => $growth->pluck('month'),
                'data' => $growth->pluck('count'),
            ],
            'leaderboard' => [
                'reviewers' => $topReviewers,
                'authors' => $topAuthors,
            ],
        ]);
    }
}
