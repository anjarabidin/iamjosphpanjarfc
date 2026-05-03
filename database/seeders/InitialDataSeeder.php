<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Journal;
use App\Models\Section;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // =====================================================
        // CREATE DEFAULT JOURNAL
        // =====================================================
        $journal = Journal::firstOrCreate(
            ['path' => 'iamjos'],
            [
                'name' => 'IAMJOS - Indonesian Academic Journal System',
                'slug' => 'iamjos',
                'abbreviation' => 'IAMJOS',
                'description' => 'An open-access academic journal platform built for the Indonesian research community.',
                'publisher' => 'IAMJOS Publishing',
                'enabled' => true,
                'visible' => true,
                'settings' => [
                    'contact' => [
                        'email' => 'editor@iamjos.id',
                        'phone' => '+62 21 1234567',
                    ],
                    'policies' => [
                        'open_access' => true,
                        'peer_review' => 'double-blind',
                    ],
                    'appearance' => [
                        'primary_color' => '#0ea5e9',
                        'font_family' => 'Inter',
                    ],
                ],
            ]
        );

        $this->command->info("Journal created: {$journal->name}");

        // =====================================================
        // CREATE DEFAULT SECTIONS
        // =====================================================
        $sections = [
            [
                'name' => 'Original Articles',
                'abbreviation' => 'OA',
                'policy' => 'Original research articles presenting new findings, methodologies, or significant contributions to the field.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Review Articles',
                'abbreviation' => 'RA',
                'policy' => 'Comprehensive reviews and meta-analyses of existing literature in a specific research area.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Case Studies',
                'abbreviation' => 'CS',
                'policy' => 'Detailed case reports and studies providing insights into specific scenarios or applications.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Short Communications',
                'abbreviation' => 'SC',
                'policy' => 'Brief reports on preliminary findings, technical notes, or urgent communications.',
                'sort_order' => 4,
            ],
        ];

        foreach ($sections as $sectionData) {
            Section::firstOrCreate(
                [
                    'journal_id' => $journal->id,
                    'name' => $sectionData['name'],
                ],
                array_merge($sectionData, [
                    'journal_id' => $journal->id,
                    'is_active' => true,
                ])
            );
        }

        $this->command->info("Sections created: " . count($sections));

        // =====================================================
        // CREATE DEFAULT USERS
        // =====================================================

        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@iamjos.id'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'affiliation' => 'IAMJOS System',
                'country' => 'Indonesia',
            ]
        );
        $superAdmin->syncRoles(['Super Admin']);

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@iamjos.id'],
            [
                'name' => 'Journal Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'affiliation' => 'IAMJOS Publishing',
                'country' => 'Indonesia',
            ]
        );
        $admin->syncRoles(['Admin']);

        // Editor
        $editor = User::firstOrCreate(
            ['email' => 'editor@iamjos.id'],
            [
                'name' => 'Chief Editor',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'affiliation' => 'Universitas Indonesia',
                'country' => 'Indonesia',
                'bio' => 'Professor of Computer Science with 15 years of experience in academic publishing.',
            ]
        );
        $editor->syncRoles(['Editor']);

        // Reviewer
        $reviewer = User::firstOrCreate(
            ['email' => 'reviewer@iamjos.id'],
            [
                'name' => 'Dr. Reviewer',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'affiliation' => 'Institut Teknologi Bandung',
                'country' => 'Indonesia',
                'bio' => 'Associate Professor specializing in peer review methodology.',
            ]
        );
        $reviewer->syncRoles(['Reviewer']);

        // Author
        $author = User::firstOrCreate(
            ['email' => 'author@iamjos.id'],
            [
                'name' => 'John Author',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'affiliation' => 'Universitas Gadjah Mada',
                'country' => 'Indonesia',
            ]
        );
        $author->syncRoles(['Author']);

        $this->command->info('Default users created:');
        $this->command->table(
            ['Email', 'Role', 'Password'],
            [
                ['superadmin@iamjos.id', 'Super Admin', 'password'],
                ['admin@iamjos.id', 'Admin', 'password'],
                ['editor@iamjos.id', 'Editor', 'password'],
                ['reviewer@iamjos.id', 'Reviewer', 'password'],
                ['author@iamjos.id', 'Author', 'password'],
            ]
        );
    }
}
