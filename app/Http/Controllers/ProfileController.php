<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Intervention\Image\Laravel\Facades\Image;
use App\Models\Role;
use App\Models\Journal;

class ProfileController extends Controller
{
    /**
     * Display user profile edit form.
     * Journal parameter is injected via route model binding.
     */
    public function edit(Journal $journal): View
    {
        $user = Auth::user();

        // Fetch roles available for self-registration in current journal
        $availableRoles = Role::where('allow_registration', true)
            ->where('journal_id', $journal->id)
            ->get();

        // Get current user's role IDs for this journal using JournalUserRole
        $userRolesIds = $user->journalRoles()
            ->where('journal_id', $journal->id)
            ->pluck('role_id')
            ->toArray();

        // Fetch other enabled journals for enrollment section
        $otherJournals = Journal::where('id', '!=', $journal->id)
            ->where('enabled', true)
            ->with(['roles' => function($query) {
                $query->where('allow_registration', true);
            }])
            ->get();

        // Optimize enrollment check: Get all journal IDs where user has a role via JournalUserRole
        $enrolledJournalIds = $user->journalRoles()
            ->pluck('journal_id')
            ->unique()
            ->toArray();

        return view('profile.edit', compact('user', 'journal', 'availableRoles', 'userRolesIds', 'otherJournals', 'enrolledJournalIds'));

    }

    /**
     * Update user profile information.
     */
    public function update(Request $request, Journal $journal): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            // Identity
            'name' => ['required', 'string', 'max:255'],
            'given_name' => ['nullable', 'string', 'max:255'],
            'family_name' => ['nullable', 'string', 'max:255'],
            'affiliation' => ['nullable', 'string', 'max:500'],
            'country' => ['nullable', 'string', 'max:100'],
            
            // Contact
            // 'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]*$/'],
            'mailing_address' => ['nullable', 'string', 'max:1000'],
            
            // Public Profile
            'bio' => ['nullable', 'string', 'max:5000'],
            'homepage' => ['nullable', 'url', 'max:500'],
            'orcid_id' => ['nullable', 'string', 'max:50', 'regex:/^https?:\/\/orcid\.org\/\d{4}-\d{4}-\d{4}-\d{3}[0-9X]$/'],
        ], [
            'orcid_id.regex' => 'The ORCID iD must be a valid URL format (e.g., https://orcid.org/0000-0001-2345-6789)',
            'homepage.url' => 'The homepage must be a valid URL (e.g., https://example.com)',
            'phone.regex' => 'The phone number may only contain numbers, spaces, and the + - ( ) characters.',
        ]);

        // Sanitize Bio HTML (allow basic formatting tags only)
        if (isset($validated['bio'])) {
            $validated['bio'] = $this->sanitizeBio($validated['bio']);
        }

        $user->update($validated);

        return redirect()->route('journal.profile.edit', $journal->slug)->with('success', 'Profile updated successfully.');
    }

    /**
     * Sanitize biography HTML content
     * Allows only safe HTML tags for academic formatting
     */
    private function sanitizeBio(?string $bio): ?string
    {
        if (empty($bio)) {
            return null;
        }

        // Define allowed tags (OJS 3.3 compatible + img for TinyMCE)
        $allowedTags = '<p><br><strong><b><em><i><u><ul><ol><li><a><img>';
        
        // Strip all tags except allowed ones
        $cleaned = strip_tags($bio, $allowedTags);
        
        // Additional security: ensure links only have href attribute
        $cleaned = preg_replace_callback(
            '/<a\s+([^>]*?)>/i',
            function($matches) {
                if (preg_match('/href=["\']([^"\']*?)["\']/i', $matches[1], $href)) {
                    // Only allow http/https links
                    if (preg_match('/^https?:\/\//i', $href[1])) {
                        return '<a href="' . htmlspecialchars($href[1], ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener noreferrer">';
                    }
                }
                return '';
            },
            $cleaned
        );
        
        return $cleaned;
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request, Journal $journal): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('journal.profile.edit', $journal->slug)->with('success', 'Password updated successfully.');
    }

    /**
     * Update user avatar.
     */
    public function updateAvatar(Request $request, Journal $journal): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $user = Auth::user();

        // Delete old avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store and optimize new avatar
        $file = $request->file('avatar');
        $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = 'avatars/' . $filename;

        // Resize and optimize the image
        $image = Image::read($file);
        $image->cover(400, 400); // Resize to 400x400
        
        // Save to storage
        Storage::disk('public')->put($path, (string) $image->encode());

        $user->update(['avatar' => $path]);

        return redirect()->route('journal.profile.edit', $journal->slug)->with('success', 'Avatar updated successfully.');
    }

    /**
     * Delete user avatar.
     */
    /**
     * Handle image upload from TinyMCE
     */
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = 'journal_img_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('journal/images', $filename, 'public');
            
            return response()->json([
                'location' => asset('storage/' . $path)
            ]);
        }
        
        return response()->json(['error' => 'No file uploaded'], 400);
    }

    /**
     * Delete user avatar.
     */
    public function deleteAvatar(Journal $journal): RedirectResponse
    {
        $user = Auth::user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        return redirect()->route('journal.profile.edit', $journal->slug)->with('success', 'Avatar removed successfully.');
    }

    /**
     * Update user's self-registerable roles safely.
     * CRITICAL SAFETY: This method prevents accidental removal of administrative roles.
     * Algorithm:
     * 1. Keep all existing roles that are NOT allowed for self-registration (e.g., Admin, Editor)
     * 2. Merge with newly selected self-registerable roles
     * 3. Sync the combined list
     */
    public function updateRoles(Request $request, Journal $journal): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'selected_roles' => 'nullable|array',
            'selected_roles.*' => 'exists:roles,id',
        ]);

        // Step 1: Get user's current roles that are NOT allowed for self-registration
        // These are administrative roles (Editor, Journal Manager, etc.) that must be preserved
        // Use JournalUserRole to fetch these accurately
        $keptRoles = $user->journalRoles()
            ->where('journal_id', $journal->id)
            ->with(['role' => function($query) {
                $query->where('allow_registration', false);
            }])
            ->whereHas('role', function($query) {
                $query->where('allow_registration', false);
            })
            ->pluck('role_id')
            ->toArray();

        // Step 2: Get the selected self-registerable roles from the request
        $selectedRoles = $request->input('selected_roles', []);

        // Step 3: Validate that selected roles are actually self-registerable for this journal
        $validSelfRegisterableRoles = Role::where('journal_id', $journal->id)
            ->where('allow_registration', true)
            ->pluck('id')
            ->toArray();

        $validSelectedRoles = array_intersect($selectedRoles, $validSelfRegisterableRoles);

        // Step 4: Merge kept administrative roles with valid selected roles
        $finalRoles = array_unique(array_merge($keptRoles, $validSelectedRoles));

        // Step 5: Sync only this journal's roles using JournalUserRole logic
        // We delete all existing roles for this journal, then re-insert the final list
        
        // Remove all roles for this user in this journal first
        \App\Models\JournalUserRole::where('journal_id', $journal->id)
            ->where('user_id', $user->id)
            ->delete();

        // Re-assign the final roles
        foreach ($finalRoles as $roleId) {
            \App\Models\JournalUserRole::create([
                'journal_id' => $journal->id,
                'user_id' => $user->id,
                'role_id' => $roleId,
            ]);
        }

        return redirect()->route('journal.profile.edit', $journal->slug)->with('success', 'Your roles have been updated successfully.');
    }

    /**
     * Enroll user in a specific journal with selected roles.
     */
    public function enroll(Request $request, Journal $journal): RedirectResponse
    {
        $request->validate([
            'roles' => 'required|array|min:1',
            'roles.*' => 'string', // Role names like "Author", "Reader"
        ]);

        $user = Auth::user();

        // Get Role IDs for selected role names in THIS journal
        // Ensure they are allowed for self-registration
        $rolesToAssign = Role::where('journal_id', $journal->id)
            ->whereIn('name', $request->roles)
            ->where('allow_registration', true)
            ->pluck('id')
            ->toArray();

        if (empty($rolesToAssign)) {
            return back()->with('error', 'Invalid roles selected or roles not available for registration.');
        }

        // Attach roles without detaching existing ones (if any)
        // Use JournalUserRole helper or manual create
        foreach ($rolesToAssign as $roleId) {
            \App\Models\JournalUserRole::firstOrCreate([
                'journal_id' => $journal->id,
                'user_id' => $user->id,
                'role_id' => $roleId,
            ]);
        }

        return redirect()->back()->with('success', 'You have successfully joined ' . $journal->name);
    }
}
