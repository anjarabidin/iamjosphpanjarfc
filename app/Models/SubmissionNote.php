<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionNote extends Model
{
    use \Illuminate\Database\Eloquent\Concerns\HasUuids;

    protected $fillable = [
        'submission_id',
        'user_id',
        'note',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
