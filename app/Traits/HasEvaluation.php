<?php

namespace App\Traits;

use App\Models\TargetHour;

trait HasEvaluation
{
    public function workingHoursForDate($date)
    {
        $working_hours = $this->timeTrackings()
            ->whereDate('starts_at',$date)
            ->first();

        return optional($working_hours)->balance;
    }

    public function absentHoursForDate($date)
    {
        return $this->absenceIndex()->where('date',$date);
    }
}
