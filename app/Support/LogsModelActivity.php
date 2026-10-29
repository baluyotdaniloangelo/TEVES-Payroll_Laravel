<?php

namespace App\Support;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait LogsModelActivity
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        $options = LogOptions::defaults()
            ->useLogName(static::$logName)
            ->logOnly(static::$logAttributes);

        if (static::$logOnlyDirty) {
            $options->logOnlyDirty();
        }

        return $options;
    }
}
