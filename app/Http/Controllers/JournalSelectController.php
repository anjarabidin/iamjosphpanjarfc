<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\JournalUserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class JournalSelectController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();

        // Get journals the user is registered with
        $journals = JournalUserRole::getUserJournals($user);
        
        // Eager load submissions count to avoid N+1 query on selection page
        if ($journals->isNotEmpty()) {
            $journals->loadCount('submissions');
        }

        // If user has no journals, show all enabled journals with option to join
        if ($journals->isEmpty()) {
            $journals = Journal::where('enabled', true)
                ->withCount('submissions')
                ->orderBy('name')
                ->get();
                
            return view('journal-select', [
                'journals' => $journals,
                'userJournals' => collect([]),
                'showJoinOption' => true,
            ]);
        }

        // If only one journal exists, redirect base on role (OJS 3.3 style)
        if ($journals->count() === 1) {
            return $this->getRedirectForJournal($journals->first());
        }

        return view('journal-select', [
            'journals' => $journals,
            'userJournals' => $journals,
            'showJoinOption' => false,
        ]);
    }

    /**
     * Redirect to the correct page for a specific journal based on user role.
     */
    public function select(Journal $journal): RedirectResponse
    {
        // Verify user is actually enrolled in this journal (or is Super Admin)
        $user = auth()->user();
        $isEnrolled = JournalUserRole::where('user_id', $user->id)
            ->where('journal_id', $journal->id)
            ->exists();

        if (!$isEnrolled && !$user->hasRole('Super Admin')) {
            return redirect()->route('journal.select')->with('error', 'You are not enrolled in this journal.');
        }

        return $this->getRedirectForJournal($journal);
    }

    /**
     * Redirect from old /dashboard to journal select or first journal.
     */
    public function redirectToDashboard(): RedirectResponse
    {
        $user = auth()->user();

        // Get journals the user is registered with
        $journals = JournalUserRole::getUserJournals($user);

        if ($journals->isEmpty()) {
            // User has no journals, redirect to selection page
            return redirect()->route('journal.select');
        }

        // If only one journal, redirect (OJS 3.3 style)
        if ($journals->count() === 1) {
            return $this->getRedirectForJournal($journals->first());
        }

        // Otherwise show selection page
        return redirect()->route('journal.select');
    }

    /**
     * Get the redirect response for a journal based on user roles.
     * Matches OJS 3.3 behavior.
     */
    private function getRedirectForJournal(Journal $journal): RedirectResponse
    {
        $user = auth()->user();

        // Reviewers go to their review assignments (unless they have higher roles like Editor)
        $hasHigherRole = $user->hasAnyRole(['Journal Manager', 'Editor', 'Section Editor', 'Admin', 'Super Admin']);

        if ($user->hasRole('Reviewer') && !$hasHigherRole) {
            return redirect()->route('journal.reviewer.index', ['journal' => $journal->slug]);
        }

        // All other users (Authors, Editors, etc.) go to submissions list
        return redirect()->route('journal.submissions.index', ['journal' => $journal->slug]);
    }
}
