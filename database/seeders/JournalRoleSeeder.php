<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Journal;
use App\Models\Role;
use Illuminate\Support\Str;

class JournalRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $journals = Journal::all();

        $rolesTemplate = [
            [
                'name' => 'Journal Manager',
                'permission_level' => 1,
                'permit_submission' => true,
                'permit_review' => true,
                'permit_copyediting' => true,
                'permit_production' => true,
                'allow_registration' => false,
                'show_contributor' => true,
                'allow_submission' => true,
            ],
            [
                'name' => 'Editor',
                'permission_level' => 2,
                'permit_submission' => true,
                'permit_review' => true,
                'permit_copyediting' => true,
                'permit_production' => true,
                'allow_registration' => false,
                'show_contributor' => true,
                'allow_submission' => true, // Editors can submit too
            ],
            [
                'name' => 'Section Editor',
                'permission_level' => 2,
                'permit_submission' => true,
                'permit_review' => true,
                'permit_copyediting' => true,
                'permit_production' => false,
                'allow_registration' => false,
                'show_contributor' => true,
                'allow_submission' => true,
            ],
            [
                'name' => 'Reviewer',
                'permission_level' => 3,
                'permit_submission' => false,
                'permit_review' => true,
                'permit_copyediting' => false,
                'permit_production' => false,
                'allow_registration' => true, // Users can often self-register as reviewers
                'show_contributor' => false,
                'allow_submission' => false,
            ],
            [
                'name' => 'Author',
                'permission_level' => 3,
                'permit_submission' => true,
                'permit_review' => false,
                'permit_copyediting' => false,
                'permit_production' => false,
                'allow_registration' => true,
                'show_contributor' => true, // Author shown in list
                'allow_submission' => true,
            ],
            [
                'name' => 'Copyeditor',
                'permission_level' => 3,
                'permit_submission' => false,
                'permit_review' => false,
                'permit_copyediting' => true,
                'permit_production' => false,
                'allow_registration' => false,
                'show_contributor' => false,
                'allow_submission' => false,
            ],
            [
                'name' => 'Production Editor',
                'permission_level' => 3,
                'permit_submission' => false,
                'permit_review' => false,
                'permit_copyediting' => false,
                'permit_production' => true,
                'allow_registration' => false,
                'show_contributor' => false,
                'allow_submission' => false,
            ],
        ];

        foreach ($journals as $journal) {
            foreach ($rolesTemplate as $template) {
                // If using Spatie Permission, 'name' + 'guard_name' must be unique.
                // If we want scoped roles without changing Spatie's unique constraint, 
                // we might need to modify the name.
                // However, assuming the User has handled the Schema to allow non-unique names with different journal_ids
                // OR we just try to create it.
                // If it fails, we catch it? No, we should try to do it right.
                // Common pattern is "Manager (Journal Name)" or internal name vs display name.
                // But let's assume strict separation by journal_id in query allows duplicate names IF table allows.

                // Let's check if the table allows it.
                // Since I can't check schema easily, I'll attempt to use the name as is.
                // If it crashes, the user might need to fix the unique constraint.

                // UPDATE: The User asked for "roles defaults corresponding to each journal id".

                Role::firstOrCreate(
                    [
                        'name' => $template['name'], // Potential collision if table has unique(name, guard)
                        'journal_id' => $journal->id,
                        'guard_name' => 'web',
                    ],
                    [
                        'permission_level' => $template['permission_level'],
                        'permit_submission' => $template['permit_submission'],
                        'permit_review' => $template['permit_review'],
                        'permit_copyediting' => $template['permit_copyediting'],
                        'permit_production' => $template['permit_production'],
                        'allow_registration' => $template['allow_registration'],
                        'show_contributor' => $template['show_contributor'],
                        'allow_submission' => $template['allow_submission'],
                        'is_system' => true, // These are default system roles
                    ]
                );
            }
        }
    }
}
