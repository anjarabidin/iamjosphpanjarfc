<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use UuidTrait;

    protected $table = 'site_settings';

    protected $fillable = [
        'site_title',
        'site_intro',
        'about_content',
        'footer_content',
        'min_password_length',
        'redirect_to_journal',
        'wa_api_url',
        'wa_sender_number',
        'wa_device_id',
        'recaptcha_site_key',
        'recaptcha_secret_key',
    ];

    protected $casts = [
        'redirect_to_journal' => 'boolean',
        'min_password_length' => 'integer',
    ];
}
