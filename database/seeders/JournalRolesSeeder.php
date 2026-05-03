<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Journal;
use App\Models\Role;

class JournalRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Create default OJS 3.3 roles for all existing journals.
     */
    public function run(): void
    {
        $journals = Journal::all();

        $defaultRoles = [
            [
                'name' => 'Journal Manager',
                'slug' => 'journal-manager',
                'permit_submission' => true,
                'permit_review' => true,
                'permit_copyediting' => true,
                'permit_production' => true,
                'allow_registration' => true,
                'show_contributor' => true,
                'allow_submission' => true,
                'description' => 'Manages the journal settings, users, and roles.'
            ],
            [
                'name' => 'Editor',
                'slug' => 'editor',
                'permit_submission' => true,
                'permit_review' => true,
                'permit_copyediting' => true,
                'permit_production' => true,
                'allow_registration' => true,
                'show_contributor' => true,
                'allow_submission' => true,
                'description' => 'Oversees the editorial process.'
            ],
            [
                'name' => 'Section Editor',
                'slug' => 'section-editor',
                'permit_submission' => true,
                'permit_review' => true,
                'permit_copyediting' => true,
                'permit_production' => true,
                'allow_registration' => true,
                'show_contributor' => true,
                'allow_submission' => false,
                'description' => 'Manages submissions in assigned sections.'
            ],
            [
                'name' => 'Guest Editor',
                'slug' => 'guest-editor',
                'permit_submission' => true,
                'permit_review' => true,
                'permit_copyediting' => true,
                'permit_production' => true,
                'allow_registration' => false,
                'show_contributor' => true,
                'allow_submission' => false,
                'description' => 'Guest editor for special issues.'
            ],
            [
                'name' => 'Reviewer',
                'slug' => 'reviewer',
                'permit_submission' => false,
                'permit_review' => true,
                'permit_copyediting' => false,
                'permit_production' => false,
                'allow_registration' => true,
                'show_contributor' => false,
                'allow_submission' => false,
                'description' => 'Reviews submissions.'
            ],
            [
                'name' => 'Author',
                'slug' => 'author',
                'permit_submission' => true,
                'permit_review' => false,
                'permit_copyediting' => false,
                'permit_production' => false,
                'allow_registration' => true,
                'show_contributor' => true,
                'allow_submission' => true,
                'description' => 'Submits manuscripts.'
            ],
            [
                'name' => 'Reader',
                'slug' => 'reader',
                'permit_submission' => false,
                'permit_review' => false,
                'permit_copyediting' => false,
                'permit_production' => false,
                'allow_registration' => true,
                'show_contributor' => false,
                'allow_submission' => false,
                'description' => 'Reads published content.'
            ],
            [
                'name' => 'Subscription Manager',
                'slug' => 'subscription-manager',
                'permit_submission' => false,
                'permit_review' => false,
                'permit_copyediting' => false,
                'permit_production' => false,
                'allow_registration' => false,
                'show_contributor' => false,
                'allow_submission' => false,
                'description' => 'Manages subscriptions and access.'
            ],
            [
                'name' => 'Copyeditor',
                'slug' => 'copyeditor',
                'permit_submission' => false,
                'permit_review' => false,
                'permit_copyediting' => true,
                'permit_production' => false,
                'allow_registration' => false,
                'show_contributor' => true,
                'allow_submission' => false,
                'description' => 'Edits submissions for grammar and style.'
            ],
            [
                'name' => 'Layout Editor',
                'slug' => 'layout-editor',
                'permit_submission' => false,
                'permit_review' => false,
                'permit_copyediting' => false,
                'permit_production' => true,
                'allow_registration' => false,
                'show_contributor' => true,
                'allow_submission' => false,
                'description' => 'Formats the articles (Galleys).'
            ],
            [
                'name' => 'Proofreader',
                'slug' => 'proofreader',
                'permit_submission' => false,
                'permit_review' => false,
                'permit_copyediting' => false,
                'permit_production' => true,
                'allow_registration' => false,
                'show_contributor' => true,
                'allow_submission' => false,
                'description' => 'Checks final version before publication.'
            ],
            [
                'name' => 'Production Editor',
                'slug' => 'production-editor',
                'permit_submission' => false,
                'permit_review' => false,
                'permit_copyediting' => false,
                'permit_production' => true,
                'allow_registration' => false,
                'show_contributor' => true,
                'allow_submission' => false,
                'description' => 'Oversees the production process.'
            ],
        ];

        foreach ($journals as $journal) {
            $this->command->info("Seeding roles for Journal: {$journal->name} ({$journal->slug})");

            foreach ($defaultRoles as $roleData) {
                Role::firstOrCreate(
                    [
                        'name' => $roleData['name'],
                        'journal_id' => $journal->id,
                        'guard_name' => 'web',
                    ],
                    [
                        'slug' => $roleData['slug'], // Assuming slug is unique per journal or handled
                        'is_system' => true,
                        // Permissions mapping
                        'permit_submission' => $roleData['permit_submission'],
                        'permit_review' => $roleData['permit_review'],
                        'permit_copyediting' => $roleData['permit_copyediting'],
                        'permit_production' => $roleData['permit_production'],
                        'allow_registration' => $roleData['allow_registration'],
                        'show_contributor' => $roleData['show_contributor'],
                        'allow_submission' => $roleData['allow_submission'],
                    ]
                );
            }
        }

        // Also ensure Super Admin exists globally (no journal_id or null?)
        // Assuming Super Admin is global and already handled, typically journal_id is null.
        // If we need to assign it to specific journals, User model logic handles that (Assign Role).
    }
}
