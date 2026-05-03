<?php

use App\Models\Journal;

if (!function_exists('current_journal')) {
    /**
     * Get the current active journal from context.
     *
     * @return \App\Models\Journal|null
     */
    function current_journal(): ?Journal
    {
        if (app()->bound('currentJournal')) {
            return app('currentJournal');
        }
        return null;
    }
}

if (!function_exists('journal_route')) {
    /**
     * Generate a URL for a named route with current journal context.
     *
     * @param string $name
     * @param array $parameters
     * @param bool $absolute
     * @return string
     */
    function journal_route(string $name, array $parameters = [], bool $absolute = true): string
    {
        $journal = current_journal();

        if ($journal) {
            $parameters = array_merge(['journal' => $journal->slug], $parameters);
        }

        return route($name, $parameters, $absolute);
    }
}

if (!function_exists('all_journals')) {
    /**
     * Get all enabled journals.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function all_journals()
    {
        return Journal::where('enabled', true)->orderBy('name')->get();
    }
}

if (!function_exists('user_journals')) {
    /**
     * Get journals accessible to the current user.
     * For now, returns all enabled journals.
     * Can be extended later for per-journal user roles.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function user_journals()
    {
        // In a more complex setup, this would filter based on user's journal assignments
        return all_journals();
    }
}
