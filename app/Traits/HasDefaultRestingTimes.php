<?php

namespace App\Traits;

use App\Models\DefaultRestingTime;
use App\Models\DefaultRestingTimeUser;

trait HasDefaultRestingTimes
{
    public function defaultRestingTimes()
    {
        return $this->belongsToMany(DefaultRestingTime::class, DefaultRestingTimeUser::class)
            ->withTimestamps()
            ->orderBy('min_hours','DESC')
            ->as('defaultRestingTimes');
    }
}
