<?php

namespace App\Providers;

use App\Models\Issue;
use App\Models\Submission;
use App\Models\ReviewAssignment;
use App\Policies\IssuePolicy;
use App\Policies\SubmissionPolicy;
use App\Policies\ReviewAssignmentPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\View\Composers\PublicLayoutComposer;
use App\View\Composers\SiteLayoutComposer;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.tailwind');
        // Paginator::defaultSimpleView('vendor.pagination.simple-tailwind');

        \Carbon\Carbon::setLocale('id');

        /**
         * Directive @journalRole
         * Cara pakai: @journalRole('Editor|Manager', $journal->id) ... @endJournalRole
         */
        Blade::if('journalRole', function ($roles, $journalParam = null) {
            $user = Auth::user();
            
            if (!$user) {
                return false;
            }

            // Jika parameter tidak dikirim, ambil dari route
            if (is_null($journalParam)) {
                $journalParam = request()->route('journal');
            }

            // Resolve ID
            $journalId = null;
            if ($journalParam instanceof \App\Models\Journal) {
                $journalId = $journalParam->id;
            } elseif (is_string($journalParam)) {
                // Cek apakah ini ID/UUID atau Slug
                // Supaya aman dan cepat, kita query jika bukan UUID (asumsi slug)
                // Atau query saja by slug/id
                $journal = \App\Models\Journal::where('slug', $journalParam)
                            ->orWhere('id', $journalParam)
                            ->first(['id']);
                $journalId = $journal ? $journal->id : null;
            } elseif (is_numeric($journalParam)) {
                 $journalId = $journalParam;
            }

            if (!$journalId) {
                return false; 
            }

            return $user->hasJournalRole($roles, $journalId);
        });

        /**
         * Directive @journalPermission
         * Usage: @journalPermission([1, 2], $journal->id) ... @endJournalPermission
         * Or: @journalPermission([Role::LEVEL_MANAGER, Role::LEVEL_SECTION_EDITOR], $journal->id)
         */
        Blade::if('journalPermission', function ($levels, $journalParam = null) {
            $user = Auth::user();

            if (!$user) {
                return false;
            }

             // Jika parameter tidak dikirim, ambil dari route
            if (is_null($journalParam)) {
                $journalParam = request()->route('journal');
            }

            // Resolve ID
            $journalId = null;
            if ($journalParam instanceof \App\Models\Journal) {
                $journalId = $journalParam->id;
            } elseif (is_string($journalParam)) {
                 $journal = \App\Models\Journal::where('slug', $journalParam)
                            ->orWhere('id', $journalParam)
                            ->first(['id']);
                $journalId = $journal ? $journal->id : null;
            } elseif (is_numeric($journalParam)) {
                 $journalId = $journalParam;
            }

            if (!$journalId) {
                return false;
            }

            return $user->hasJournalPermission($levels, $journalId);
        });

        // Register View Composer for Public Layout (both regular and component versions)
        View::composer([
            'layouts.public',
            'components.layouts.public',
        ], PublicLayoutComposer::class);

        // Also register PublicSidebarComposer to the layouts because they check $sidebarBlocks->isNotEmpty()
        View::composer([
            'layouts.public',
            'components.layouts.public',
        ], \App\View\Composers\PublicSidebarComposer::class);

        // Register View Composer for Public Sidebar
        View::composer([
            'components.public.sidebar',
        ], \App\View\Composers\PublicSidebarComposer::class);

        // Register View Composer for Portal/Site Layout
        View::composer([
            'layouts.portal',
            'site.*',
            'components.site.navbar',
            'components.site.footer',
        ], SiteLayoutComposer::class);
    }
}
