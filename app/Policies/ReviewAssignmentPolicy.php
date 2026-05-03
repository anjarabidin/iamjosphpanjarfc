<?php

namespace App\Policies;

use App\Models\ReviewAssignment;
use App\Models\User;

class ReviewAssignmentPolicy
{
    /**
     * Determine if the user can view the review assignment.
     */
    public function view(User $user, ReviewAssignment $review): bool
    {
        // Assigned reviewer can view
        if ($review->reviewer_id === $user->id) {
            return true;
        }

        // Editor/Admin can view all
        if ($user->hasAnyRole(['Editor', 'Admin', 'Super Admin'])) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can respond to the invitation (accept/decline).
     */
    public function respond(User $user, ReviewAssignment $review): bool
    {
        return $review->reviewer_id === $user->id
            && $review->status === ReviewAssignment::STATUS_PENDING;
    }

    /**
     * Determine if the user can submit a review.
     */
    public function submit(User $user, ReviewAssignment $review): bool
    {
        return $review->reviewer_id === $user->id
            && $review->status === ReviewAssignment::STATUS_ACCEPTED;
    }

    /**
     * Determine if the user can create review assignments.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Editor', 'Admin', 'Super Admin']);
    }

    /**
     * Determine if the user can cancel the review assignment.
     */
    public function cancel(User $user, ReviewAssignment $review): bool
    {
        return $user->hasAnyRole(['Editor', 'Admin', 'Super Admin']);
    }
}
