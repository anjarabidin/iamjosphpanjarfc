<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Journal;
use App\Models\User;


class ReviewerApiController extends Controller
{
    public function index(Request $request, $journal)
    {
        $search = $request->get('q');

        /*
        |--------------------------------------------------------------------------
        | Resolve journal (UUID vs slug) — AMAN UNTUK POSTGRES
        |--------------------------------------------------------------------------
        */
        if ($journal instanceof Journal) {
            $journalModel = $journal;
        } else {
            $journalModel = Journal::query()
                ->when(
                    Str::isUuid($journal),
                    fn ($q) => $q->where('id', $journal),
                    fn ($q) => $q->where('slug', $journal)
                )
                ->firstOrFail();
        }

        $journalId = $journalModel->id;

        /*
        |--------------------------------------------------------------------------
        | Exclude assigned reviewers
        |--------------------------------------------------------------------------
        */
        $submissionId = $request->get('submission_id');
        $excludeIds = [];

        if ($submissionId) {
            $submission = \App\Models\Submission::find($submissionId);
            if ($submission) {
                // Get current round (default to 1 if no round exists yet)
                $currentRound = $submission->currentReviewRound();
                $roundId = $currentRound ? $currentRound->id : null;
                
                // If there is a round, check assignments
                if ($roundId) {
                     $excludeIds = \App\Models\ReviewAssignment::where('submission_id', $submission->id)
                        ->where('review_round_id', $roundId)
                        ->whereNotIn('status', ['cancelled', 'declined'])
                        ->pluck('reviewer_id')
                        ->toArray();
                } else {
                    // Check if there are assignments for round 1 even if round record doesn't exist (edge case, but likely round 1 exists if assignments do)
                     $excludeIds = \App\Models\ReviewAssignment::where('submission_id', $submission->id)
                        ->where('round', 1)
                        ->whereNotIn('status', ['cancelled', 'declined'])
                        ->pluck('reviewer_id')
                        ->toArray();
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Query reviewers
        |--------------------------------------------------------------------------
        */
        $reviewers = User::query()
            ->whereHas('journalRoles', function ($q) use ($journalId) {
                $q->where('journal_id', $journalId)
                ->whereHas('role', fn ($r) => $r->where('name', 'Reviewer'));
            })
            ->when(!empty($excludeIds), fn($q) => $q->whereNotIn('id', $excludeIds))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'ILIKE', "%{$search}%")
                        ->orWhere('email', 'ILIKE', "%{$search}%")
                        ->orWhere('affiliation', 'ILIKE', "%{$search}%");
                });
            })

            /*
            |--------------------------------------------------------------------------
            | Counts
            |--------------------------------------------------------------------------
            */
            ->withCount([
                'reviewAssignments as active_count' =>
                    fn ($q) => $q->whereIn('status', ['pending', 'accepted']),
                'reviewAssignments as completed_count' =>
                    fn ($q) => $q->where('status', 'completed'),
                'reviewAssignments as declined_count' =>
                    fn ($q) => $q->where('status', 'declined'),
                'reviewAssignments as cancelled_count' =>
                    fn ($q) => $q->where('status', 'cancelled'),
            ])

            /*
            |--------------------------------------------------------------------------
            | Average rating
            |--------------------------------------------------------------------------
            */
            ->withAvg(
                ['reviewAssignments as avg_rating' =>
                    fn ($q) => $q->whereNotNull('quality_rating')
                ],
                'quality_rating'
            )

            /*
            |--------------------------------------------------------------------------
            | Last assigned date
            |--------------------------------------------------------------------------
            */
            ->withMax('reviewAssignments as last_assigned_at', 'assigned_at')

            /*
            |--------------------------------------------------------------------------
            | Average completion days — POSTGRES WAY (NO DATEDIFF)
            |--------------------------------------------------------------------------
            */
            ->select('users.*')
            ->selectSub(function ($q) {
                $q->from('review_assignments')
                ->selectRaw(
                    'AVG(EXTRACT(EPOCH FROM (completed_at - assigned_at)) / 86400)'
                )
                ->whereColumn('review_assignments.reviewer_id', 'users.id')
                ->where('status', 'completed')
                ->whereNotNull('assigned_at')
                ->whereNotNull('completed_at')
                ->whereNull('review_assignments.deleted_at');
            }, 'avg_completion_days')

            ->limit(50)
            ->get()

            /*
            |--------------------------------------------------------------------------
            | Formatting output
            |--------------------------------------------------------------------------
            */
           ->map(function ($user) {
                $daysSinceLast = $user->last_assigned_at
                    ? Carbon::parse($user->last_assigned_at)->diffInDays(now())
                    : 0;

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'affiliation' => $user->affiliation,
                    // averages
                    'avg_rating' => round((float) ($user->avg_rating ?? 0), 2),
                    'avg_completion_days' => round((float) $user->avg_completion_days, 1),

                    // counts (already safe, but dipaksa biar konsisten)
                    'active_count' => (int) $user->active_count,
                    'completed_count' => (int) $user->completed_count,
                    'declined_count' => (int) $user->declined_count,
                    'cancelled_count' => (int) $user->cancelled_count,

                    // time
                    'days_since_last' => $daysSinceLast . ' days',
                ];
            })
            ->toArray();


        return response()->json($reviewers);
    }

}
