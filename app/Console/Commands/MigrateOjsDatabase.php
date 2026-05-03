<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MigrateOjsDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'iamjos:migrate-ojs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data directly from an external OJS PKP 3.x MySQL database into IAMJOS.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("===========================================");
        $this->info("    IAMJOS - OJS PKP Database Migrator     ");
        $this->info("===========================================");
        $this->line("Script ini akan menghubungkan server ini dengan database OJS lama (termasuk yang berbeda server).");
        $this->line("Pastikan server database OJS lama Anda mengizinkan Remote MySQL Connection.");
        $this->line("");

        // 1. Dapatkan Kredensial Database Lama
        $host = $this->ask('Masukkan Host/IP Database OJS Lama (contoh: 192.168.1.10 atau namadomain.com)', '127.0.0.1');
        $port = $this->ask('Masukkan Port Database OJS Lama', '3306');
        $database = $this->ask('Masukkan Nama Database OJS Lama');
        $username = $this->ask('Masukkan Username Database OJS Lama');
        $password = $this->secret('Masukkan Password Database OJS Lama (Boleh kosong jika tidak ada)');

        $this->info("Menguji koneksi ke database {$database} di {$host}...");

        // Set konfigurasi dinamis
        Config::set('database.connections.ojs_legacy', [
            'driver' => 'mysql',
            'host' => $host,
            'port' => $port,
            'database' => $database,
            'username' => $username,
            'password' => $password,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ]);

        try {
            DB::connection('ojs_legacy')->getPdo();
            $this->info('Koneksi Berhasil! ✅');
        } catch (\Exception $e) {
            $this->error("Koneksi gagal: " . $e->getMessage());
            return 1;
        }

        // Cek Versi OJS secara kasar (berdasarkan struktur tabel)
        $isOjs3 = Schema::connection('ojs_legacy')->hasTable('publications');
        if (!$isOjs3) {
            $this->warn("PERINGATAN: Database ini sepertinya dari OJS 2.x atau 3.0/3.1 (Tidak ada tabel 'publications'). Script standar ini dirancang optimal untuk OJS 3.2 / 3.3. Beberapa data mungkin perlu disesuaikan.");
        } else {
            $this->info("Terdeteksi struktur database OJS 3.2 / 3.3. Melanjutkan...");
        }

        // 2. Pilih Jurnal Sumber (OJS) dan Tujuan (IAMJOS)
        // Ambil daftar jurnal dari OJS lama
        try {
            $oldJournals = DB::connection('ojs_legacy')->table('journals')->get();
        } catch (\Exception $e) {
            $this->error("Tabel 'journals' tidak ditemukan di database OJS. Pastikan ini adalah database OJS yang benar.");
            return 1;
        }

        if ($oldJournals->isEmpty()) {
            $this->error("Tidak ada jurnal yang ditemukan di database OJS lama.");
            return 1;
        }

        $this->line("\nDaftar Jurnal di Database OJS Lama:");
        foreach ($oldJournals as $oj) {
            $this->line("ID: {$oj->journal_id} | Path: {$oj->path}");
        }

        $oldJournalId = $this->ask('Masukkan ID Jurnal Sumber (dari daftar di atas)');
        
        $newJournals = \App\Models\Journal::all();
        if ($newJournals->isEmpty()) {
            $this->error("Belum ada jurnal yang dibuat di IAMJOS. Silakan buat minimal 1 jurnal di Dashboard Admin IAMJOS terlebih dahulu.");
            return 1;
        }

        $this->line("\nDaftar Jurnal di IAMJOS:");
        foreach ($newJournals as $nj) {
            $this->line("ID: {$nj->id} | Nama: {$nj->name} | Singkatan: {$nj->abbreviation}");
        }
        $newJournalId = $this->ask('Masukkan ID Jurnal Tujuan (IAMJOS)');
        $targetJournal = \App\Models\Journal::find($newJournalId);

        if (!$targetJournal) {
            $this->error("Jurnal IAMJOS tidak ditemukan.");
            return 1;
        }

        if (!$this->confirm("Anda yakin ingin memigrasi data dari Jurnal OJS (ID: $oldJournalId) ke Jurnal IAMJOS (ID: $newJournalId)?", true)) {
            $this->info("Migrasi dibatalkan.");
            return 0;
        }

        // MULAI PROSES MIGRASI
        $this->info("Memulai Migrasi...");

        // --- A. MIGRASI USERS ---
        $this->line("1. Migrasi Akun Users...");
        $oldUsersCount = 0;
        $usersSkipped = 0;
        
        try {
            DB::connection('ojs_legacy')->table('users')->orderBy('user_id')->chunk(100, function ($users) use (&$oldUsersCount, &$usersSkipped) {
                foreach ($users as $user) {
                    $exists = \App\Models\User::where('email', $user->email)->orWhere('username', $user->username)->exists();
                    if (!$exists) {
                        \App\Models\User::create([
                            'name' => trim($user->first_name . ' ' . $user->last_name) ?: $user->username,
                            'given_name' => $user->first_name,
                            'family_name' => $user->last_name,
                            'email' => $user->email,
                            'username' => $user->username,
                            'password' => $user->password, // Tetap gunakan hash password lama
                            'created_at' => $user->date_registered ?? now(),
                            'updated_at' => $user->date_last_login ?? now(),
                        ]);
                        $oldUsersCount++;
                    } else {
                        $usersSkipped++;
                    }
                }
            });
            $this->info("Selesai: $oldUsersCount Users ditambahkan ($usersSkipped dilewati karena email/username sudah ada).");
        } catch (\Exception $e) {
            $this->error("Gagal memigrasi users: " . $e->getMessage());
        }

        // --- B. MIGRASI ISSUES ---
        $this->line("\n2. Migrasi Issues (Edisi)...");
        $issueCount = 0;
        // Mapping untuk issue id lama ke baru agar artikel masuk ke edisi yang benar
        $issueMap = []; 

        try {
            $issues = DB::connection('ojs_legacy')->table('issues')->where('journal_id', $oldJournalId)->get();
            foreach ($issues as $issue) {
                $newIssue = \App\Models\Issue::create([
                    'journal_id' => $newJournalId,
                    'volume' => $issue->volume,
                    'number' => $issue->number,
                    'year' => $issue->year,
                    'title' => 'Vol. ' . $issue->volume . ' No. ' . $issue->number . ' (' . $issue->year . ')',
                    'published_at' => $issue->date_published ?? now(),
                    'is_published' => $issue->published,
                ]);
                $issueMap[$issue->issue_id] = $newIssue->id;
                $issueCount++;
            }
            $this->info("Selesai: $issueCount Issues (Edisi) dimigrasi.");
        } catch (\Exception $e) {
            $this->error("Gagal memigrasi issues: " . $e->getMessage());
        }

        // --- C. MIGRASI SUBMISSIONS (Artikel) ---
        $this->line("\n3. Migrasi Artikel (Submissions)...");
        $subCount = 0;
        
        try {
            $submissions = clone DB::connection('ojs_legacy')->table('submissions')
                               ->where('context_id', $oldJournalId); // context_id = journal_id di ojs 3.x
                               
            $submissionsList = $submissions->get();

            // Default section id in IAMJOS
            $sectionId = $targetJournal->sections->first()->id ?? null;

            foreach ($submissionsList as $sub) {
                // Ambil data user di sistem baru
                $oldSubUser = DB::connection('ojs_legacy')->table('users')->where('user_id', $sub->submission_progress ?? null)->first(); // Not always accurate, just an example
                $newUserId = 1; // Default to admin

                // OJS 3.2+ menggunakan publications table untuk judul & abstrak
                $title = 'Tanpa Judul';
                $abstract = '';
                $issueId = null;

                if ($isOjs3) {
                    $publication = DB::connection('ojs_legacy')->table('publications')
                        ->where('submission_id', $sub->submission_id)
                        ->where('status', 3) // Published
                        ->orderByDesc('publication_id')
                        ->first();

                    if ($publication) {
                        $issueId = $publication->issue_id ?? null;
                        
                        // Cari Judul (OJS simpan di publication_settings biasanya)
                        $titleSetting = DB::connection('ojs_legacy')->table('publication_settings')
                            ->where('publication_id', $publication->publication_id)
                            ->where('setting_name', 'title')
                            ->first();
                        if ($titleSetting) $title = $titleSetting->setting_value;

                        // Cari Abstrak
                        $abstractSetting = DB::connection('ojs_legacy')->table('publication_settings')
                            ->where('publication_id', $publication->publication_id)
                            ->where('setting_name', 'abstract')
                            ->first();
                        if ($abstractSetting) $abstract = $abstractSetting->setting_value;
                    }
                }

                $mappedIssueId = $issueId && isset($issueMap[$issueId]) ? $issueMap[$issueId] : null;

                $newSub = \App\Models\Submission::create([
                    'journal_id' => $newJournalId,
                    'user_id' => $newUserId,
                    'issue_id' => $mappedIssueId,
                    'section_id' => $sectionId,
                    'title' => Str::limit($title, 250),
                    'abstract' => strip_tags($abstract),
                    'status' => \App\Models\Submission::STATUS_PUBLISHED,
                    'stage' => \App\Models\Submission::STAGE_PRODUCTION,
                    'submitted_at' => $sub->date_submitted ?? now(),
                    'published_at' => $sub->date_submitted ?? now(),
                    'seq_id' => $sub->submission_id, // Simpan ID lama untuk referensi
                ]);

                // --- D. MIGRASI AUTHORS ---
                if ($isOjs3 && isset($publication)) {
                    $authors = DB::connection('ojs_legacy')->table('authors')->where('publication_id', $publication->publication_id)->get();
                    foreach ($authors as $auth) {
                        // Ambil detail nama dari author_settings
                        $authSettings = DB::connection('ojs_legacy')->table('author_settings')->where('author_id', $auth->author_id)->get();
                        
                        $givenName = '';
                        $familyName = '';
                        $affiliation = '';

                        foreach ($authSettings as $as) {
                            if ($as->setting_name == 'givenName') $givenName = $as->setting_value;
                            if ($as->setting_name == 'familyName') $familyName = $as->setting_value;
                            if ($as->setting_name == 'affiliation') $affiliation = $as->setting_value;
                        }

                        $fullName = trim($givenName . ' ' . $familyName) ?: 'Unknown Author';

                        \App\Models\SubmissionAuthor::create([
                            'submission_id' => $newSub->id,
                            'name' => $fullName,
                            'given_name' => $givenName,
                            'family_name' => $familyName,
                            'email' => $auth->email ?? 'no-email@example.com',
                            'affiliation' => $affiliation,
                            'is_primary_contact' => $auth->primary_contact ?? false,
                            'is_corresponding' => $auth->primary_contact ?? false,
                            'sort_order' => $auth->seq ?? 0,
                        ]);
                    }
                }

                $subCount++;
            }
            $this->info("Selesai: $subCount Artikel (Submissions) dimigrasi.");
        } catch (\Exception $e) {
            $this->error("Gagal memigrasi submissions: " . $e->getMessage() . ' - Line: ' . $e->getLine());
        }

        $this->line("");
        $this->info("==================================================");
        $this->info("  MIGRASI DATABASE SELESAI! 🎉  ");
        $this->info("==================================================");
        $this->line("");
        $this->warn("TINDAKAN SELANJUTNYA UNTUK FILE PDF:");
        $this->line("1. Buka File Manager/CPanel server OJS lama Anda.");
        $this->line("2. Cari folder 'files_dir' (biasanya berada di luar folder public_html).");
        $this->line("3. Compress/Zip folder tersebut, lalu salin isinya ke folder IAMJOS Anda di:");
        $this->line("   storage/app/public/journals/ojs_files_legacy/...");
        $this->line("(Catatan: Lokasi path PDF mungkin memerlukan penyesuaian manual di tabel submission_files tergantung pada versi OJS lama Anda).");

        return 0;
    }
}
