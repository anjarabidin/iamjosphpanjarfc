<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JournalUserRole;
use App\Models\Role;
use App\Models\User;
use App\Services\MergeUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class JournalUserManagementController extends Controller
{
    /**
     * Helper to determine route prefix (admin vs general)
     */
    private function getRoutePrefix()
    {
        return request()->routeIs('journal.admin.*') ? 'journal.admin.users' : 'journal.users';
    }

    /**
     * Display all users for the specific journal context.
     * Shows users registered in this journal + all Super Admins.
     */
    public function index(Request $request)
    {
        $journal = current_journal();

        // Get user IDs that are registered in this journal
        $journalUserIds = JournalUserRole::where('journal_id', $journal->id)
            ->distinct()
            ->pluck('user_id')
            ->toArray();

        // Also include all Super Admins (they have access to all journals)
        $superAdminIds = User::whereHas('roles', function ($q) {
            $q->where('name', 'Super Admin')
                ->where('guard_name', 'web');
        })->pluck('id')->toArray();

        // Merge and get unique user IDs
        $allUserIds = array_unique(array_merge($journalUserIds, $superAdminIds));

        $query = User::whereIn('id', $allUserIds);

        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Filter by role in this journal
        if ($request->has('role') && $request->role != '') {
            if ($request->role === 'Super Admin') {
                // Filter to only Super Admins
                $query->role('Super Admin');
            } else {
                $roleId = Role::where('name', $request->role)->value('id');
                if ($roleId) {
                    $userIdsWithRole = JournalUserRole::where('journal_id', $journal->id)
                        ->where('role_id', $roleId)
                        ->pluck('user_id');
                    $query->whereIn('id', $userIdsWithRole);
                }
            }
        }

        $users = $query->paginate(10);

        // Get roles for filtering dropdown
        $roles = Role::where('journal_id', $journal->id)->pluck('name')->toArray() ?? [];

        // Load each user's roles in this journal (includes Super Admin check)
        $users->getCollection()->transform(function ($user) use ($journal) {
            $user->journal_roles = JournalUserRole::getUserRolesInJournal($user, $journal);
            return $user;
        });

        $routePrefix = $this->getRoutePrefix();
        return view('admin.journals.users.index', compact('journal', 'users', 'roles', 'routePrefix'));
    }

    /**
     * Display roles and permissions.
     */
    public function roles()
    {
        $journal = current_journal();
        // Show roles for current journal
        $roles = Role::where('journal_id', $journal->id)->get();

        $routePrefix = $this->getRoutePrefix();
        // Since roles are now scoped, we pass them as is.
        // Also ensure roles-table view handles them (it just iterates).
        return view('admin.journals.users.roles', compact('journal', 'roles', 'routePrefix'));
    }

    /**
     * Display site access configuration.
     */
    public function access()
    {
        $journal = current_journal();
        $routePrefix = $this->getRoutePrefix();
        return view('admin.journals.users.access', compact('journal', 'routePrefix'));
    }

    public function create()
    {
        $journal = current_journal();
        $routePrefix = $this->getRoutePrefix();

        // Get all assignable roles (exclude Super Admin for security)
        $roles = Role::where('journal_id', $journal->id)
            ->whereNotIn('name', ['Super Admin'])
            ->get();

        return view('admin.journals.users.create', compact('journal', 'routePrefix', 'roles'));
    }

    public function edit($journal, User $user)
    {
        $journal = current_journal();
        $routePrefix = $this->getRoutePrefix();

        // Get all assignable roles (exclude Super Admin for security)
        $roles = Role::where('journal_id', $journal->id)->get();

        // Get user's current roles in THIS journal (not global Spatie roles)
        $userRoleNames = JournalUserRole::getUserRolesInJournal($user, $journal)->pluck('id')->toArray();

        return view('admin.journals.users.edit', compact('journal', 'user', 'routePrefix', 'roles', 'userRoleNames'));
    }

    public function loginAs($journalSlug, User $user)
    {
        // Get the journal model from slug
        $journal = \App\Models\Journal::where('slug', $journalSlug)->first();
        if (!$journal) {
            abort(404, 'Journal not found.');
        }

        // Security check: Only super admin or admin should do this
        if (Auth::user()->id === $user->id) {
            return back()->with('error', 'You cannot login as yourself.');
        }

        // Store the original admin's ID before impersonating
        session()->put('impersonator_id', auth()->id());
        session()->put('impersonator_journal', $journalSlug);

        Auth::login($user);

        // Redirect based on user role
        if ($user->hasRole('Reviewer')) {
            // Priority 1: Journal context from current route
            if ($journal) {
                session()->forget(['intended_journal', 'login_journal_slug']);
                return redirect()->route('journal.reviewer.index', ['journal' => $journal->slug])
                    ->with('success', "You are now logged in as {$user->name}");
            }

            // Priority 2: Intended journal from query parameter or session
            $intendedJournal = session('intended_journal');
            if ($intendedJournal) {
                session()->forget('intended_journal');
                return redirect()->route('journal.reviewer.index', ['journal' => $intendedJournal])
                    ->with('success', "You are now logged in as {$user->name}");
            }

            // Priority 3: Journal stored in session from context middleware
            $loginJournalSlug = session('login_journal_slug');
            if ($loginJournalSlug) {
                session()->forget('login_journal_slug');
                return redirect()->route('journal.reviewer.index', ['journal' => $loginJournalSlug])
                    ->with('success', "You are now logged in as {$user->name}");
            }

            // Priority 4: If user has only one journal, go directly to its reviewer page
            $userJournals = $user->registeredJournals();
            if ($userJournals->count() === 1) {
                return redirect()->route('journal.reviewer.index', ['journal' => $userJournals->first()->slug])
                    ->with('success', "You are now logged in as {$user->name}");
            }

            // Default: Go to journal selection page
            return redirect()->route('journal.select')
                ->with('success', "You are now logged in as {$user->name}");
        } elseif ($user->hasAnyRole(['Editor', 'Section Editor', 'Journal Manager', 'Admin', 'Super Admin', 'Author'])) {
            // For editors, managers, admins, and authors: redirect to the submission list
            return redirect()->route('journal.submissions.index', ['journal' => $journal->slug])
                ->with('success', "You are now logged in as {$user->name}");
        }

        // Default fallback: Submission index using slug
        return redirect()->route('journal.submissions.index', ['journal' => $journal->slug])
            ->with('success', "You are now logged in as {$user->name}");
    }

    /**
     * Stop impersonating and return to admin account.
     */
    public function stopImpersonating()
    {
        // Check if currently impersonating
        if (!session()->has('impersonator_id')) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not currently impersonating anyone.');
        }

        // Retrieve admin ID and original journal
        $adminId = session()->pull('impersonator_id');
        $originalJournal = session()->pull('impersonator_journal');

        // Switch back to admin
        Auth::logout();
        Auth::loginUsingId($adminId);

        // Redirect back to user management page
        $journal = current_journal() ?? \App\Models\Journal::where('slug', $originalJournal)->first();

        if ($journal) {
            return redirect()->route('journal.users.index', ['journal' => $journal->slug])
                ->with('success', 'Welcome back to your account.');
        }

        return redirect()->route('dashboard')
            ->with('success', 'Welcome back to your account.');
    }

    public function disable($journal, User $user)
    {
        // Logic to disable user
        $user->update(['status' => 'inactive']); // Assuming status column exists or similar logic
        return back()->with('success', 'User disabled.');
    }

    public function enable($journal, User $user)
    {
        $user->update(['status' => 'active']);
        return back()->with('success', 'User enabled.');
    }

    public function email($journal, User $user)
    {
        // Mock email functionality layout
        return back()->with('success', 'Email compose window opened (mock).');
    }

    // Role Management Methods
    public function createRole()
    {
        $journal = current_journal();
        $routePrefix = $this->getRoutePrefix();
        return view('admin.journals.users.roles.create', compact('journal', 'routePrefix'));
    }

    public function storeRole(Request $request)
    {
        $journal = current_journal();

        $request->validate([
            'name' => [
                'required',
                'max:255',
                Rule::unique('roles')->where(function ($query) use ($journal) {
                    return $query->where('journal_id', $journal->id);
                }),
            ],
            'permission_level' => 'required|integer|min:1|max:6',
        ]);

        DB::beginTransaction();

        try {
            $role = Role::query()->create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'guard_name' => 'web',
                'journal_id' => $journal->id,
                'permission_level' => $request->permission_level,
                'permit_submission' => $request->boolean('permit_submission', false),
                'permit_review' => $request->boolean('permit_review', false),
                'permit_copyediting' => $request->boolean('permit_copyediting', false),
                'permit_production' => $request->boolean('permit_production', false),
                'allow_registration' => $request->boolean('allow_registration', false),
                'show_contributor' => $request->boolean('show_contributor', false),
                'allow_submission' => $request->boolean('allow_submission', false),
                'is_system' => $request->boolean('is_system', false),
            ]);

            DB::commit();

            return redirect()
                ->route($this->getRoutePrefix() . '.roles', ['journal' => $journal->slug])
                ->with('success', "Role '{$role->name}' created successfully.");
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('Failed to create role', [
                'journal_id' => $journal->id,
                'request' => $request->only(['name', 'permission_level', 'permit_submission', 'permit_review', 'permit_copyediting', 'permit_production', 'allow_registration', 'show_contributor', 'allow_submission']),
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to create role. Check logs for details.');
        }
    }

    public function editRole($journal, $role)
    {
        $role = Role::findOrFail($role);
        $journal = current_journal(); // Get actual Journal object

        $routePrefix = $this->getRoutePrefix();
        return view('admin.journals.users.roles.edit', compact('journal', 'role', 'routePrefix'));
    }

    public function updateRole(Request $request, $journal, $role)
    {
        $role = Role::findOrFail($role);

        $request->validate([
            'name' => [
                'required',
                'max:255',
                Rule::unique('roles')->where(function ($query) use ($role) {
                    return $query->where('journal_id', $role->journal_id);
                })->ignore($role->id),
            ],
            'permission_level' => 'required|integer|min:1|max:6',
            'stages' => 'nullable|array',
            'allow_registration' => 'nullable',
            'show_contributor' => 'nullable',
            'allow_submission' => 'nullable',
        ]);

        $stages = $request->input('stages', []);

        DB::beginTransaction();

        try {
            $role->query()->where('id', $role->id)->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'permission_level' => $request->permission_level,
                'permit_submission' => in_array('submission', $stages),
                'permit_review' => in_array('review', $stages),
                'permit_copyediting' => in_array('copyediting', $stages),
                'permit_production' => in_array('production', $stages),
                'allow_registration' => $request->boolean('allow_registration'),
                'show_contributor' => $request->boolean('show_contributor'),
                'allow_submission' => $request->boolean('allow_submission'),
                'is_system' => $request->boolean('is_system', $role->is_system),
            ]);

            DB::commit();

            return redirect()->route($this->getRoutePrefix() . '.roles', ['journal' => $journal])
                ->with('success', "Role updated successfully.");
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('Failed to update role', [
                'role_id' => $role->id,
                'journal' => $journal,
                'request' => $request->only(['name', 'permission_level', 'stages', 'allow_registration', 'show_contributor', 'allow_submission']),
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to update role. Check logs for details.');
        }
    }

    public function updateRolePermission(Request $request, $journal, Role $role)
    {
        $request->validate([
            'field' => 'required|in:permit_submission,permit_review,permit_copyediting,permit_production,allow_registration,show_contributor,allow_submission',
            'value' => 'required|boolean'
        ]);

        // Security: Protect Super Admin
        if ($role->name === 'Super Admin' || $role->name === 'Admin') {
            return response()->json(['success' => false, 'message' => 'Cannot modify critical system roles.'], 403);
        }

        $field = $request->input('field');
        $value = $request->boolean('value');

        // Force update the specific field
        $role->$field = $value;
        $role->save();

        return response()->json([
            'success' => true,
            'message' => 'Permission updated.',
            'role' => $role->name,
            'field' => $field,
            'new_value' => $value
        ]);
    }

    public function destroyRole($journal, $role)
    {
        $role = Role::findOrFail($role);
        // Prevent deleting critical system roles if needed, for now allow all
        $role->delete();
        return redirect()->route($this->getRoutePrefix() . '.roles', ['journal' => $journal])
            ->with('success', "Role deleted successfully.");
    }

    public function resetRolePermissions($journal, $role)
    {
        $role = Role::findOrFail($role);
        // Determine default permissions based on role name logic (simplified for demo)
        $role->syncPermissions([]); // Clear permissions
        return back()->with('success', 'Role permissions reset to default (Cleared).');
    }

    // User Management Store/Update
    public function store(Request $request, $journal)
    {
        $journalModel = current_journal();

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'name' => 'required|string|max:255', // Preferred Public Name
            'given_name' => 'required|string|max:255',
            'family_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'affiliation' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:2',
            'phone' => 'nullable|string|max:50',
            'mailing_address' => 'nullable|string',
            'orcid_id' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'password' => 'required|min:8|confirmed',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id', 
        ]);

        $userData = $request->only([
            'username',
            'name',
            'given_name',
            'family_name',
            'email',
            'affiliation',
            'country',
            'phone',
            'mailing_address',
            'orcid_id',
            'bio'
        ]);

        $userData['password'] = bcrypt($request->password);
        $userData['email_verified_at'] = now(); // Auto-verify when created by admin
        $userData['date_registered'] = now();

        $user = User::create($userData);

        // Assign roles to user for THIS journal using JournalUserRole
        JournalUserRole::assignRoles($user, $journalModel, $request->roles);

        // Also give them the Spatie roles for global permission checks
        $user->syncRoles($request->roles);

        return redirect()->route($this->getRoutePrefix() . '.index', ['journal' => $journalModel->slug])
            ->with('success', 'User created and enrolled in this journal successfully.');
    }

    public function update(Request $request, $journal, User $user)
    {
        $journalModel = current_journal();

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'name' => 'required|string|max:255', // Preferred Public Name
            'given_name' => 'required|string|max:255',
            'family_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'affiliation' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:2',
            'phone' => 'nullable|string|max:50',
            'mailing_address' => 'nullable|string',
            'orcid_id' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $userData = $request->only([
            'username',
            'name',
            'given_name',
            'family_name',
            'email',
            'affiliation',
            'country',
            'phone',
            'mailing_address',
            'orcid_id',
            'bio'
        ]);

        // Handle Password Update
        if ($request->filled('password')) {
            $userData['password'] = bcrypt($request->password);
        }

        $user->update($userData);

        // Remove all existing roles for this user in THIS journal
        JournalUserRole::where('journal_id', $journalModel->id)
            ->where('user_id', $user->id)
            ->delete();

        // Re-assign selected roles for THIS journal
        JournalUserRole::assignRoles($user, $journalModel, $request->roles);

        // Also sync Spatie roles (for permission checks) - merge all journal roles
        $allUserRoles = JournalUserRole::where('user_id', $user->id)
            ->with('role')
            ->get()
            ->pluck('role.name')
            ->unique()
            ->toArray();
        $user->syncRoles($allUserRoles);

        return redirect()->route($this->getRoutePrefix() . '.index', ['journal' => $journalModel->slug])
            ->with('success', 'User profile and roles updated successfully.');
    }

    /**
     * Remove a user from this journal (unenroll).
     * This does NOT delete the user account, just removes their roles in this journal.
     * Super Admins cannot be removed from journals.
     */
    public function destroy($journal, User $user)
    {
        $journalModel = current_journal();

        // Super Admins cannot be removed from journals
        if ($user->hasRole('Super Admin')) {
            return redirect()->route($this->getRoutePrefix() . '.index', ['journal' => $journalModel->slug])
                ->with('error', 'Super Admins cannot be removed from journals.');
        }

        // Remove all roles for this user in THIS journal
        $deleted = JournalUserRole::where('journal_id', $journalModel->id)
            ->where('user_id', $user->id)
            ->delete();

        if ($deleted) {
            // Re-sync Spatie roles based on remaining journal assignments
            $allUserRoles = JournalUserRole::where('user_id', $user->id)
                ->with('role')
                ->get()
                ->pluck('role.name')
                ->unique()
                ->toArray();

            // If user has no more roles, give them Reader as default
            if (empty($allUserRoles)) {
                $allUserRoles = ['Reader'];
            }
            $user->syncRoles($allUserRoles);

            return redirect()->route($this->getRoutePrefix() . '.index', ['journal' => $journalModel->slug])
                ->with('success', 'User removed from this journal successfully.');
        }

        return redirect()->route($this->getRoutePrefix() . '.index', ['journal' => $journalModel->slug])
            ->with('error', 'User was not found in this journal.');
    }

    /**
     * Show form to enroll an existing user to this journal.
     */
    public function enrollForm()
    {
        $journal = current_journal();
        $routePrefix = $this->getRoutePrefix();

        // Get users NOT already in this journal (exclude Super Admins - they're already in all journals)
        $existingUserIds = JournalUserRole::where('journal_id', $journal->id)
            ->distinct()
            ->pluck('user_id')
            ->toArray();

        // Also exclude Super Admins since they're automatically in all journals
        $superAdminIds = User::whereHas('roles', function ($q) {
            $q->where('name', Role::ROLE_SUPERADMIN)
                ->where('guard_name', 'web');
        })->pluck('id')->toArray();

        $excludeIds = array_unique(array_merge($existingUserIds, $superAdminIds));

        $availableUsers = User::whereNotIn('id', $excludeIds)
            ->orderBy('name')
            ->get();

        $roles = Role::where('journal_id', $journal->id)
            ->whereNotIn('name', ['Super Admin'])
            ->get();

        return view('admin.journals.users.enroll', compact('journal', 'routePrefix', 'availableUsers', 'roles'));
    }

    /**
     * Enroll an existing user to this journal.
     */
    public function enroll(Request $request, $journal)
    {
        $journalModel = current_journal();

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,name',
        ]);

        $user = User::findOrFail($request->user_id);

        // Assign roles to user for THIS journal
        JournalUserRole::assignRoles($user, $journalModel, $request->roles);

        // Also sync Spatie roles
        $allUserRoles = JournalUserRole::where('user_id', $user->id)
            ->with('role')
            ->get()
            ->pluck('role.name')
            ->unique()
            ->toArray();
        $user->syncRoles($allUserRoles);

        return redirect()->route($this->getRoutePrefix() . '.index', ['journal' => $journalModel->slug])
            ->with('success', "User '{$user->name}' enrolled in this journal successfully.");
    }

    // Access Management
    public function updateAccess(Request $request)
    {
        // Logic to update journal settings regarding access
        return back()->with('success', 'Access settings updated.');
    }

    /**
     * Display the notify users form.
     */
    public function notify()
    {
        $journal = current_journal();
        $routePrefix = $this->getRoutePrefix();

        // Get all roles for the checkbox grid
        $roles = Role::where('journal_id', $journal->id)->orderBy('permission_level', 'asc')->get();

        return view('admin.journals.users.notify', compact('journal', 'routePrefix', 'roles'));
    }

    /**
     * Send notification to selected roles.
     * Uses chunking and queued jobs for scalability.
     */
    public function sendNotification(Request $request, $journal)
    {
        $journalModel = current_journal();

        $request->validate([
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,name',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'send_copy' => 'nullable|boolean',
        ]);

        $selectedRoles = $request->input('roles');
        $subject = $request->input('subject');
        $body = $request->input('body');
        $sendCopy = $request->boolean('send_copy');

        // Get user IDs in this journal with selected roles
        $roleIds = Role::whereIn('name', $selectedRoles)->pluck('id')->toArray();

        $userIds = JournalUserRole::where('journal_id', $journalModel->id)
            ->whereIn('role_id', $roleIds)
            ->distinct()
            ->pluck('user_id')
            ->toArray();

            // Also include Super Admins if selected (they're in all journals)
        if (in_array(Role::ROLE_SUPERADMIN, $selectedRoles)) {

            $superAdminIds = User::whereHas('roles', function ($q) {
                $q->where('name', Role::ROLE_SUPERADMIN)
                ->where('guard_name', 'web');
            })->pluck('id')->toArray();

            $userIds = array_unique(array_merge($userIds, $superAdminIds));
        }

        $totalUsers = count($userIds);

        if ($totalUsers === 0) {
            return back()
                ->withInput()
                ->with('error', 'No users found with the selected roles in this journal.');
        }

        // Chunk users and dispatch jobs (memory efficient)
        User::whereIn('id', $userIds)->chunk(100, function ($users) use ($subject, $body, $journalModel) {
            foreach ($users as $user) {
                \App\Jobs\SendBroadcastNotificationJob::dispatch(
                    $user,
                    $subject,
                    $body,
                    $journalModel->name
                );
            }
        });

        // Send copy to self if requested
        if ($sendCopy) {
            $currentUser = auth()->user();
            \App\Jobs\SendBroadcastNotificationJob::dispatch(
                $currentUser,
                $subject,
                $body,
                $journalModel->name
            );
        }

        return redirect()
            ->route($this->getRoutePrefix() . '.index', ['journal' => $journalModel->slug])
            ->with('success', "Notification task queued for {$totalUsers} user(s).");
    }

    /**
     * Show merge user form
     */
    public function merge($journal, User $user)
    {
        $journal = current_journal();
        $routePrefix = $this->getRoutePrefix();

        // Prevent merging Super Admins
        if ($user->hasRole('Super Admin')) {
            return redirect()
                ->route($routePrefix . '.index', ['journal' => $journal->slug])
                ->with('error', 'Cannot merge a Super Admin account.');
        }

        $sourceUser = $user;

        // Get potential target users (exclude source user and Super Admins)
        $mergeService = new MergeUserService();
        $potentialTargets = $mergeService->getPotentialTargets($sourceUser);

        return view('admin.journals.users.merge', compact('journal', 'routePrefix', 'sourceUser', 'potentialTargets'));
    }

    /**
     * Execute user merge
     */
    public function executeMerge(Request $request, $journal, User $user)
    {
        $journal = current_journal();
        $routePrefix = $this->getRoutePrefix();

        $request->validate([
            'target_user_id' => 'required|exists:users,id|different:' . $user->id,
            'confirmation_text' => 'required|in:MERGE',
        ]);

        // Prevent merging Super Admins
        if ($user->hasRole('Super Admin')) {
            return redirect()
                ->route($routePrefix . '.index', ['journal' => $journal->slug])
                ->with('error', 'Cannot merge a Super Admin account.');
        }

        $sourceUser = $user;
        $targetUser = User::findOrFail($request->target_user_id);

        // Prevent merging into Super Admin
        if ($targetUser->hasRole('Super Admin')) {
            return back()
                ->withInput()
                ->with('error', 'Cannot merge into a Super Admin account.');
        }

        try {
            $mergeService = new MergeUserService();
            $mergeService->merge($sourceUser, $targetUser);

            return redirect()
                ->route($routePrefix . '.index', ['journal' => $journal->slug])
                ->with('success', "Successfully merged '{$sourceUser->name}' into '{$targetUser->name}'. All data has been transferred and the source account has been deleted.");
        } catch (\Exception $e) {
            Log::error('User merge failed', [
                'source_user_id' => $sourceUser->id,
                'target_user_id' => $targetUser->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to merge users: ' . $e->getMessage());
        }
    }
}
