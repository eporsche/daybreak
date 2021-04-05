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

    public function timeTrackingsForLocation()
    {
        abort_if(!$this->currentLocation, 400, __('Location not set for user'));

        return $this->timeTrackings()
            ->where('location_id', $this->currentLocation->id)
            ->orderBy('ends_at', 'DESC');
    }
}
