<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Guard;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    /**
     * Menggunakan UUID untuk primary key.
     */
    use HasUuids;

    const ROLE_SUPERADMIN = 'Super Admin';
    const ROLE_ADMIN = 'Admin';
    const ROLE_PUBLISHER = 'Publisher';
    const ROLE_AUTHOR = 'Author';
    const ROLE_ASSESSOR = 'Assessor';

    /**
     * Pengaturan Primary Key non-incrementing.
     */
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Konstanta Level untuk Hierarki Akses.
     */
    const LEVEL_SUPER_ADMIN = 0;
    const LEVEL_ADMIN = 1;
    const LEVEL_MANAGER = 1;
    const LEVEL_EDITOR = 2;
    const LEVEL_SECTION_EDITOR = 2;
    const LEVEL_ASSISTANT = 3;
    const LEVEL_REVIEWER = 4;
    const LEVEL_AUTHOR = 5;
    const LEVEL_READER = 6;

    protected $fillable = [
        'name',
        'guard_name',
        'permission_level',
        'permit_submission',
        'permit_review',
        'permit_copyediting',
        'permit_production',
        'allow_registration',
        'show_contributor',
        'allow_submission',
        'journal_id',
        'slug',
        'is_system'
    ];

    protected $casts = [
        'permit_submission' => 'boolean',
        'permit_review' => 'boolean',
        'permit_copyediting' => 'boolean',
        'permit_production' => 'boolean',
        'allow_registration' => 'boolean',
        'show_contributor' => 'boolean',
        'allow_submission' => 'boolean',
        'is_system' => 'boolean',
    ];

    /**
     * Method Booted: Menambahkan Global Scope agar Role selalu difilter per jurnal.
     */
    protected static function booted()
    {
        static::addGlobalScope('journal', function (Builder $builder) {
            $journalId = \current_journal()?->id;
            if ($journalId) {
                // Mengunci query hanya pada journal_id yang sedang aktif.
                $builder->where($builder->getQuery()->from . '.journal_id', $journalId);
            }
        });
    }

    /**
     * OVERRIDE: Create Role dengan validasi journal_id.
     * Mencegah Spatie menolak nama yang sama di jurnal berbeda.
     */
    public static function create(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? Guard::getDefaultName(static::class);
        $attributes['journal_id'] = $attributes['journal_id'] ?? \current_journal()?->id;

        // Pengecekan unik berdasarkan nama + guard + journal_id.
        $exists = static::withoutGlobalScope('journal')
            ->where('name', $attributes['name'])
            ->where('guard_name', $attributes['guard_name'])
            ->where('journal_id', $attributes['journal_id'])
            ->first();

        if ($exists) {
            throw RoleAlreadyExists::create($attributes['name'], $attributes['guard_name']);
        }

        return static::query()->create($attributes);
    }

    /**
     * OVERRIDE: Mencari role berdasarkan nama dengan filter journal_id.
     */
    public static function findByName(string $name, ?string $guardName = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $journalId = \current_journal()?->id;

        $role = static::where('name', $name)
            ->where('guard_name', $guardName)
            ->where('journal_id', $journalId)
            ->first();

        if (!$role) {
            throw RoleDoesNotExist::named($name, $guardName);
        }

        return $role;
    }

    /**
     * OVERRIDE: Digunakan Spatie untuk sinkronisasi internal.
     */
    protected static function findByParam(array $params = []): ?RoleContract
    {
        $journalId = \current_journal()?->id;
        if ($journalId && !isset($params['journal_id'])) {
            $params['journal_id'] = $journalId;
        }

        return static::query()->where($params)->first();
    }

    /**
     * Seeder Otomatis untuk Jurnal Baru.
     */
    public static function seedDefaultRolesForJournal(Journal $journal): void
    {
        $defaultRoles = [
            // --- Level 1: Management ---
            ['name' => 'Journal manager', 'level' => self::LEVEL_MANAGER, 'permit_submission' => true, 'permit_review' => true, 'permit_copyediting' => true, 'permit_production' => true, 'allow_registration' => false, 'show_contributor' => true, 'allow_submission' => true],
            ['name' => 'Journal editor', 'level' => self::LEVEL_MANAGER, 'permit_submission' => true, 'permit_review' => true, 'permit_copyediting' => true, 'permit_production' => true, 'allow_registration' => false, 'show_contributor' => true, 'allow_submission' => true],
            ['name' => 'Production editor', 'level' => self::LEVEL_MANAGER, 'permit_submission' => true, 'permit_review' => true, 'permit_copyediting' => true, 'permit_production' => true, 'allow_registration' => false, 'show_contributor' => true, 'allow_submission' => true],

            // --- Level 2: Editorial Decisions ---
            ['name' => 'Section editor', 'level' => self::LEVEL_SECTION_EDITOR, 'permit_submission' => true, 'permit_review' => true, 'permit_copyediting' => true, 'permit_production' => true, 'allow_registration' => false, 'show_contributor' => true, 'allow_submission' => true],
            ['name' => 'Guest editor', 'level' => self::LEVEL_SECTION_EDITOR, 'permit_submission' => true, 'permit_review' => true, 'permit_copyediting' => true, 'permit_production' => true, 'allow_registration' => false, 'show_contributor' => true, 'allow_submission' => true],

            // --- Level 3: Assistants (Specific Workflow Stages) ---
            ['name' => 'Copyeditor', 'level' => self::LEVEL_ASSISTANT, 'permit_submission' => false, 'permit_review' => false, 'permit_copyediting' => true, 'permit_production' => false, 'allow_registration' => false, 'show_contributor' => false, 'allow_submission' => false],
            ['name' => 'Designer', 'level' => self::LEVEL_ASSISTANT, 'permit_submission' => false, 'permit_review' => false, 'permit_copyediting' => false, 'permit_production' => true, 'allow_registration' => false, 'show_contributor' => false, 'allow_submission' => false],
            ['name' => 'Funding coordinator', 'level' => self::LEVEL_ASSISTANT, 'permit_submission' => true, 'permit_review' => true, 'permit_copyediting' => false, 'permit_production' => false, 'allow_registration' => false, 'show_contributor' => false, 'allow_submission' => false],
            ['name' => 'Indexer', 'level' => self::LEVEL_ASSISTANT, 'permit_submission' => false, 'permit_review' => false, 'permit_copyediting' => false, 'permit_production' => true, 'allow_registration' => false, 'show_contributor' => false, 'allow_submission' => false],
            ['name' => 'Layout Editor', 'level' => self::LEVEL_ASSISTANT, 'permit_submission' => false, 'permit_review' => false, 'permit_copyediting' => false, 'permit_production' => true, 'allow_registration' => false, 'show_contributor' => false, 'allow_submission' => false],
            ['name' => 'Marketing and sales coordinator', 'level' => self::LEVEL_ASSISTANT, 'permit_submission' => false, 'permit_review' => false, 'permit_copyediting' => true, 'permit_production' => false, 'allow_registration' => false, 'show_contributor' => false, 'allow_submission' => false],
            ['name' => 'Proofreader', 'level' => self::LEVEL_ASSISTANT, 'permit_submission' => false, 'permit_review' => false, 'permit_copyediting' => false, 'permit_production' => true, 'allow_registration' => false, 'show_contributor' => false, 'allow_submission' => false],

            // --- Level 4 & 5: Authors & Reviewers ---
            ['name' => 'Author', 'level' => self::LEVEL_AUTHOR, 'permit_submission' => true, 'permit_review' => true, 'permit_copyediting' => true, 'permit_production' => true, 'allow_registration' => true, 'show_contributor' => true, 'allow_submission' => true],
            ['name' => 'Translator', 'level' => self::LEVEL_AUTHOR, 'permit_submission' => true, 'permit_review' => true, 'permit_copyediting' => true, 'permit_production' => true, 'allow_registration' => true, 'show_contributor' => true, 'allow_submission' => true],
            ['name' => 'Reviewer', 'level' => self::LEVEL_REVIEWER, 'permit_submission' => false, 'permit_review' => true, 'permit_copyediting' => false, 'permit_production' => false, 'allow_registration' => true, 'show_contributor' => false, 'allow_submission' => false],

            // --- Level 6: Readers & Specialized ---
            ['name' => 'Reader', 'level' => self::LEVEL_READER, 'permit_submission' => false, 'permit_review' => false, 'permit_copyediting' => false, 'permit_production' => false, 'allow_registration' => true, 'show_contributor' => false, 'allow_submission' => false],
            ['name' => 'Subscription Manager', 'level' => self::LEVEL_READER, 'permit_submission' => false, 'permit_review' => false, 'permit_copyediting' => false, 'permit_production' => false, 'allow_registration' => false, 'show_contributor' => false, 'allow_submission' => false],
            ['name' => 'Site administrator', 'level' => self::LEVEL_ADMIN, 'permit_submission' => false, 'permit_review' => false, 'permit_copyediting' => false, 'permit_production' => false, 'allow_registration' => false, 'show_contributor' => false, 'allow_submission' => false],
        ];

        foreach ($defaultRoles as $roleData) {
            // Pastikan mengecek tanpa scope jurnal agar pencarian akurat saat proses seeding.
            $exists = static::withoutGlobalScope('journal')
                ->where('name', $roleData['name'])
                ->where('journal_id', $journal->id)
                ->exists();

            if (!$exists) {
                static::create([
                    'name' => $roleData['name'],
                    'guard_name' => 'web',
                    'permission_level' => $roleData['level'],
                    'permit_submission' => $roleData['permit_submission'],
                    'permit_review' => $roleData['permit_review'],
                    'permit_copyediting' => $roleData['permit_copyediting'],
                    'permit_production' => $roleData['permit_production'],
                    'allow_registration' => $roleData['allow_registration'],
                    'show_contributor' => $roleData['show_contributor'],
                    'allow_submission' => $roleData['allow_submission'],
                    'journal_id' => $journal->id,
                    'slug' => Str::slug($roleData['name'] . '-' . $journal->id),
                    'is_system' => true,
                ]);
            }
        }
    }
}