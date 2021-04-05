<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\TargetHour;

trait HasTargetHours
{

    public function targetHours()
    {
        return $this->hasMany(TargetHour::class);
    }

    public function getTargetHour($date) : TargetHour
    {
        return once(function () use ($date) {
            return $this->targetHours()->where('start_date', function ($query) use ($date) {
                return $query->select('start_date')
                    ->from('target_hours')
                    ->where('start_date','<=',$date)
                    ->where('user_id', $this->id)
                    ->orderBy('start_date','DESC')
                    ->groupBy('start_date')
                    ->limit(1);
            })->orderBy('start_date','ASC')->firstOrNew([]);
        });
    }

    public function targetHourDayForDate(Carbon $date)
    {
        return $this->getTargetHour($date)
            ->week_days
            ->getDayForDate($date);
    }

    public function targetHoursForDate(Carbon $date)
    {
        return $this->targetHourDayForDate($date)->hours;
    }
}
