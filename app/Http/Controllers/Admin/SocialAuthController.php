<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Journal;
use App\Models\JournalUserRole;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialAuthController extends Controller
{
    /**
     * Redirect to Google OAuth.
     * 
     * @param string|null $journalSlug Optional journal context
     */
    public function redirectToGoogle(?string $journalSlug = null): RedirectResponse
    {
        // Store journal context in session for post-auth handling
        if ($journalSlug) {
            session(['oauth_journal_context' => $journalSlug]);
        } else {
            session()->forget('oauth_journal_context');
        }

        return Socialite::driver('google')
            ->redirect();
    }

    /**
     * Handle Google OAuth callback.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Unable to authenticate with Google. Please try again.');
        }

        // Check if user already exists
        $existingUser = User::where('email', $googleUser->getEmail())->first();

        if ($existingUser) {
            // Existing user - just log them in
            $this->updateLastLogin($existingUser);
            Auth::login($existingUser, true);

            return $this->redirectAfterLogin($existingUser);
        }

        // New user - create account and assign to journal
        $user = $this->createUserFromGoogle($googleUser);

        // Handle journal context assignment
        $this->assignJournalFromContext($user);

        // Fire registered event
        event(new Registered($user));

        // Log the user in
        Auth::login($user, true);

        return redirect()->route('journal.select')
            ->with('success', 'Welcome to IAMJOS! Your account has been created via Google.');
    }

    /**
     * Create a new user from Google OAuth data.
     */
    protected function createUserFromGoogle($googleUser): User
    {
        // Parse name into given/family name
        $nameParts = $this->parseFullName($googleUser->getName());

        // Generate a unique username from email
        $username = $this->generateUniqueUsername($googleUser->getEmail());

        $user = User::create([
            'name' => $googleUser->getName(),
            'given_name' => $nameParts['given_name'],
            'family_name' => $nameParts['family_name'],
            'email' => $googleUser->getEmail(),
            'username' => $username,
            'password' => Hash::make(Str::random(32)), // Random password (user can reset if needed)
            'avatar' => $googleUser->getAvatar(),
            'email_verified_at' => now(), // Google emails are verified
            'date_registered' => now(),
            'date_last_login' => now(),
            'privacy_consented_at' => now(), // Implicit consent via OAuth
        ]);

        // Assign default global roles
        $user->assignRole(['Reader', 'Author']);

        return $user;
    }

    /**
     * Assign user to journal based on context (URL or session).
     */
    protected function assignJournalFromContext(User $user): void
    {
        $journalSlug = session('oauth_journal_context');
        session()->forget('oauth_journal_context');

        if ($journalSlug) {
            // User came from a specific journal context
            $journal = Journal::where('slug', $journalSlug)
                ->where('enabled', true)
                ->first();

            if ($journal) {
                // Assign Author and Reader roles for this specific journal
                JournalUserRole::assignRoles($user, $journal, ['Reader', 'Author']);
                return;
            }
        }

        // No specific journal context - assign to default journal if configured
        $defaultJournal = $this->getDefaultJournal();

        if ($defaultJournal) {
            JournalUserRole::assignRoles($user, $defaultJournal, ['Reader', 'Author']);
        }

        // If no default journal, user remains unassigned to any journal
        // They can join journals later via their profile
    }

    /**
     * Get the default journal for new SSO users.
     * 
     * @return Journal|null
     */
    protected function getDefaultJournal(): ?Journal
    {
        // Option 1: Use application setting
        // $defaultJournalId = config('app.default_journal_id');
        // if ($defaultJournalId) {
        //     return Journal::find($defaultJournalId);
        // }

        // Option 2: Use the first enabled journal
        // return Journal::where('enabled', true)->orderBy('created_at')->first();

        // Option 3: Return null - user must manually join journals
        return null;
    }

    /**
     * Parse full name into given and family name.
     */
    protected function parseFullName(string $fullName): array
    {
        $parts = explode(' ', trim($fullName), 2);

        return [
            'given_name' => $parts[0] ?? '',
            'family_name' => $parts[1] ?? '',
        ];
    }

    /**
     * Generate a unique username from email.
     */
    protected function generateUniqueUsername(string $email): string
    {
        // Extract the part before @
        $base = Str::before($email, '@');
        
        // Remove non-alphanumeric characters
        $base = preg_replace('/[^a-zA-Z0-9_]/', '', $base);
        
        // Ensure minimum length
        if (strlen($base) < 3) {
            $base = $base . Str::random(5);
        }

        $username = $base;
        $counter = 1;

        // Keep appending numbers until we find a unique username
        while (User::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }

        return strtolower($username);
    }

    /**
     * Update last login timestamp.
     */
    protected function updateLastLogin(User $user): void
    {
        $user->update([
            'date_last_login' => now(),
        ]);
    }

    /**
     * Redirect user after successful login.
     */
    protected function redirectAfterLogin(User $user): RedirectResponse
    {
        // Check if there was a journal context
        $journalSlug = session('oauth_journal_context');
        session()->forget('oauth_journal_context');

        if ($journalSlug) {
            $journal = Journal::where('slug', $journalSlug)
                ->where('enabled', true)
                ->first();

            if ($journal) {
                // Check if user is already registered with this journal
                $userJournals = JournalUserRole::getUserJournals($user);
                
                if (!$userJournals->contains('id', $journal->id)) {
                    // Auto-assign to this journal
                    JournalUserRole::assignRoles($user, $journal, ['Reader', 'Author']);
                }

                // Redirect to this journal's dashboard
                return redirect()->route('journal.dashboard', ['journal' => $journalSlug]);
            }
        }

        // Default redirect
        if ($user->hasRole('Super Admin')) {
            return redirect()->route('admin.site.index');
        }

        return redirect()->route('journal.select');
    }
}
