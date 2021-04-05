<?php

namespace App\Traits;

use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;

trait HasPeriod
{
    public function getPeriodAttribute()
    {
        return new CarbonPeriod(
            $this->starts_at,
            CarbonInterval::minutes(1),
            $this->ends_at
        );
    }
}
