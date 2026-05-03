<?php

namespace App\Http\Middleware;

use App\Models\Journal;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectJournalContext
{
    /**
     * Handle an incoming request.
     *
     * This middleware detects the journal context from the route parameter
     * and binds it to the service container for global access.
     * Unlike JournalContextMiddleware, this does NOT abort if no journal is found,
     * allowing the request to continue for portal-level authentication.
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

        if ($journal) {
            // For disabled journals on public auth routes, show generic message
            // Authenticated staff can still access via main dashboard routes
            if (!$journal->enabled && !$request->user()) {
                abort(404, 'Journal not found.');
            }

            // Bind the journal to the service container for global access
            app()->instance('currentJournal', $journal);

            // Share with all views
            view()->share('currentJournal', $journal);

            // Store in session for redirect after login
            session()->put('login_journal_slug', $journal->slug);
        } else {
            // No journal context - portal level (or invalid slug if param was provided)
            
            // If we are strictly under a journal prefix but failed to resolve, we should probably 404
            // But this middleware is "Detect", implying optional? 
            // The original code aborted 404 if slug provided but not found.
            if ($journalParam && !$journal) {
                 abort(404, 'Journal not found.');
            }
            
            app()->instance('currentJournal', null);
            view()->share('currentJournal', null);
        }

        return $next($request);
    }
}
