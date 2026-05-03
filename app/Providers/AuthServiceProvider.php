<?php

namespace App\Providers;

use App\Models\Issue;
use App\Models\Submission;
use App\Models\ReviewAssignment;
use App\Policies\IssuePolicy;
use App\Policies\SubmissionPolicy;
use App\Policies\ReviewAssignmentPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Submission::class => SubmissionPolicy::class,
        ReviewAssignment::class => ReviewAssignmentPolicy::class,
        Issue::class => IssuePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        /**
         * Global Auth Bypass (Gatekeeper)
         * Grants all permissions to the 'Super Admin' role globally.
         */
        Gate::before(function ($user) {
            return $user->hasRole('Super Admin') ? true : null;
        });
    }
}
