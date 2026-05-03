<?php

namespace App\Http\Controllers;

use App\Models\ReviewAssignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    /**
     * Display pending reviews for current reviewer.
     */
    public function pending(): View
    {
        $user = auth()->user();

        $reviews = ReviewAssignment::where('reviewer_id', $user->id)
            ->pending()
            ->with(['submission.journal', 'submission.section'])
            ->latest('assigned_at')
            ->paginate(10);

        $overdueCount = ReviewAssignment::where('reviewer_id', $user->id)
            ->overdue()
            ->count();

        return view('reviews.pending', compact('reviews', 'overdueCount'));
    }

    /**
     * Display completed reviews for current reviewer.
     */
    public function completed(): View
    {
        $user = auth()->user();

        $reviews = ReviewAssignment::where('reviewer_id', $user->id)
            ->completed()
            ->with(['submission.journal', 'submission.section'])
            ->latest('completed_at')
            ->paginate(10);

        return view('reviews.completed', compact('reviews'));
    }

    /**
     * Show review form for a specific assignment.
     */
    public function show(ReviewAssignment $review): View
    {
        $this->authorize('view', $review);

        $review->load([
            'submission.journal',
            'submission.section',
            'submission.authors',
            'submission.files' => function ($query) {
                $query->where('file_type', 'manuscript')
                    ->latestVersion();
            }
        ]);

        return view('reviews.show', compact('review'));
    }

    /**
     * Accept review invitation.
     */
    public function accept(ReviewAssignment $review): RedirectResponse
    {
        $this->authorize('respond', $review);

        if ($review->status !== ReviewAssignment::STATUS_PENDING) {
            return back()->with('error', 'This invitation has already been responded to.');
        }

        $review->update([
            'status' => ReviewAssignment::STATUS_ACCEPTED,
            'responded_at' => now(),
        ]);

        return redirect()->route('reviews.show', $review)
            ->with('success', 'Review invitation accepted. You can now submit your review.');
    }

    /**
     * Decline review invitation.
     */
    public function decline(Request $request, ReviewAssignment $review): RedirectResponse
    {
        $this->authorize('respond', $review);

        if ($review->status !== ReviewAssignment::STATUS_PENDING) {
            return back()->with('error', 'This invitation has already been responded to.');
        }

        $metadata = $review->metadata ?? [];
        $metadata['decline_reason'] = $request->reason;

        $review->update([
            'status' => ReviewAssignment::STATUS_DECLINED,
            'responded_at' => now(),
            'metadata' => $metadata,
        ]);

        return redirect()->route('reviews.pending')
            ->with('success', 'Review invitation declined.');
    }

    /**
     * Submit the review.
     */
    public function submit(Request $request, ReviewAssignment $review): RedirectResponse
    {
        $this->authorize('submit', $review);

        if ($review->status !== ReviewAssignment::STATUS_ACCEPTED) {
            return back()->with('error', 'You must accept the invitation before submitting a review.');
        }

        $validated = $request->validate([
            'recommendation' => 'required|in:accept,minor_revision,major_revision,reject',
            'comments_for_author' => 'required|string|min:50',
            'comments_for_editor' => 'nullable|string',
            'quality_rating' => 'nullable|integer|min:1|max:5',
        ]);

        $review->update([
            'status' => ReviewAssignment::STATUS_COMPLETED,
            'recommendation' => $validated['recommendation'],
            'comments_for_author' => $validated['comments_for_author'],
            'comments_for_editor' => $validated['comments_for_editor'],
            'quality_rating' => $validated['quality_rating'],
            'completed_at' => now(),
        ]);

        return redirect()->route('reviews.completed')
            ->with('success', 'Review submitted successfully. Thank you for your contribution!');
    }
}
