<?php

namespace App\Traits;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


trait LogActivityTrait
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('system')
            ->setDescriptionForEvent(fn (string $eventName) => "Data from table " . (new static)->getTable() . " {$this->name}  has been {$eventName}")
            ->logFillable();
    }
}
