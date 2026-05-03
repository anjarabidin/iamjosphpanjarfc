<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionIndexStat extends Model
{
    protected $fillable = [
        'journal_id',
        'submission_id',
        'is_monitored',
        'is_indexed',
        'last_check_status',
        'last_checked_at',
        'scholar_url',
    ];

    protected $casts = [
        'is_monitored' => 'boolean',
        'is_indexed' => 'boolean',
        'last_checked_at' => 'datetime',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}
