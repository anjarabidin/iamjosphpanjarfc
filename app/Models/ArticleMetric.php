<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleMetric extends Model
{
    const TYPE_VIEW = 'view';
    const TYPE_DOWNLOAD = 'download';
    protected $table = 'article_metrics';
    protected $fillable = [
        'submission_id',
        'type',
        'ip_address',
        'country_code',
        'city',
        'date'
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class)->withTrashed();
    }
}
