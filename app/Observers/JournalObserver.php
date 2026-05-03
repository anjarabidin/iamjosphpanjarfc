<?php

namespace App\Observers;

use App\Models\Journal;
use App\Models\User;
use App\Models\JournalUserRole;

class JournalObserver
{
    /**
     * Handle the Journal "created" event.
     * Automatically enroll all Super Admins in the new journal.
     */
    public function created(Journal $journal): void
    {
        // Only enroll Super Admins if the journal is enabled
        if ($journal->enabled) {
            JournalUserRole::enrollAllSuperAdminsInJournal($journal);
        }
    }

    /**
     * Handle the Journal "updated" event.
     * If journal becomes enabled, enroll Super Admins.
     */
    public function updated(Journal $journal): void
    {
        // If journal was just enabled, enroll Super Admins
        if ($journal->wasChanged('enabled') && $journal->enabled) {
            JournalUserRole::enrollAllSuperAdminsInJournal($journal);
        }
    }
}
