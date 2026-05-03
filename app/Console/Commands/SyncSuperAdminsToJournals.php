<?php

namespace App\Console\Commands;

use App\Models\Journal;
use App\Models\JournalUserRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class SyncSuperAdminsToJournals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'journals:sync-super-admins';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enroll all Super Admins in all enabled journals';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        
        if (!$superAdminRole) {
            $this->error('Super Admin role not found. Please run the seeder first.');
            return self::FAILURE;
        }

        $superAdmins = User::whereHas('roles', fn($q) => $q->where('name', 'Super Admin'))->get();
        $journals = Journal::where('enabled', true)->get();

        if ($superAdmins->isEmpty()) {
            $this->warn('No Super Admins found.');
            return self::SUCCESS;
        }

        if ($journals->isEmpty()) {
            $this->warn('No enabled journals found.');
            return self::SUCCESS;
        }

        $this->info("Found {$superAdmins->count()} Super Admin(s) and {$journals->count()} journal(s).");

        $created = 0;
        $existing = 0;

        foreach ($superAdmins as $admin) {
            foreach ($journals as $journal) {
                $record = JournalUserRole::firstOrCreate([
                    'journal_id' => $journal->id,
                    'user_id' => $admin->id,
                    'role_id' => $superAdminRole->id,
                ]);

                if ($record->wasRecentlyCreated) {
                    $created++;
                    $this->line("  ✓ Enrolled <info>{$admin->name}</info> in <comment>{$journal->name}</comment>");
                } else {
                    $existing++;
                }
            }
        }

        $this->newLine();
        $this->info("Done! Created {$created} new enrollments. {$existing} already existed.");

        return self::SUCCESS;
    }
}
