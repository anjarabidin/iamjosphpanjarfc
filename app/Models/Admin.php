<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\Publisher;
use App\Models\Author;
use App\Traits\UuidTrait;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    const MAX_LOGIN_ATTEMPTS = 3;
    const TYPE_ADMIN = 'ADMIN';
    const TYPE_PUBLISHER = 'PUBLISHER';
    const TYPE_AUTHOR = 'AUTHOR';
    const TYPE_ASSESSOR = 'ASSESSOR';
    
    use HasFactory, HasRoles, UuidTrait,  HasApiTokens;
    // use LogActivityTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'is_active',
        'role_id',
        'token',
        'login_attempts',
        'blocked_until',
        'type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */

    public function GetRoleNameAttribute()
    {
        return $this->roles()->first()->name;
    }

    /**
     * Memeriksa apakah akun sedang dalam status terblokir.
     */
    public function isBlocked(): bool
    {
        return $this->blocked_until && $this->blocked_until->isFuture();
    }

    /**
     * Mencatat percobaan login yang gagal dan memblokir jika perlu.
     */
    public function recordFailedLoginAttempt(): void
    {
        $this->login_attempts++;

        if ($this->login_attempts >= self::MAX_LOGIN_ATTEMPTS) {
            $this->blocked_until = Carbon::now()->addHours(2);
        }

        $this->save();
    }

    /**
     * Membersihkan data percobaan login dan status blokir setelah berhasil login.
     */
    public function clearLoginAttempts(): void
    {
        $this->login_attempts = 0;
        $this->blocked_until = null;
        $this->save();
    }

    /**
     * Get the publisher detail of this admin.
     */
    public function publisher(): HasOne
    {
        return $this->hasOne(Publisher::class);
    }

    /**
     * Get the author detail of this admin.
     */
    public function author(): HasOne
    {
        return $this->hasOne(Author::class);
    }
}
