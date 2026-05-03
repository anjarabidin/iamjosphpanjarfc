<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicationSetting extends Model
{
    use SoftDeletes, UuidTrait;

    protected $fillable = [
        'ad_art'
    ];

    /**
     * Get the full URL for AD/ART file
     */
    public function getAdArtUrlAttribute()
    {
        if ($this->ad_art) {
            return asset('storage/' . $this->ad_art);
        }
        return null;
    }

    /**
     * Get the file name for AD/ART
     */
    public function getAdArtFileNameAttribute()
    {
        if ($this->ad_art) {
            return basename($this->ad_art);
        }
        return null;
    }

    /**
     * Check if AD/ART file exists
     */
    public function hasAdArt()
    {
        return !empty($this->ad_art) && file_exists($this->ad_art);
    }

    /**
     * Get file size in human readable format
     */
    public function getAdArtFileSizeAttribute()
    {
        if ($this->ad_art && file_exists($this->ad_art)) {
            $bytes =  filesize($this->ad_art);
            return $this->formatBytes($bytes);
        }
        return null;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
