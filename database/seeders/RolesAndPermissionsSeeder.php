<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // =====================================================
        // CREATE PERMISSIONS
        // =====================================================

        // Submission Permissions
        $submissionPermissions = [
            'submissions.view-own',
            'submissions.create',
            'submissions.edit-own',
            'submissions.delete-own',
            'submissions.view-all',
            'submissions.edit-all',
            'submissions.delete-all',
            'submissions.assign-reviewer',
            'submissions.make-decision',
        ];

        // Review Permissions
        $reviewPermissions = [
            'reviews.view-own',
            'reviews.submit',
            'reviews.view-all',
        ];

        // Issue Permissions
        $issuePermissions = [
            'issues.view',
            'issues.create',
            'issues.edit',
            'issues.delete',
            'issues.publish',
        ];

        // User Management Permissions
        $userPermissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.assign-role',
        ];

        // Journal Settings Permissions
        $journalPermissions = [
            'journal.view-settings',
            'journal.edit-settings',
            'sections.manage',
        ];

        // Create all permissions
        $allPermissions = array_merge(
            $submissionPermissions,
            $reviewPermissions,
            $issuePermissions,
            $userPermissions,
            $journalPermissions
        );

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // =====================================================
        // CREATE ROLES WITH PERMISSIONS
        // =====================================================

        // 0. READER - Basic registered user
        $readerRole = Role::firstOrCreate(['name' => 'Reader', 'guard_name' => 'web']);
        // Reader has no specific restricted permissions by default, but role existence is required

        // 1. AUTHOR - Can submit and manage own articles
        $authorRole = Role::firstOrCreate(['name' => 'Author', 'guard_name' => 'web']);
        $authorRole->syncPermissions([
            'submissions.view-own',
            'submissions.create',
            'submissions.edit-own',
            'submissions.delete-own',
        ]);

        // 2. REVIEWER - Can review assigned articles
        $reviewerRole = Role::firstOrCreate(['name' => 'Reviewer', 'guard_name' => 'web']);
        $reviewerRole->syncPermissions([
            'reviews.view-own',
            'reviews.submit',
        ]);

        // 2b. COPYEDITOR (Optional OJS role)
        $copyeditorRole = Role::firstOrCreate(['name' => 'Copyeditor', 'guard_name' => 'web']);

        // 3. EDITOR - Can manage submissions and make decisions
        $editorRole = Role::firstOrCreate(['name' => 'Editor', 'guard_name' => 'web']);
        $editorRole->syncPermissions([
            'submissions.view-own',
            'submissions.create',
            'submissions.edit-own',
            'submissions.view-all',
            'submissions.edit-all',
            'submissions.assign-reviewer',
            'submissions.make-decision',
            'reviews.view-all',
            'issues.view',
            'issues.create',
            'issues.edit',
            'issues.publish',
        ]);

        // 3b. SECTION EDITOR
        $sectionEditorRole = Role::firstOrCreate(['name' => 'Section Editor', 'guard_name' => 'web']);
        $sectionEditorRole->syncPermissions([
            'submissions.view-own',
            'submissions.view-all', // Needs policy refinement for actual section scoping
            'submissions.edit-all',
            'submissions.assign-reviewer',
            'submissions.make-decision',
            'reviews.view-all',
        ]);

        // 3c. JOURNAL MANAGER
        $journalManagerRole = Role::firstOrCreate(['name' => 'Journal Manager', 'guard_name' => 'web']);
        $journalManagerRole->syncPermissions([
            'users.view',
            'users.create',
            'users.edit',
            'users.assign-role',
            'journal.view-settings',
            'journal.edit-settings',
            'sections.manage',
            'issues.view',
            'issues.create',
            'issues.edit',
            'issues.publish',
            // Can typically manage submissions too
            'submissions.view-all',
            'submissions.make-decision',
        ]);

        // 4. ADMIN - Can manage users, sections, and journal settings
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions([
            // All Editor permissions
            'submissions.view-own',
            'submissions.create',
            'submissions.edit-own',
            'submissions.view-all',
            'submissions.edit-all',
            'submissions.delete-all',
            'submissions.assign-reviewer',
            'submissions.make-decision',
            'reviews.view-all',
            'issues.view',
            'issues.create',
            'issues.edit',
            'issues.delete',
            'issues.publish',
            // Admin specific
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.assign-role',
            'journal.view-settings',
            'journal.edit-settings',
            'sections.manage',
        ]);

        // 5. SUPER ADMIN - Has all permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions(Permission::all());

        $this->command->info('Roles and Permissions seeded successfully!');
        $this->command->table(
            ['Role', 'Permissions Count'],
            [
                ['Author', $authorRole->permissions->count()],
                ['Reviewer', $reviewerRole->permissions->count()],
                ['Editor', $editorRole->permissions->count()],
                ['Admin', $adminRole->permissions->count()],
                ['Super Admin', $superAdminRole->permissions->count()],
            ]
        );
    }
}
