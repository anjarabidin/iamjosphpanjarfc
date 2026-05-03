<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // =====================================================
        // IAMJOS Production Seeders
        // =====================================================

        $this->command->info('🚀 Starting IAMJOS Database Seeding...');
        $this->command->newLine();

        $this->call([
            // 1. Setup Roles & Permissions (Required First)
            RolesAndPermissionsSeeder::class,
            // RoleSeeder::class,

            // 3. Portal & Site Content (Public Pages)
            SiteContentSeeder::class,
            PortalSeeder::class,

            // 4. Email Templates
            EmailTemplateSeeder::class,

            // 5. Sample Journals & Initial Data
            JournalSeeder::class,
            InitialDataSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('✅ IAMJOS Database seeded successfully!');
        $this->command->newLine();
        $this->command->info('📋 Default Credentials:');
        $this->command->info('   Email: superadmin@iamjos.id');
        $this->command->info('   Password: password');
        $this->command->newLine();
        $this->command->warn('⚠️  Remember to change default password in production!');
        $this->command->newLine();
    }
}
