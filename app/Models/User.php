<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasUuids, HasRoles, SoftDeletes;

    /**
     * Default guard for Spatie Permission
     */
    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'username',
        'given_name',
        'family_name',
        'email',
        'password',
        'affiliation',
        'country',
        'bio',
        'phone',
        'avatar',
        'orcid_id',
        'homepage',
        'mailing_address',
        'signature',
        'locale',
        'date_last_login',
        'date_registered',
        // Security: 'disabled', 'disabled_reason', 'must_change_password'
        // removed from $fillable — set these explicitly in admin-only methods
        'inline_help',
        'email_notifications',
        'privacy_consented_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
    }

    // =====================================================
    // RELATIONSHIPS
    // =====================================================

    /**
     * Get submissions authored by this user
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'user_id');
    }

    /**
     * Get review assignments for this user (as reviewer)
     */
    public function reviewAssignments(): HasMany
    {
        return $this->hasMany(ReviewAssignment::class, 'reviewer_id');
    }

    /**
     * Get uploaded files by this user
     */
    public function uploadedFiles(): HasMany
    {
        return $this->hasMany(SubmissionFile::class, 'uploaded_by');
    }

    /**
     * Get journal-specific role assignments for this user
     */
    public function journalRoles(): HasMany
    {
        return $this->hasMany(JournalUserRole::class, 'user_id');
    }

    /**
     * Get journals this user is registered with
     */
    public function registeredJournals()
    {
        return JournalUserRole::getUserJournals($this);
    }

    /**
     * Get user's roles in a specific journal
     */
    public function rolesInJournal($journal)
    {
        return JournalUserRole::getUserRolesInJournal($this, $journal);
    }

    /**
     * Check if user has a specific role in a journal
     */
    public function hasRoleInJournal($role, $journal): bool
    {
        return JournalUserRole::hasRole($this, $journal, $role);
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    /**
     * Cek Permission Level (Angka).
     * @param int|array $levels
     * @param string $journalId
     */
    public function hasJournalPermission($levels, $journalId)
    {
        // --- GATEKEEPER: Super Admin Bypass ---
        if ($this->hasRole(Role::ROLE_SUPERADMIN)) {
            return true;
        }

        $levels = is_array($levels) ? $levels : [$levels];

        return DB::table('journal_user_roles')
            ->join('roles', 'journal_user_roles.role_id', '=', 'roles.id')
            ->where('journal_user_roles.user_id', $this->id)
            ->where('journal_user_roles.journal_id', $journalId)
            ->whereIn('roles.permission_level', $levels)
            ->exists();
    }

    /**
     * Determine if the user can access a specific workflow stage in a journal.
     * 
     * @param string $stage ('submission', 'review', 'copyediting', 'production')
     * @param string|int $journalId
     * @param string|int|null $submissionId Optional to check specific assignment
     */
    public function canAccessStage(string $stage, $journalId, $submissionId = null): bool
    {
        // Manager & Editor bypass (level <= 2)
        if ($this->hasJournalPermission([Role::LEVEL_MANAGER, Role::LEVEL_EDITOR], $journalId)) {
            return true;
        }

        // Map stage names to the corresponding permit column in roles table
        $permitColumn = match ($stage) {
            'submission' => 'permit_submission',
            'review' => 'permit_review',
            'copyediting' => 'permit_copyediting',
            'production' => 'permit_production',
            default => null,
        };

        if (!$permitColumn) {
            throw new \InvalidArgumentException("Invalid stage: {$stage}");
        }

        // Check if user has a role with the specific permit in this journal
        $hasPermit = DB::table('journal_user_roles')
            ->join('roles', 'journal_user_roles.role_id', '=', 'roles.id')
            ->where('journal_user_roles.user_id', $this->id)
            ->where('journal_user_roles.journal_id', $journalId)
            ->where('roles.' . $permitColumn, true)
            ->exists();

        if ($hasPermit) {
            // If they have the permit, they need to be assigned to the submission
            // OR be the author (if it's their submission, they usually have limited access handled elsewhere,
            // but this checks generic stage access).
            if ($submissionId) {
                // Check if they are assigned as an editor/assistant
                $isAssigned = DB::table('editorial_assignments')
                    ->where('submission_id', $submissionId)
                    ->where('user_id', $this->id)
                    ->where('is_active', true)
                    ->exists();

                if ($isAssigned) {
                    return true;
                }

                // Check Author access
                if ($this->hasJournalRole(Role::ROLE_AUTHOR, $journalId)) {
                    $isAuthor = DB::table('submissions')
                        ->where('id', $submissionId)
                        ->where('user_id', $this->id)
                        ->exists();
                        
                    if ($isAuthor) {
                       // Authors can access stages that the submission is currently at or has passed
                       $submission = DB::table('submissions')->where('id', $submissionId)->first();
                       if ($submission) {
                           $stageMap = [
                               'submission' => 1,
                               'review' => 2,
                               'copyediting' => 3,
                               'production' => 4,
                           ];
                           
                           $requestedStageId = $stageMap[$stage] ?? 0;
                           if ($submission->stage_id >= $requestedStageId && $hasPermit) {
                               return true;
                           }
                       }
                    }
                }
                
                // Check Reviewer access (only for Review stage)
                if ($stage === 'review' && $this->hasJournalRole('Reviewer', $journalId)) {
                     $isReviewer = DB::table('review_assignments')
                        ->where('submission_id', $submissionId)
                        ->where('reviewer_id', $this->id)
                        ->exists();
                     if ($isReviewer) {
                         return true;
                     }
                }

                return false; // Has permit but not assigned to this submission
            } else {
                return true; // No submission ID provided, just checking generic capability
            }
        }

        return false;
    }

    /**
     * Smart Check (Bisa Terima Nama Role atau Level Permission).
     * @param string|int|array $roles
     * @param string $journalId
     */
    public function hasJournalRole($roles, $journalId)
    {
        // --- GATEKEEPER: Super Admin Bypass ---
        if ($this->hasRole(Role::ROLE_SUPERADMIN)) {
            return true;
        }

        // 1. Normalisasi String pipa "|"
        if (is_string($roles)) {
            $roles = explode('|', $roles);
        }
        
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        // 2. DETEKSI TIPE INPUT
        $firstItem = reset($roles);

        // Jika inputnya Angka, oper ke fungsi permission level
        if (is_numeric($firstItem)) {
            return $this->hasJournalPermission($roles, $journalId);
        }

        // 3. Jika inputnya String (Nama Role), jalankan query by NAME
        return DB::table('journal_user_roles')
            ->join('roles', 'journal_user_roles.role_id', '=', 'roles.id')
            ->where('journal_user_roles.user_id', $this->id)
            
            // PENTING: Sudah benar pakai tabel pivot untuk filter jurnal
            ->where('journal_user_roles.journal_id', $journalId)
            
            ->whereIn('roles.name', $roles)
            ->exists();
    }
    /**
     * Get the user's initials
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';

        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }

        return substr($initials, 0, 2);
    }

    /**
     * Get primary role name (legacy - for backward compatibility)
     */
    public function getPrimaryRoleAttribute(): string
    {
        return $this->primary_role_label;
    }

    /**
     * Get the highest priority role name for display.
     * 
     * This accessor implements smart role selection where higher privilege
     * roles take precedence. Reader is only shown if no other roles exist.
     */
    public function getPrimaryRoleLabelAttribute(): string
    {
        // 1. Handle Super Admin (Global priority)
        if ($this->hasRole(Role::ROLE_SUPERADMIN)) {
            return Role::ROLE_SUPERADMIN;
        }

        // 2. Find the highest priority role from journalRoles (smallest permission_level wins)
        $primaryJournalRole = DB::table('journal_user_roles')
            ->join('roles', 'journal_user_roles.role_id', '=', 'roles.id')
            ->where('journal_user_roles.user_id', $this->id)
            ->orderBy('roles.permission_level', 'asc')
            ->select('roles.name')
            ->first();

        if ($primaryJournalRole) {
            return $primaryJournalRole->name;
        }

        // Fallback: return first Spatie role if exists, otherwise 'Author'
        return $this->roles->first()?->name ?? 'Author';
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar) {
            return \Illuminate\Support\Facades\Storage::url($this->avatar);
        }
        return null;
    }

    public function submissionAuthors()
    {
        return $this->hasMany(SubmissionAuthor::class, 'user_id');
    }
}
