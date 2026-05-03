<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Journal;
use App\Models\JournalUserRole;
use App\Models\Role;
use App\Services\WaGateway;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Fetch all enabled journals for multi-journal registration
        $journals = Journal::where('enabled', true)
            ->orderBy('name')
            ->get();

        return view('admins.auth.register', compact('journals'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate all OJS 3.3 required fields
        $validated = $request->validate([
            // Profile Information
            'given_name' => ['required', 'string', 'max:255'],
            'family_name' => ['required', 'string', 'max:255'],
            'affiliation' => ['required', 'string', 'max:500'],
            'country' => ['required', 'string', 'max:5'],
            'phone' => ['required', 'string', 'max:20'],

            // Account Credentials
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'username' => [
                'required',
                'string',
                'max:50',
                'unique:users,username',
                'regex:/^[a-zA-Z0-9_]+$/', // Alphanumeric + underscore only
            ],
            'password' => ['required', 'confirmed', Password::min(8)],

            // Consent
            'privacy_consent' => ['required', 'accepted'],

            // Optional
            'email_notifications' => ['nullable', 'boolean'],

            // Multi-Journal Registration (required - at least one journal)
            'journals' => ['required', 'array', 'min:1'],
            'journals.*' => ['required', 'uuid', 'exists:journals,id'],

            // Reviewer interest per journal (optional)
            'reviewer_for_journal' => ['nullable', 'array'],
            'reviewer_for_journal.*' => ['nullable', 'boolean'],
        ], [
            // Custom error messages
            'given_name.required' => 'First name is required.',
            'family_name.required' => 'Last name is required.',
            'affiliation.required' => 'Affiliation/Institution is required.',
            'country.required' => 'Please select your country.',
            'phone.required' => 'Phone number is required.',
            'username.regex' => 'Username may only contain letters, numbers, and underscores.',
            'username.unique' => 'This username is already taken.',
            'email.unique' => 'An account with this email already exists.',
            'privacy_consent.required' => 'You must agree to the privacy policy.',
            'privacy_consent.accepted' => 'You must agree to the privacy policy.',
            'journals.required' => 'Please select at least one journal to register with.',
            'journals.min' => 'Please select at least one journal to register with.',
        ]);

        // Combine given_name and family_name into name field
        $fullName = trim($validated['given_name'] . ' ' . $validated['family_name']);

        // Normalize phone number (Convert 08... to 628...)
        $phone = preg_replace('/[^0-9]/', '', $validated['phone']); // Remove non-numeric characters
        if (str_starts_with($phone, '08')) {
            $phone = '62' . substr($phone, 1);
        }

        // Create the user with all OJS fields
        $user = User::create([
            'name' => $fullName,
            'given_name' => $validated['given_name'],
            'family_name' => $validated['family_name'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'affiliation' => $validated['affiliation'],
            'country' => $validated['country'],
            'phone' => $phone,
            'email_notifications' => $request->boolean('email_notifications'),
            'privacy_consented_at' => now(),
            'date_registered' => now(),
        ]);

        // Assign global default roles (Reader and Author)
        // These are site-wide roles using Spatie Permission
        $user->assignRole(['Reader', 'Author']);

        // Get reviewer interest per journal
        $reviewerForJournal = $request->input('reviewer_for_journal', []);

        // Assign per-journal roles
        foreach ($validated['journals'] as $journalId) {
            // Assign default roles for this journal: Reader and Author
            JournalUserRole::assignRoles($user, $journalId, ['Reader', 'Author']);

            // Check if user wants to be a reviewer for this specific journal
            if (!empty($reviewerForJournal[$journalId])) {
                JournalUserRole::assignRole($user, $journalId, 'Reviewer');
                
                // Also assign global Reviewer role if not already assigned
                if (!$user->hasRole('Reviewer')) {
                    $user->assignRole('Reviewer');
                }
            }
        }

        // Fire the Registered event (for email verification, etc.)
        event(new Registered($user));

        // Send WhatsApp welcome notification
        WaGateway::sendTemplate($user, 'welcome', [
            'name' => $user->name,
        ]);

        // Log the user in
        Auth::login($user);

        // Redirect to journal selection or dashboard
        return redirect()->route('journal.select')
            ->with('success', 'Welcome to IAMJOS! Your account has been created successfully.');
    }
}
