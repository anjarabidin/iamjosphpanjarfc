<?php

namespace App\Services;

use App\Models\Journal;
use App\Models\Role;

class JournalSetupService
{
    public function seedDefaultRoles(Journal $journal)
    {
        $defaults = [
            [
                'name' => 'Journal Manager',
                'slug' => 'journal-manager',
                'permission_level' => 1, // Manager
                'permit_submission' => true,
                'permit_review' => true,
                'permit_copyediting' => true,
                'permit_production' => true,
                'is_system' => true, // Flag to prevent deletion if needed
            ],
            [
                'name' => 'Editor',
                'slug' => 'editor',
                'permission_level' => 1, // Manager
                'permit_submission' => true,
                'permit_review' => true,
                'permit_copyediting' => true,
                'permit_production' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Section Editor',
                'slug' => 'section-editor',
                'permission_level' => 2, // Section Editor
                'permit_submission' => true,
                'permit_review' => true,
                'permit_copyediting' => true,
                'permit_production' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Guest Editor',
                'slug' => 'guest-editor',
                'permission_level' => 2, // Section Editor
                'permit_submission' => true,
                'permit_review' => true,
                'permit_copyediting' => true,
                'permit_production' => true,
                'is_system' => true,
            ],
            [
                'name' => 'Copyeditor',
                'slug' => 'copyeditor',
                'permission_level' => 3, // Assistant
                'permit_submission' => false,
                'permit_review' => false,
                'permit_copyediting' => true, // Copyediting specific
                'permit_production' => false,
                'is_system' => true,
            ],
            [
                'name' => 'Designer / Layout Editor',
                'slug' => 'layout-editor',
                'permission_level' => 3, // Assistant
                'permit_submission' => false,
                'permit_review' => false,
                'permit_copyediting' => false,
                'permit_production' => true, // Production specific
                'is_system' => true,
            ],
            [
                'name' => 'Reviewer',
                'slug' => 'reviewer',
                'permission_level' => 3, // Reviewer
                'permit_submission' => false,
                'permit_review' => true, // Only review access
                'permit_copyediting' => false,
                'permit_production' => false,
                'is_system' => true,
            ],
            [
                'name' => 'Author',
                'slug' => 'author',
                'permission_level' => 4, // Author/Submitter
                'permit_submission' => true,
                'permit_review' => false,
                'permit_copyediting' => false,
                'permit_production' => false,
                'is_system' => true,
            ],
            [
                'name' => 'Reader',
                'slug' => 'reader',
                'permission_level' => 5, // Reader
                'permit_submission' => false,
                'permit_review' => false,
                'permit_copyediting' => false,
                'permit_production' => false,
                'is_system' => true,
            ],
        ];

        foreach ($defaults as $roleData) {
            // Ensure compatibility with Spatie Permissions
            $data = array_merge($roleData, [
                'journal_id' => $journal->id,
                'guard_name' => 'web'
            ]);

            Role::create($data);
        }
    }
}
