<?php

namespace Database\Seeders;

use App\Models\Journal;
use App\Models\Section;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JournalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $journals = [
            [
                'name' => 'Journal of Informatics and Technology',
                'abbreviation' => 'JIT',
                'slug' => 'jit',
                'description' => 'JIT is a peer-reviewed journal focusing on the latest research in computer science, information systems, software engineering, and emerging technologies. We publish original research articles, reviews, and case studies that contribute to the advancement of informatics.',
                'publisher' => 'Universitas Akademik Indonesia',
                'issn_print' => '2580-1234',
                'issn_online' => '2580-1235',
                'enabled' => true,
                'visible' => true,
                'settings' => [
                    'contact' => [
                        'email' => 'jit@example.com',
                        'phone' => '+62 21 1234567',
                    ],
                    'policies' => [
                        'peer_review' => 'double_blind',
                        'review_days' => 30,
                    ],
                ],
                'sections' => [
                    ['name' => 'Original Research', 'abbreviation' => 'OR', 'sort_order' => 1],
                    ['name' => 'Review Articles', 'abbreviation' => 'RA', 'sort_order' => 2],
                    ['name' => 'Short Communications', 'abbreviation' => 'SC', 'sort_order' => 3],
                    ['name' => 'Case Studies', 'abbreviation' => 'CS', 'sort_order' => 4],
                ],
            ],
            [
                'name' => 'Medical Science Journal',
                'abbreviation' => 'MSJ',
                'slug' => 'medika',
                'description' => 'Medical Science Journal publishes high-quality research in clinical medicine, public health, biomedical sciences, and healthcare management. Our mission is to disseminate medical knowledge that improves patient care and advances the field.',
                'publisher' => 'Fakultas Kedokteran Universitas Akademik',
                'issn_print' => '2581-5678',
                'issn_online' => '2581-5679',
                'enabled' => true,
                'visible' => true,
                'settings' => [
                    'contact' => [
                        'email' => 'medika@example.com',
                        'phone' => '+62 21 7654321',
                    ],
                    'policies' => [
                        'peer_review' => 'double_blind',
                        'review_days' => 45,
                    ],
                ],
                'sections' => [
                    ['name' => 'Clinical Research', 'abbreviation' => 'CR', 'sort_order' => 1],
                    ['name' => 'Public Health', 'abbreviation' => 'PH', 'sort_order' => 2],
                    ['name' => 'Medical Education', 'abbreviation' => 'ME', 'sort_order' => 3],
                    ['name' => 'Case Reports', 'abbreviation' => 'CP', 'sort_order' => 4],
                ],
            ],
            [
                'name' => 'Journal of Business and Economics',
                'abbreviation' => 'JBE',
                'slug' => 'jbe',
                'description' => 'JBE is dedicated to publishing innovative research in business administration, economics, finance, marketing, and organizational behavior. We welcome empirical studies, theoretical contributions, and policy analyses.',
                'publisher' => 'Fakultas Ekonomi dan Bisnis',
                'issn_print' => '2582-9012',
                'issn_online' => '2582-9013',
                'enabled' => true,
                'visible' => true,
                'settings' => [
                    'contact' => [
                        'email' => 'jbe@example.com',
                        'phone' => '+62 21 9876543',
                    ],
                    'policies' => [
                        'peer_review' => 'single_blind',
                        'review_days' => 30,
                    ],
                ],
                'sections' => [
                    ['name' => 'Business Strategy', 'abbreviation' => 'BS', 'sort_order' => 1],
                    ['name' => 'Financial Economics', 'abbreviation' => 'FE', 'sort_order' => 2],
                    ['name' => 'Marketing Management', 'abbreviation' => 'MM', 'sort_order' => 3],
                    ['name' => 'Human Resources', 'abbreviation' => 'HR', 'sort_order' => 4],
                ],
            ],
            [
                'name' => 'Engineering and Applied Sciences',
                'abbreviation' => 'EAS',
                'slug' => 'eas',
                'description' => 'EAS covers all aspects of engineering including civil, mechanical, electrical, chemical engineering, as well as applied sciences. We publish research that bridges theoretical knowledge with practical applications.',
                'publisher' => 'Fakultas Teknik Universitas Akademik',
                'issn_print' => '2583-3456',
                'issn_online' => '2583-3457',
                'enabled' => true,
                'visible' => true,
                'settings' => [
                    'contact' => [
                        'email' => 'eas@example.com',
                        'phone' => '+62 21 3456789',
                    ],
                    'policies' => [
                        'peer_review' => 'double_blind',
                        'review_days' => 35,
                    ],
                ],
                'sections' => [
                    ['name' => 'Civil Engineering', 'abbreviation' => 'CE', 'sort_order' => 1],
                    ['name' => 'Mechanical Engineering', 'abbreviation' => 'ME', 'sort_order' => 2],
                    ['name' => 'Electrical Engineering', 'abbreviation' => 'EE', 'sort_order' => 3],
                    ['name' => 'Environmental Engineering', 'abbreviation' => 'ENV', 'sort_order' => 4],
                ],
            ],
        ];

        foreach ($journals as $journalData) {
            $sections = $journalData['sections'];
            unset($journalData['sections']);

            // Create or update journal
            $journal = Journal::updateOrCreate(
                ['slug' => $journalData['slug']],
                array_merge($journalData, ['path' => $journalData['slug']])
            );

            $this->command->info("Created/Updated journal: {$journal->name}");

            // Create sections for this journal
            foreach ($sections as $sectionData) {
                Section::updateOrCreate(
                    [
                        'journal_id' => $journal->id,
                        'abbreviation' => $sectionData['abbreviation'],
                    ],
                    [
                        'name' => $sectionData['name'],
                        'sort_order' => $sectionData['sort_order'],
                        'is_active' => true,
                    ]
                );
            }

            $this->command->info("  - Created " . count($sections) . " sections");
        }

        $this->command->info("\n✅ Journal seeding completed! Created " . count($journals) . " journals.");
    }
}
