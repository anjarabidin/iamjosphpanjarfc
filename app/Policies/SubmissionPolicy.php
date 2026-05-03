<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    /**
     * Determine if the user can view any submissions.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view their submissions list
    }

    /**
     * Determine if the user can view the submission.
     */
    public function view(User $user, Submission $submission): bool
    {
        // Author can view own submissions
        if ($submission->user_id === $user->id) {
            return true;
        }

        // Editor/Admin can view all
        if ($user->hasAnyRole(['Editor', 'Admin', 'Super Admin'])) {
            return true;
        }

        // Reviewer can view assigned submissions
        if ($user->hasRole('Reviewer')) {
            return $submission->reviewAssignments()
                ->where('reviewer_id', $user->id)
                ->exists();
        }

        return false;
    }

    /**
     * Determine if the user can create submissions.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Author', 'Editor', 'Admin', 'Super Admin']);
    }

    /**
     * Determine if the user can update the submission.
     */
    public function update(User $user, Submission $submission): bool
    {
        // Only author can update, and only if editable
        if ($submission->user_id === $user->id && $submission->isEditable()) {
            return true;
        }

        // Editor/Admin can always update
        if ($user->hasAnyRole(['Editor', 'Admin', 'Super Admin'])) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can delete the submission.
     */
    public function delete(User $user, Submission $submission): bool
    {
        // Only author can delete drafts
        if ($submission->user_id === $user->id && $submission->status === Submission::STATUS_DRAFT) {
            return true;
        }

        // Admin can delete any
        if ($user->hasAnyRole(['Admin', 'Super Admin'])) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can assign reviewers.
     */
    public function assignReviewer(User $user, Submission $submission): bool
    {
        return $user->hasAnyRole(['Editor', 'Admin', 'Super Admin']);
    }

    /**
     * Determine if the user can make editorial decisions.
     */
    public function makeDecision(User $user, Submission $submission): bool
    {
        return $user->hasAnyRole(['Editor', 'Admin', 'Super Admin']);
    }

    /**
     * Determine if the user can publish the submission.
     */
    public function publish(User $user, Submission $submission): bool
    {
        return $user->hasAnyRole(['Editor', 'Admin', 'Super Admin'])
            && $submission->status === Submission::STATUS_ACCEPTED;
    }

    /**
     * Determine if the user can access a specific workflow stage.
     */
    public function accessStage(User $user, Submission $submission, string $stage): bool
    {
        return $user->canAccessStage($stage, $submission->journal_id, $submission->id);
    }
}
