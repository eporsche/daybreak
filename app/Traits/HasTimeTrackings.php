<?php

namespace App\Traits;

use App\Models\Location;
use App\Models\TimeTracking;

trait HasTimeTrackings
{
    public function timeTrackings()
    {
        return $this->hasMany(TimeTracking::class);
    }
}
