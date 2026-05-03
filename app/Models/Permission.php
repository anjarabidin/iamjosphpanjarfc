<?php

namespace App\Models;

// Gunakan trait HasUuids untuk Laravel 10+
use Illuminate\Database\Eloquent\Concerns\HasUuids;
// Perluas (extend) model asli dari Spatie
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    /**
     * CARA MODERN (LARAVEL 10+):
     * Cukup gunakan trait ini. Laravel akan otomatis mengatur
     * $incrementing = false dan $keyType = 'string'.
     */
    use HasUuids;

    /**
     * CARA MANUAL (LARAVEL 9 KE BAWAH):
     * Jika Anda menggunakan versi Laravel lama, hapus baris "use HasUuids;" di atas
     * dan hapus tanda komentar dari dua baris di bawah ini.
     */
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'guard_name',
        'label',
    ];
}
