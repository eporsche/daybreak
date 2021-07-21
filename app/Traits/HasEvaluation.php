<?php

namespace App\Traits;

use Brick\Math\BigDecimal;
use App\Models\TimeTracking;

trait HasEvaluation
{
    public function workingHoursForDate($date)
    {
        $working_hours = $this->timeTrackings()
            ->whereDate('starts_at',$date)
            ->get();

        return $working_hours->reduce(function (BigDecimal $total, TimeTracking $timeTracking) {
            return $total->plus($timeTracking->balance);
        }, BigDecimal::zero());
    }

    public function absentHoursForDate($date)
    {
        return $this->absenceIndex()->where('date',$date);
    }
}
