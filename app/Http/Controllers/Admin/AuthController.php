<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\AuthRequest;
use App\Models\Admin;
use App\Models\Journal;
use App\Models\User;
use App\Models\SiteSetting;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Display the login page with context-aware branding.
     *
     * Portal Context: /login - Generic IAMJOS branding
     * Journal Context: /journal/{slug}/login - Journal-specific branding
     */
    public function index(Request $request)
    {
        // Get journal context from service container (set by DetectJournalContext middleware)
        $journal = app()->bound('currentJournal') ? app('currentJournal') : null;

        // Store intended journal for post-login redirect (backward compatibility)
        if ($request->has('intended_journal')) {
            session(['intended_journal' => $request->query('intended_journal')]);
        }

        // Prepare branding data based on context
        $branding = $this->getBrandingData($journal);

        // Get global site settings
        $siteSetting = SiteSetting::first();

        return view('admins.auth.login', compact('journal', 'branding', 'siteSetting'));
    }

    /**
     * Handle authentication with context-aware redirects.
     */
    public function authenticate(AuthRequest $request)
    {
        $credentials = $request->validated();

        // Get journal context
        $journal = app()->bound('currentJournal') ? app('currentJournal') : null;

        // Determine if input is email or username
        $loginInput = $credentials['email'];
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Find user by either email or username
        $user = User::where($fieldType, $loginInput)->first();

        // Guard Clause: User not found
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Email/Username atau password tidak sesuai.'],
            ]);
        }

        // Check if user account is disabled
        if ($user->disabled) {
            return back()->with('warning', 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.')
                ->withInput($request->only('email'));
        }

        // Validate reCAPTCHA only for journal context if enabled and global keys are present
        $siteSetting = \App\Models\SiteSetting::first();
        if ($journal && $journal->is_recaptcha_enabled && $siteSetting && $siteSetting->recaptcha_secret_key) {
            $request->validate([
                'g-recaptcha-response' => ['required', new \App\Rules\RecaptchaRule($siteSetting->recaptcha_secret_key)]
            ]);
        }

        // Prepare credentials for authentication
        $authCredentials = [
            $fieldType => $loginInput,
            'password' => $credentials['password']
        ];

        // Attempt authentication
        if (Auth::guard('web')->attempt($authCredentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            /** @var \App\Models\User $user */
            $user = Auth::guard('web')->user();

            // Update last login timestamp
            $user->update(['date_last_login' => now()]);

            // Determine redirect based on context
            return $this->handlePostLoginRedirect($user, $journal);
        }

        throw ValidationException::withMessages([
            'email' => ['Email/Username atau password tidak sesuai.'],
        ]);
    }

    /**
     * Handle logout with context-aware redirects.
     */
    public function logout(Request $request)
    {
        // 1. Capture current journal context before session invalidation
        $journalSlug = $request->route('journal');

        // 2. Check session as fallback
        if (!$journalSlug) {
            $journalSlug = session('login_journal_slug');
        }

        // 3. Fallback: Check Referer/Previous URL
        // If the user hit the global /logout route from a journal page, we want to send them back to that journal.
        if (!$journalSlug) {
            $previousUrl = url()->previous();
            $path = parse_url($previousUrl, PHP_URL_PATH);
            if ($path) {
                // Assuming journal applications are at /{slug}/...
                $segments = explode('/', trim($path, '/'));
                if (!empty($segments)) {
                    $potentialSlug = $segments[0];
                    // Verify if this slug is actually a journal to avoid redirecting to 'admin' or 'login' as a slug
                    // We only redirect if it's a valid journal
                    if (Journal::where('slug', $potentialSlug)->exists()) {
                        $journalSlug = $potentialSlug;
                    }
                }
            }
        }

        // Logout the user
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect based on context
        if ($journalSlug) {
            // Verify the journal still exists and is enabled
            $journal = Journal::where('slug', $journalSlug)->where('enabled', true)->first();
            if ($journal) {
                return redirect()->route('journal.public.home', ['journal' => $journalSlug])
                    ->with('success', 'Anda berhasil logout.');
            }
        }

        // Default: redirect to portal home
        return redirect()->route('portal.home')
            ->with('success', 'Anda berhasil logout.');
    }

    /**
     * Handle post-login redirect based on user role and context.
     */
    protected function handlePostLoginRedirect(User $user, ?Journal $journal)
    {
        // 1. Super Admin always goes to site admin dashboard
        if ($user->hasRole('Super Admin')) {
            session()->forget(['intended_journal', 'login_journal_slug']);
            return redirect()->route('admin.site.index');
        }

        $userJournals = $user->registeredJournals();
        
        // Helper to determine the target route and handle redirection message
        $redirectWithInfo = function ($targetJournal, $originalJournal = null, $intendedSlug = null) use ($user) {
            session()->forget(['intended_journal', 'login_journal_slug']);
            
            $route = $user->hasRoleInJournal('Reviewer', $targetJournal->id) 
                        && $user->rolesInJournal($targetJournal->id)->count() === 1 
                        ? 'journal.reviewer.index' 
                        : 'journal.submissions.index';
            
            $redirect = redirect()->route($route, ['journal' => $targetJournal->slug]);
            
            if ($originalJournal && $targetJournal->id !== $originalJournal->id) {
                $redirect->with('info', "You have been redirected to {$targetJournal->name} because you do not have access to {$originalJournal->name}.");
            } elseif ($intendedSlug && $targetJournal->slug !== $intendedSlug) {
                $redirect->with('info', "You have been redirected to {$targetJournal->name} because you do not have access to the requested journal.");
            }
            
            return $redirect;
        };

        // 2. Check intended journal from query parameter or session
        $intendedJournalSlug = session('intended_journal') ?? session('login_journal_slug');
        $intendedJournal = $intendedJournalSlug ? Journal::where('slug', $intendedJournalSlug)->first() : null;
        
        // Determine the requested journal context (either current route journal or intended journal)
        $requestedJournal = $journal ?? $intendedJournal;

        if ($requestedJournal) {
            // Skenario A & B: Check if user has context in requested journal
            $hasRoleInRequested = $userJournals->contains('id', $requestedJournal->id);

            if ($hasRoleInRequested) {
                // Skenario A (Punya Akses)
                return $redirectWithInfo($requestedJournal);
            } else {
                // Skenario B (Tidak Punya Akses di Requested Journal)
                if ($userJournals->count() === 1) {
                    // Redirect to their ONLY journal
                    return $redirectWithInfo($userJournals->first(), $requestedJournal);
                }
            }
        }

        // Priority 4: No specific context, or user has multiple journals but no access to the requested one
        if ($userJournals->count() === 1) {
            return $redirectWithInfo($userJournals->first(), null, $intendedJournalSlug);
        }

        // Skenario C: Global/Multi-Akses - Go to journal selection page
        session()->forget(['intended_journal', 'login_journal_slug']);
        
        $response = redirect()->route('journal.select');
        if ($requestedJournal) {
            $response->with('info', "Please select a journal to access. You do not have permissions in {$requestedJournal->name}.");
        }
        return $response;
    }

    /**
     * Get branding data based on context.
     */
    protected function getBrandingData(?Journal $journal): array
    {
        if ($journal) {
            return [
                'name' => $journal->name,
                'acronym' => $journal->abbreviation ?? $journal->name,
                'description' => $journal->description ?? $journal->summary ?? 'Academic Journal Publishing Platform',
                'logo_url' => $journal->logo_path ? asset('storage/' . $journal->logo_path) : null,
                'cover_url' => $journal->homepage_image_path ? asset('storage/' . $journal->homepage_image_path) : null,
                'headline' => $journal->name,
                'tagline' => $journal->description ?? 'Submit, Review, and Publish Academic Research',
            ];
        }

        // Default portal branding
        return [
            'name' => config('app.name', 'IAMJOS'),
            'acronym' => 'IAMJOS',
            'description' => 'Indonesian Academic Journal System',
            'logo_url' => null,
            'cover_url' => null,
            'headline' => 'Advance Your Academic Research',
            'tagline' => 'A modern platform for managing academic journal submissions, peer reviews, and publications with streamlined workflows.',
        ];
    }
}
