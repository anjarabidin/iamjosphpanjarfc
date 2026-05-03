<?php

namespace App\Services;

use App\Models\User;
use App\Models\JournalUserRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MergeUserService
{
    /**
     * Merge $fromUser into $toUser.
     * All records belonging to $fromUser will be reassigned to $toUser.
     * $fromUser will be deleted.
     *
     * @param User $fromUser The user to merge (will be deleted)
     * @param User $toUser The target user (will receive all records)
     * @return bool
     * @throws \Exception
     */
    public function merge(User $fromUser, User $toUser): bool
    {
        // Validation: Cannot merge user into themselves
        if ($fromUser->id === $toUser->id) {
            throw new \Exception("Cannot merge a user into themselves.");
        }

        // Validation: Prevent merging Super Admin (optional strict rule)
        if ($fromUser->hasRole('Super Admin')) {
            throw new \Exception("Cannot merge a Super Admin account. Please remove Super Admin role first.");
        }

        DB::beginTransaction();

        try {
            // 1. Transfer Submissions (Author)
            \App\Models\Submission::where('user_id', $fromUser->id)
                ->update(['user_id' => $toUser->id]);

            // 2. Transfer Co-Authors
            \App\Models\SubmissionAuthor::where('user_id', $fromUser->id)
                ->update(['user_id' => $toUser->id]);

            // 3. Transfer Review Assignments
            \App\Models\ReviewAssignment::where('reviewer_id', $fromUser->id)
                ->update(['reviewer_id' => $toUser->id]);

            // 4. Transfer Editorial Decisions
            \App\Models\EditorialDecision::where('editor_id', $fromUser->id)
                ->update(['editor_id' => $toUser->id]);

            // 5. Transfer Editorial Assignments
            \App\Models\EditorialAssignment::where('user_id', $fromUser->id)
                ->update(['user_id' => $toUser->id]);

            // 6. Transfer Discussion Participants
            \App\Models\DiscussionParticipant::where('user_id', $fromUser->id)
                ->update(['user_id' => $toUser->id]);

            // 7. Transfer Discussion Messages
            \App\Models\DiscussionMessage::where('sender_id', $fromUser->id)
                ->update(['sender_id' => $toUser->id]);

            // 8. Transfer File Uploads
            \App\Models\SubmissionFile::where('uploader_id', $fromUser->id)
                ->update(['uploader_id' => $toUser->id]);

            // 9. Transfer Access Logs (if exists)
            if (DB::getSchemaBuilder()->hasTable('access_logs')) {
                DB::table('access_logs')->where('user_id', $fromUser->id)
                    ->update(['user_id' => $toUser->id]);
            }

            // 10. Transfer Article Metrics (if exists)
            if (DB::getSchemaBuilder()->hasTable('article_metrics')) {
                DB::table('article_metrics')->where('user_id', $fromUser->id)
                    ->update(['user_id' => $toUser->id]);
            }

            // 11. Merge Journal Roles
            // Get all journal roles from old user
            $oldUserJournalRoles = JournalUserRole::where('user_id', $fromUser->id)->get();
            
            foreach ($oldUserJournalRoles as $journalRole) {
                // Check if target user already has this role in this journal
                $existingRole = JournalUserRole::where('user_id', $toUser->id)
                    ->where('journal_id', $journalRole->journal_id)
                    ->where('role_id', $journalRole->role_id)
                    ->first();

                // If target user doesn't have this role, assign it
                if (!$existingRole) {
                    JournalUserRole::create([
                        'user_id' => $toUser->id,
                        'journal_id' => $journalRole->journal_id,
                        'role_id' => $journalRole->role_id,
                    ]);
                }
            }

            // Delete old user's journal roles
            JournalUserRole::where('user_id', $fromUser->id)->delete();

            // 12. Merge Spatie Roles (Global)
            $oldRoles = $fromUser->roles;
            foreach ($oldRoles as $role) {
                if (!$toUser->hasRole($role->name)) {
                    $toUser->assignRole($role->name);
                }
            }

            // 13. Transfer Notifications
            \App\Models\Notification::where('user_id', $fromUser->id)
                ->update(['user_id' => $toUser->id]);

            // 14. Delete the Old User
            $fromUser->delete();

            DB::commit();
            
            Log::info("User Merge Successful", [
                'from_user_id' => $fromUser->id,
                'from_user_email' => $fromUser->email,
                'to_user_id' => $toUser->id,
                'to_user_email' => $toUser->email,
                'merged_at' => now(),
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("User Merge Failed", [
                'from_user_id' => $fromUser->id,
                'to_user_id' => $toUser->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Get potential target users for merging (exclude the source user and Super Admins)
     *
     * @param User $sourceUser
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPotentialTargets(User $sourceUser)
    {
        return User::where('id', '!=', $sourceUser->id)
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'Super Admin');
            })
            ->orderBy('name')
            ->get();
    }
}
