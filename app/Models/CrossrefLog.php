<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrossrefLog extends Model
{
    use HasUuids, HasFactory;
    protected $fillable = [
        'journal_id',
        'submission_id',
        'status',
        'crossref_batch_id',
        'message',
    ];

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}
