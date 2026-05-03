<?php

namespace App\Policies;

use App\Models\Issue;
use App\Models\User;

class IssuePolicy
{
    /**
     * Determine if the user can view any issues.
     */
    public function viewAny(User $user): bool
    {
        return true; // All can view published issues list
    }

    /**
     * Determine if the user can view the issue.
     */
    public function view(User $user, Issue $issue): bool
    {
        // Published issues are public
        if ($issue->is_published) {
            return true;
        }

        // Editor/Admin can view unpublished
        return $user->hasAnyRole(['Editor', 'Admin', 'Super Admin']);
    }

    /**
     * Determine if the user can create issues.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Editor', 'Admin', 'Super Admin']);
    }

    /**
     * Determine if the user can update the issue.
     */
    public function update(User $user, Issue $issue): bool
    {
        return $user->hasAnyRole(['Editor', 'Admin', 'Super Admin']);
    }

    /**
     * Determine if the user can delete the issue.
     */
    public function delete(User $user, Issue $issue): bool
    {
        return $user->hasAnyRole(['Admin', 'Super Admin'])
            && !$issue->submissions()->exists();
    }

    /**
     * Determine if the user can publish the issue.
     */
    public function publish(User $user, Issue $issue): bool
    {
        return $user->hasAnyRole(['Editor', 'Admin', 'Super Admin']);
    }
}
