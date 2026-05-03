<?php

namespace Database\Seeders;

use App\Models\Journal;
use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Seed default email templates for all journals.
     */
    public function run(): void
    {
        $journals = Journal::all();

        foreach ($journals as $journal) {
            // Skip if journal already has templates
            if ($journal->emailTemplates()->exists()) {
                $this->command->info("Skipping {$journal->name} - templates already exist.");
                continue;
            }

            EmailTemplate::seedForJournal($journal->id);
            $this->command->info("Seeded email templates for: {$journal->name}");
        }
    }

    /**
     * Seed templates for a specific journal.
     * Can be called from JournalObserver when journal is created.
     */
    public static function seedForJournal(Journal $journal): void
    {
        EmailTemplate::seedForJournal($journal->id);
    }
}
