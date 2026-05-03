<?php

namespace App\Http\Middleware;

use App\Models\Journal;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JournalContextMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $journalParam = $request->route('journal');
        $journal = null;

        if ($journalParam instanceof Journal) {
            $journal = $journalParam;
        } elseif (is_string($journalParam)) {
            $journal = Journal::where('slug', $journalParam)->first();
        }

        if (!$journal) {
            abort(404, 'Journal not found.');
        }
        
        // Enforce enabled check (originally part of query)
        if (!$journal->enabled) {
             // Optional: allow admin access or specific bypass?
             // Original query: ->where('enabled', true)
             abort(404, 'Journal not found.');
        }

        // Global Cross-Journal Authorization for Authenticated Users
        $user = $request->user();
        if ($user && !$user->hasRole('Super Admin')) {
            $userJournals = $user->registeredJournals();
            if (!$userJournals->contains('id', $journal->id)) {
                // User does not have access to this journal context
                if ($userJournals->count() === 1) {
                    $targetJournal = $userJournals->first();
                    $route = $user->hasRoleInJournal('Reviewer', $targetJournal->id) 
                                && $user->rolesInJournal($targetJournal->id)->count() === 1 
                                ? 'journal.reviewer.index' 
                                : 'journal.submissions.index';
                    
                    return redirect()->route($route, ['journal' => $targetJournal->slug])
                                     ->with('info', "Access denied for {$journal->name}. You have been redirected to {$targetJournal->name}.");
                }
                
                return redirect()->route('journal.select')
                                 ->with('info', "Access denied. You do not have permissions in {$journal->name}. Please select a journal.");
            }
        }

        // Bind the journal to the service container for global access
        app()->instance('currentJournal', $journal);

        // Also share with all views
        view()->share('currentJournal', $journal);

        // Store in session for convenience
        session()->flash('current_journal_id', $journal->id);

        return $next($request);
    }
}
