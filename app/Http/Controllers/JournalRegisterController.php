<?php

namespace App\Http\Controllers;

use App\Mail\GeneralNotificationMail;
use App\Models\Journal;
use App\Models\JournalUserRole;
use App\Models\Role;
use App\Models\User;
use App\Services\WaGateway;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class JournalRegisterController extends Controller
{
    /**
     * Display the registration view for a specific journal.
     */
    public function showRegistrationForm(Journal $journal): View
    {
        return view('journal.auth.register', compact('journal'));
    }

    /**
     * Handle an incoming registration request for a specific journal.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    /**
     * Handle an incoming registration request for a specific journal.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request, Journal $journal): RedirectResponse
    {
        // 1. Initial Validation
        $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required', 'string', Password::min(8)],
        ]);

        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        if ($user) {
            return $this->handleExistingUser($request, $journal, $user);
        }

        return $this->handleNewUser($request, $journal);
    }

    /**
     * Handle logic for an existing user (Smart Enroll).
     */
    private function handleExistingUser(Request $request, Journal $journal, User $user): RedirectResponse
    {
        // Verify password
        if (!Hash::check($request->input('password'), $user->password)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => [__('This email is already registered, but the password provided is incorrect. Please try logging in normally.')],
            ]);
        }

        // Password is correct. Log them in.
        Auth::login($user);

        // Enroll in this journal if not already
        if (!JournalUserRole::where('user_id', $user->id)->where('journal_id', $journal->id)->exists()) {
            $this->assignJournalRoles($user, $journal, $request->boolean('reviewer_interest'));
            $message = 'Account linked successfully! You are now registered with ' . $journal->name . '.';
        } else {
            $message = 'You are already registered with this journal. Welcome back!';
        }

        return redirect()->route('journal.dashboard', $journal->slug)
            ->with('success', $message);
    }

    /**
     * Handle logic for a new user.
     */
    private function handleNewUser(Request $request, Journal $journal): RedirectResponse
    {
        // Full Validation for new users
        $validated = $request->validate([
            'given_name' => ['required', 'string', 'max:255'],
            'family_name' => ['required', 'string', 'max:255'],
            'affiliation' => ['required', 'string', 'max:500'],
            'country' => ['required', 'string', 'max:5'],
            'phone' => ['required', 'string', 'max:20'],
            'username' => [
                'required', 'string', 'max:50', 'unique:users,username', 'regex:/^[a-zA-Z0-9_]+$/'
            ],
            'password' => ['required', 'confirmed', Password::min(8)],
            'privacy_consent' => ['required', 'accepted'],
            'email_notifications' => ['nullable', 'boolean'],
            'reviewer_interest' => ['nullable', 'boolean'],
        ], [
            'given_name.required' => 'First name is required.',
            'family_name.required' => 'Last name is required.',
            'affiliation.required' => 'Affiliation/Institution is required.',
            'country.required' => 'Please select your country.',
            'phone.required' => 'Phone number is required.',
            'username.regex' => 'Username may only contain letters, numbers, and underscores.',
            'username.unique' => 'This username is already taken.',
            'privacy_consent.required' => 'You must agree to the privacy policy.',
        ]);

        DB::beginTransaction();

        try {
            // Normalize phone number (Convert 08... to 628...)
            $phone = preg_replace('/\D/', '', $validated['phone']);
            if (str_starts_with($phone, '08')) {
                $phone = '62' . substr($phone, 1);
            }

            // Create User
            $fullName = trim($validated['given_name'] . ' ' . $validated['family_name']);
            
            $user = User::create([
                'name' => $fullName,
                'given_name' => $validated['given_name'],
                'family_name' => $validated['family_name'],
                'email' => $request->input('email'),
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'affiliation' => $validated['affiliation'],
                'country' => $validated['country'],
                'phone' => $phone,
                'email_notifications' => $request->boolean('email_notifications'),
                'privacy_consented_at' => now(),
                'date_registered' => now(),
            ]);

            // Assign Global System Roles
            // $user->assignRole(['Reader', 'Author']);

            // Assign Journal Specific Roles
            $this->assignJournalRoles($user, $journal, $request->boolean('reviewer_interest'));

            DB::commit();

            // Fire Registered Event
            event(new Registered($user));

            // Send Email Welcome Notification (queued to background)
            try {
                $emailBody = "Congratulations! Your account has been successfully created and you are now registered as an Author at {$journal->name}.\n\nYou can now log in and start submitting your manuscripts.\n\nThank you for joining us!";
                Mail::to($user->email)->queue(new GeneralNotificationMail(
                    emailSubject: 'Welcome to ' . $journal->name . ' – Registration Successful',
                    emailBody: $emailBody,
                    recipientName: $user->name,
                    journalName: $journal->name,
                ));
            } catch (Exception $e) {
                Log::error('Failed to queue welcome email: ' . $e->getMessage());
            }

            // Send WhatsApp Welcome
            try {
                WaGateway::sendTemplate($user, 'welcome', [
                    'name' => $user->name,
                ]);
            } catch (Exception $e) {
                Log::error('Failed to send WhatsApp welcome message: ' . $e->getMessage());
            }

            // Login
            Auth::login($user);

            return redirect()->route('journal.dashboard', $journal->slug)
                ->with('success', 'Welcome to ' . $journal->name . '! Your account has been created successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Journal Registration Failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['email' => 'Registration failed. Please try again.']); // Generic error
        }
    }

    /**
     * Assign journal roles to a user.
     *
     * Looks up the journal-specific Author role (name='Author', permission_level=5, journal_id=journal->id).
     * If it does not exist yet, creates it so a role_id is always available.
     * Then assigns the user to that role via JournalUserRole.
     */
    private function assignJournalRoles(User $user, Journal $journal, bool $reviewerInterest): void
    {
        // 1. Find the journal-specific Author role (permission_level 5)
        $authorRole = Role::where('name', 'Author')
            ->where('permission_level', Role::LEVEL_AUTHOR) // 5
            ->where('journal_id', $journal->id)
            ->first();

        // 2. If not found, create it for this journal
        if (!$authorRole) {
            $authorRole = Role::query()->create([
                'name'             => 'Author',
                'guard_name'       => 'web',
                'permission_level' => Role::LEVEL_AUTHOR,
                'journal_id'       => $journal->id,
                'allow_submission' => true,
                'allow_registration' => true,
            ]);
        }

        // 3. Assign the user to the Author role for this journal
        JournalUserRole::firstOrCreate([
            'journal_id' => $journal->id,
            'user_id'    => $user->id,
            'role_id'    => $authorRole->id,
        ]);

        // 4. Handle Reviewer Request
        if ($reviewerInterest) {
            // Find or create journal-specific Reviewer role (permission_level 4)
            $reviewerRole = Role::where('name', 'Reviewer')
                ->where('permission_level', Role::LEVEL_REVIEWER) // 4
                ->where('journal_id', $journal->id)
                ->first();

            if (!$reviewerRole) {
                $reviewerRole = Role::query()->create([
                    'name'             => 'Reviewer',
                    'guard_name'       => 'web',
                    'permission_level' => Role::LEVEL_REVIEWER,
                    'journal_id'       => $journal->id,
                    'permit_review'    => true,
                ]);
            }

            JournalUserRole::firstOrCreate([
                'journal_id' => $journal->id,
                'user_id'    => $user->id,
                'role_id'    => $reviewerRole->id,
            ]);

            // Also assign global Reviewer role if not already assigned
            if (!$user->hasRole('Reviewer')) {
                $user->assignRole('Reviewer');
            }
        }
    }
}
